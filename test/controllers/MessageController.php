<?php
require_once '../models/Message.php';
require_once '../models/Pet.php';
require_once '../config/database.php';

class MessageController {
    private $messageModel;
    private $petModel;
    
    public function __construct() {
        $this->messageModel = new Message();
        $this->petModel = new Pet(getConnection());
    }
    
    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['error' => 'Invalid request method'];
        }
        
        $senderId = $_SESSION['user_id'] ?? null;
        $receiverId = $_POST['receiver_id'] ?? null;
        $message = $_POST['message'] ?? null;
        $petId = $_POST['pet_id'] ?? null;
        
        if (!$senderId || !$receiverId || !$message) {
            return ['error' => 'Missing required fields'];
        }
        
        try {
            $this->messageModel->createMessage($senderId, $receiverId, $message, $petId);
            return ['success' => true];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function getConversations() {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                return ['error' => 'User not authenticated'];
            }
            
            $conversations = $this->messageModel->getConversations($userId);
            return ['success' => true, 'conversations' => $conversations];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function getMessages($conversationId = null) {
        try {
            if (!$conversationId && isset($_GET['conversation_id'])) {
                $conversationId = $_GET['conversation_id'];
            }
            
            if (!$conversationId) {
                return ['error' => 'Conversation ID is required'];
            }
            
            $messages = $this->messageModel->getMessages($conversationId);
            
            $userId = $_SESSION['user_id'] ?? null;
            if ($userId) {
                $this->messageModel->markAsRead($conversationId, $userId);
            }
            
            return ['success' => true, 'messages' => $messages];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function startConversation($petId = null) {
        try {
            if (!$petId && isset($_GET['pet_id'])) {
                $petId = $_GET['pet_id'];
            }
            
            if (!$petId) {
                return ['error' => 'Pet ID is required'];
            }
            
            $pet = $this->petModel->getPetById($petId);
            if (!$pet) {
                return ['error' => 'Pet not found'];
            }
            
            return [
                'success' => true,
                'pet' => $pet
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
} 