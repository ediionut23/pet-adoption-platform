<?php

require_once dirname(dirname(__FILE__)) . '/config/database.php';

class NotificationController {
    private $conn;
    

    public function __construct() {
        try {
            $this->conn = getConnection();
        } catch (Exception $e) {
            error_log("Database connection error in NotificationController: " . $e->getMessage());
        }
    }
    

    public function __destruct() {
        if (isset($this->conn) && $this->conn) {
            oci_close($this->conn);
        }
    }
    

    public function getNotificationsData() {
        if (!isset($_SESSION['user_id'])) {
            return [
                'success' => false,
                'error' => 'User not authenticated'
            ];
        }

        try {
            $query = "SELECT n.*, 
                            p.name as pet_name, 
                            p.species as pet_species
                     FROM user_notifications n
                     LEFT JOIN pets p ON n.related_pet_id = p.id
                     WHERE n.user_id = :user_id
                     ORDER BY n.created_at DESC";

            $stmt = oci_parse($this->conn, $query);
            oci_bind_by_name($stmt, ":user_id", $_SESSION['user_id']);
            oci_execute($stmt);

            $notifications = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $notifications[] = $row;
            }

            return [
                'success' => true,
                'notifications' => $notifications
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }


    public function getUnreadCountData() {
        if (!isset($_SESSION['user_id'])) {
            return 0;
        }

        try {
            $query = "SELECT COUNT(*) as count 
                     FROM user_notifications 
                     WHERE user_id = :user_id 
                     AND is_read = 0";

            $stmt = oci_parse($this->conn, $query);
            oci_bind_by_name($stmt, ":user_id", $_SESSION['user_id']);
            oci_execute($stmt);

            $row = oci_fetch_assoc($stmt);
            return $row['COUNT'];

        } catch (Exception $e) {
            return 0;
        }
    }


    public function markAsReadData($notification_id) {
        try {
            $query = "UPDATE user_notifications 
                     SET is_read = 1 
                     WHERE id = :notification_id 
                     AND user_id = :user_id";

            $stmt = oci_parse($this->conn, $query);
            oci_bind_by_name($stmt, ":notification_id", $notification_id);
            oci_bind_by_name($stmt, ":user_id", $_SESSION['user_id']);
            
            if (oci_execute($stmt)) {
                return ['success' => true];
            }
            
            return ['success' => false, 'error' => 'Could not mark notification as read'];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    public function markAllAsReadData() {
        try {
            $query = "UPDATE user_notifications 
                     SET is_read = 1 
                     WHERE user_id = :user_id";

            $stmt = oci_parse($this->conn, $query);
            oci_bind_by_name($stmt, ":user_id", $_SESSION['user_id']);
            
            if (oci_execute($stmt)) {
                return ['success' => true];
            }
            
            return ['success' => false, 'error' => 'Could not mark notifications as read'];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    

    private function sendJsonResponse($success, $message = null, $data = []) {
        if (ob_get_level()) {
            ob_clean();
        }
        
        $response = ['success' => $success];
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        if (!empty($data)) {
            $data = $this->sanitizeForJson($data);
            $response = array_merge($response, $data);
        }
        
        try {
            $jsonOutput = json_encode($response);
            
            if ($jsonOutput === false) {
                $jsonError = json_last_error_msg();
                error_log("JSON encoding error: " . $jsonError);
                
                $fallbackResponse = json_encode([
                    'success' => false,
                    'message' => 'Error encoding response: ' . $jsonError
                ]);
                
                header('Content-Type: application/json');
                echo $fallbackResponse;
            } else {
                header('Content-Type: application/json');
                echo $jsonOutput;
            }
        } catch (Exception $e) {
            error_log("Exception during JSON response: " . $e->getMessage());
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while sending the response.'
            ]);
        }
        
        exit;
    }

    private function sanitizeForJson($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeForJson($value);
            }
            return $data;
        } elseif (is_object($data)) {
            $vars = get_object_vars($data);
            foreach ($vars as $key => $value) {
                $data->$key = $this->sanitizeForJson($value);
            }
            return $data;
        } else {
            return is_string($data) ? mb_convert_encoding($data, 'UTF-8', 'UTF-8') : $data;
        }
    }
    

    public function getNotifications() {
        try {
            if (!isset($_SESSION['user_id'])) {
                return $this->sendJsonResponse(false, 'User not authenticated');
            }
            
            $result = $this->getNotificationsData();
            
            if (isset($result['success']) && $result['success'] && isset($result['notifications'])) {
                return $this->sendJsonResponse(true, 'Notifications retrieved successfully', [
                    'notifications' => $result['notifications']
                ]);
            } else {
                return $this->sendJsonResponse(false, 'Failed to retrieve notifications', []);
            }
            
        } catch (Exception $e) {
            error_log("Error in getNotifications: " . $e->getMessage());
            return $this->sendJsonResponse(false, 'An error occurred while retrieving notifications');
        }
    }
    

    public function markAsRead() {
        try {
            if (!isset($_SESSION['user_id'])) {
                return $this->sendJsonResponse(false, 'User not authenticated');
            }
            
            $requestData = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $notificationId = $requestData['notification_id'] ?? null;
            
            if (!$notificationId) {
                return $this->sendJsonResponse(false, 'Invalid notification ID');
            }
            
            $result = $this->markAsReadData($notificationId);
            
            if ($result['success']) {
                return $this->sendJsonResponse(true, 'Notification marked as read');
            } else {
                return $this->sendJsonResponse(false, 'Failed to mark notification as read');
            }
            
        } catch (Exception $e) {
            error_log("Error in markAsRead: " . $e->getMessage());
            return $this->sendJsonResponse(false, 'An error occurred while marking notification as read');
        }
    }
    

    public function markAllAsRead() {
        try {
            if (!isset($_SESSION['user_id'])) {
                return $this->sendJsonResponse(false, 'User not authenticated');
            }
            
            $result = $this->markAllAsReadData();
            
            if ($result['success']) {
                return $this->sendJsonResponse(true, 'All notifications marked as read');
            } else {
                return $this->sendJsonResponse(false, 'Failed to mark all notifications as read');
            }
            
        } catch (Exception $e) {
            error_log("Error in markAllAsRead: " . $e->getMessage());
            return $this->sendJsonResponse(false, 'An error occurred while marking all notifications as read');
        }
    }
    

    public function getUnreadCount() {
        try {
            if (!isset($_SESSION['user_id'])) {
                return $this->sendJsonResponse(false, 'User not authenticated');
            }
            
            $count = $this->getUnreadCountData();
            
            return $this->sendJsonResponse(true, 'Unread notification count retrieved successfully', [
                'count' => $count
            ]);
            
        } catch (Exception $e) {
            error_log("Error in getUnreadCount: " . $e->getMessage());
            return $this->sendJsonResponse(false, 'An error occurred while retrieving unread notification count');
        }
    }
}
