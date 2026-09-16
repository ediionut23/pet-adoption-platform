<?php
require_once '../models/User.php';

class UserController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function getUserProfile($userId) {
        try {
            $userData = $this->userModel->getUserById($userId);
            $adoptedPets = $this->userModel->getApprovedPets($userId);
            
            if (!$userData) {
                return ['error' => 'User not found'];
            }
            
            return [
                'user' => $userData,
                'adoptedPets' => $adoptedPets
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function updateProfile($userId, $data) {
        try {
            if (empty($data['name']) || empty($data['surname']) || empty($data['email'])) {
                return ['error' => 'Name, surname and email are required'];
            }
            
            if (!empty($data['new_password'])) {
                if (empty($data['current_password'])) {
                    return ['error' => 'Current password is required to set a new password'];
                }
                
                if (!$this->userModel->verifyPassword($userId, $data['current_password'])) {
                    return ['error' => 'Current password is incorrect'];
                }
            }
            
            $updateResult = $this->userModel->updateUser($userId, $data);
            
            if ($updateResult) {
                return ['success' => 'Profile updated successfully'];
            } else {
                return ['error' => 'Failed to update profile'];
            }
            
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }    public function register($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = "Name is required";
        }
        if (empty($data['surname'])) {
            $errors[] = "Surname is required";
        }
        if (empty($data['email'])) {
            $errors[] = "Email is required";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        if (empty($data['password'])) {
            $errors[] = "Password is required";
        }
        if (empty($data['confirm_password'])) {
            $errors[] = "Password confirmation is required";
        }
        if (!empty($data['password']) && !empty($data['confirm_password']) && 
            $data['password'] !== $data['confirm_password']) {
            $errors[] = "Passwords do not match";
        }
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        try {
            $result = $this->userModel->createUser(
                $data['name'],
                $data['surname'],
                $data['email'],
                $data['password']
            );
            
            return [
                'success' => true,
                'message' => 'Registration successful'
            ];
            
        } catch (Exception $e) {
            $message = $e->getMessage();
            if (strpos($message, "email address is already registered") !== false) {
                return [
                    'success' => false,
                    'errors' => ['This email address is already registered']
                ];
            }
            
            error_log("Registration error: " . $e->getMessage());
            return [
                'success' => false,
                'errors' => ['Could not complete registration. Please try again.']
            ];
        }
    }
}