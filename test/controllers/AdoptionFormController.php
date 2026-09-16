<?php
require_once '../models/AdoptionForm.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_id']) && isset($_POST['status'])) {
    header('Content-Type: application/json');
    $controller = new AdoptionFormController();
    $result = $controller->updateStatus($_POST['form_id'], $_POST['status']);
    echo json_encode($result);
    exit;
}

class AdoptionFormController {
    private $model;

    public function __construct() {
        $this->model = new AdoptionForm();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
    }

    public function processForm($postData, $petId, $userId) {
        try {
            $formData = array_merge($postData, [
                'pet_id' => $petId,
                'user_id' => $userId
            ]);

            $this->model->submitForm($formData);
            return ['success' => true];
        } catch (Exception $e) {
            error_log("Error in processForm: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }    public function validateForm($data) {
        if (!is_array($data)) {
            return ['Please provide valid form data'];
        }

        $errors = [];
        $requiredFields = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone' => 'Phone Number',
            'street_address' => 'Street Address',
            'city' => 'City',
            'country' => 'Country',
            'postal_code' => 'Postal Code',
            'pet_name' => 'Pet Name',
            'yard' => 'Yard Information',
            'housing' => 'Housing Status',
            'adoption_reason' => 'Adoption Reason'
        ];

        foreach ($requiredFields as $field => $label) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[] = "Please provide your $label";
            }
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please provide a valid email address";
        }

        if (!empty($data['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $data['phone']);
            if (strlen($phone) < 7 || strlen($phone) > 15) {
                $errors[] = "Please provide a valid phone number";
            }
        }

        return $errors;
    }

    public function checkExistingSubmission($petId) {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User must be logged in to check submission');
            }

            $userId = $_SESSION['user_id'];
            $exists = $this->model->hasExistingSubmission($userId, $petId);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'exists' => $exists
            ]);
        } catch (Exception $e) {
            error_log("Error in checkExistingSubmission: " . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateStatus($formId, $status) {
        try {
            if (empty($formId) || empty($status)) {
                return [
                    'success' => false,
                    'message' => 'Missing required parameters'
                ];
            }

            $formId = (int)$formId;
            if (!in_array($status, ['approved', 'rejected'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid status'
                ];
            }

            $result = $this->model->updateStatus($formId, $status);
            
            if ($result) {
                if ($status === 'approved') {
                    $adopter = $this->model->getAdopterDetails($formId);
                    if ($adopter && !empty($adopter['EMAIL'])) {
                        $to = $adopter['EMAIL'];
                        $subject = 'Congratulations! Your Adoption Request Has Been Approved';
                        $petName = $adopter['PET_NAME_DESIRED'];
                        $firstName = $adopter['FIRST_NAME'];
                        $message = "Hello $firstName,\n\nCongratulations! Your request to adopt '$petName' has been approved. The pet will soon be part of your family!\n\nThank you for choosing adoption.\n\nBest regards,\nPow Team";
                        $headers = 'From: no-reply@pow-adopt.com' . "\r\n" .
                            'Reply-To: no-reply@pow-adopt.com' . "\r\n" .
                            'X-Mailer: PHP/' . phpversion();
                        @mail($to, $subject, $message, $headers);
                    }
                }
                return [
                    'success' => true,
                    'message' => 'Status updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update status'
                ];
            }
        } catch (Exception $e) {
            error_log("Error in updateStatus: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}