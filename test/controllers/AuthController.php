<?php
    require_once __DIR__. '/../models/User.php';
    require_once __DIR__. '/../utils/JWTManager.php';
    
    error_reporting(0);
    ini_set('display_errors', 0);
    
    class AuthController {
        private function validateRegistration($data) {
            $errors = [];
            if (empty($data['name']) || strlen($data['name']) < 2) {
                $errors[] = "Name must be at least 2 characters long";
            }
            
            if (empty($data['surname']) || strlen($data['surname']) < 2) {
                $errors[] = "Surname must be at least 2 characters long";
            }
            
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Please enter a valid email address";
            }
            
            if (empty($data['password']) || strlen($data['password']) < 6) {
                $errors[] = "Password must be at least 6 characters long";
            }
            
            if ($data['password'] !== $data['confirm_password']) {
                $errors[] = "Passwords do not match";
            }
            
            return $errors;
        }

        public function register($data) {
            if (headers_sent($filename, $linenum)) {
                error_log("Headers already sent in $filename on line $linenum");
                echo json_encode([
                    'success' => false,
                    'errors' => ['Internal server error. Please try again.']
                ]);
                exit();
            }

            header('Content-Type: application/json');
            header('Cache-Control: no-cache, must-revalidate');
            
            try {
                if (!isset($data['name']) || !isset($data['surname']) || !isset($data['email']) || !isset($data['password'])) {
                    error_log("Missing required fields in registration");
                    echo json_encode([
                        'success' => false,
                        'errors' => ["All fields are required"]
                    ]);
                    exit();
                }

                $errors = $this->validateRegistration($data);
                
                if (!empty($errors)) {
                    error_log("Validation errors in registration: " . json_encode($errors));
                    echo json_encode(['success' => false, 'errors' => $errors]);
                    exit();
                }

                try {
                    $user = new User();
                    $result = $user->createUser(
                        trim($data['name']),
                        trim($data['surname']),
                        trim($data['email']),
                        $data['password']
                    );
                    
                    if($result) {
                        echo json_encode(['success' => true, 'message' => 'Registration successful']);
                    } else {
                        error_log("User creation failed without exception");
                        echo json_encode([
                            'success' => false, 
                            'errors' => ["Failed to create account. Please try again."]
                        ]);
                    }                } catch (Exception $e) {
                    error_log("Database error during registration: " . $e->getMessage());
                    $errorMessage = $e->getMessage();
                    if (strpos($errorMessage, "This email address is already registered") !== false) {
                        echo json_encode([
                            'success' => false,
                            'errors' => ["This email address is already registered"]
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'errors' => ["Database error. Please try again later."]
                        ]);
                    }
                }
            } catch (Exception $e) {
                error_log("Unexpected error during registration: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'errors' => ["An unexpected error occurred. Please try again later."]
                ]);
            }
            exit();
        }

        public function login($data) {
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, must-revalidate');
            
            try {
                if (empty($data['name']) || empty($data['surname']) || empty($data['password'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'All fields are required'
                    ]);
                    exit();
                }

                $user = new User();
                $authenticated = $user->authenticate($data['name'], $data['surname'], $data['password']);

                if($authenticated) {
                    $userData = (object)[
                        'id' => $authenticated['ID'],
                        'name' => $data['name'],
                        'surname' => $data['surname'],
                        'is_admin' => $authenticated['IS_ADMIN'] ?? false
                    ];
                    
                    $token = JWTManager::generateToken($userData);
                    setcookie('jwt_token', $token, time() + 3600, '/', '', false, true);
                    
                    echo json_encode([
                        'success' => true,
                        'redirect' => 'homepage.php',
                        'token' => $token
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid username or password'
                    ]);
                }
            } catch (Exception $e) {
                error_log("Login error: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => 'Server error. Please try again later.'
                ]);
            }
            exit();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new AuthController();
        
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'register':
                    $controller->register($_POST);
                    break;
                case 'login':
                    $controller->login($_POST);
                    break;
                default:
                    echo json_encode([
                        'success' => false,
                        'errors' => ['Invalid action']
                    ]);
                    exit();
            }
        } else {
            echo json_encode([
                'success' => false,
                'errors' => ['No action specified']
            ]);
            exit();
        }
    } 
?> 