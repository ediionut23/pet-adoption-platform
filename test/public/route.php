<?php
    require_once '../controller/AuthController.php';

    $controller = new AuthController();
    $action = $_GET['action'] ?? '';

    switch($action){
        case 'register':
            $controller->register($_POST);
            break;
        case 'login':
            $controller->login($_POST);
            break;
        default:
            echo "Actiune necunoscuta.";
    }
