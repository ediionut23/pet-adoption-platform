<?php
require_once '../config/database.php';

class Message {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function createMessage($senderId, $receiverId, $message, $petId = null) {
        try {
            $conversationId = $this->getConversationId($senderId, $receiverId, $petId);
            
            $query = "INSERT INTO messages (conversation_id, sender_id, receiver_id, message_text, pet_id) 
                     VALUES (:conversation_id, :sender_id, :receiver_id, EMPTY_CLOB(), :pet_id) 
                     RETURNING message_text INTO :message_text";
            
            $stmt = oci_parse($this->conn, $query);
            
            $clob = oci_new_descriptor($this->conn, OCI_D_LOB);
            
            oci_bind_by_name($stmt, ":conversation_id", $conversationId);
            oci_bind_by_name($stmt, ":sender_id", $senderId);
            oci_bind_by_name($stmt, ":receiver_id", $receiverId);
            oci_bind_by_name($stmt, ":pet_id", $petId);
            oci_bind_by_name($stmt, ":message_text", $clob, -1, SQLT_CLOB);
            
            $result = oci_execute($stmt, OCI_DEFAULT);
            
            if (!$result) {
                throw new Exception("Failed to create message");
            }
            
            if (!$clob->save($message)) {
                oci_rollback($this->conn);
                throw new Exception("Failed to save message content");
            }
            
            oci_commit($this->conn);
            $clob->free();
            
            return true;
        } catch (Exception $e) {
            throw new Exception("Error creating message: " . $e->getMessage());
        }
    }
    
    private function getConversationId($senderId, $receiverId, $petId) {
        $query = "SELECT DISTINCT conversation_id 
                 FROM messages 
                 WHERE ((sender_id = :user1 AND receiver_id = :user2) 
                    OR (sender_id = :user2 AND receiver_id = :user1))
                    AND pet_id = :pet_id";
        
        $stmt = oci_parse($this->conn, $query);
        
        oci_bind_by_name($stmt, ":user1", $senderId);
        oci_bind_by_name($stmt, ":user2", $receiverId);
        oci_bind_by_name($stmt, ":pet_id", $petId);
        
        oci_execute($stmt);
        
        $row = oci_fetch_assoc($stmt);
        
        if ($row) {
            return $row['CONVERSATION_ID'];
        }
        
        $query = "SELECT MAX(conversation_id) as max_id FROM messages";
        $stmt = oci_parse($this->conn, $query);
        oci_execute($stmt);
        $row = oci_fetch_assoc($stmt);
        
        return ($row['MAX_ID'] ?? 0) + 1;
    }
    
    public function getConversations($userId) {
        $query = "SELECT DISTINCT 
                    m.conversation_id,
                    m.pet_id,
                    p.name as pet_name,
                    CASE 
                        WHEN m.sender_id = :user_id THEN m.receiver_id
                        ELSE m.sender_id
                    END as other_user_id,
                    u.name as other_user_name,
                    u.surname as other_user_surname,
                    (SELECT DBMS_LOB.SUBSTR(message_text, 4000, 1) 
                     FROM (
                         SELECT message_text
                         FROM messages m2 
                         WHERE m2.conversation_id = m.conversation_id 
                         ORDER BY m2.created_at DESC
                     ) WHERE ROWNUM = 1) as last_message,
                    (SELECT created_at 
                     FROM (
                         SELECT created_at
                         FROM messages m2 
                         WHERE m2.conversation_id = m.conversation_id 
                         ORDER BY m2.created_at DESC
                     ) WHERE ROWNUM = 1) as last_message_date,
                    (SELECT COUNT(*) 
                     FROM messages m3 
                     WHERE m3.conversation_id = m.conversation_id 
                     AND m3.receiver_id = :user_id 
                     AND m3.read_at IS NULL) as unread_count
                FROM messages m
                JOIN users u ON (
                    CASE 
                        WHEN m.sender_id = :user_id THEN m.receiver_id
                        ELSE m.sender_id
                    END = u.id
                )
                LEFT JOIN pets p ON m.pet_id = p.id
                WHERE m.sender_id = :user_id OR m.receiver_id = :user_id
                ORDER BY last_message_date DESC";
        
        $stmt = oci_parse($this->conn, $query);
        oci_bind_by_name($stmt, ":user_id", $userId);
        oci_execute($stmt);
        
        $conversations = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $conversations[] = $row;
        }
        
        return $conversations;
    }
    
    public function getMessages($conversationId) {
        $query = "SELECT 
                    m.id,
                    m.conversation_id,
                    m.sender_id,
                    m.receiver_id,
                    m.created_at,
                    m.read_at,
                    m.pet_id,
                    DBMS_LOB.SUBSTR(m.message_text, 4000, 1) as message_text,
                    u_sender.name as sender_name, 
                    u_sender.surname as sender_surname,
                    u_receiver.name as receiver_name, 
                    u_receiver.surname as receiver_surname
                 FROM messages m
                 JOIN users u_sender ON m.sender_id = u_sender.id
                 JOIN users u_receiver ON m.receiver_id = u_receiver.id
                 WHERE m.conversation_id = :conversation_id
                 ORDER BY m.created_at ASC";
        
        $stmt = oci_parse($this->conn, $query);
        oci_bind_by_name($stmt, ":conversation_id", $conversationId);
        oci_execute($stmt);
        
        $messages = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $messages[] = $row;
        }
        
        return $messages;
    }
    
    public function markAsRead($conversationId, $userId) {
        $query = "UPDATE messages 
                 SET read_at = CURRENT_TIMESTAMP 
                 WHERE conversation_id = :conversation_id 
                 AND receiver_id = :user_id 
                 AND read_at IS NULL";
        
        $stmt = oci_parse($this->conn, $query);
        oci_bind_by_name($stmt, ":conversation_id", $conversationId);
        oci_bind_by_name($stmt, ":user_id", $userId);
        
        return oci_execute($stmt);
    }
} 