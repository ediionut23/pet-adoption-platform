<?php
require_once '../models/AdoptionRequest.php';

class AdoptionRequestController {
    private $model;

    public function __construct() {
        $this->model = new AdoptionRequest();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        try {
            $requests = $this->model->getAdoptionRequests($_SESSION['user_id']);
            return $requests;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getImagePath($petImage, $species) {
        if ($petImage !== null) {
            return $petImage;
        }
        return 'stiluri/imagini/' . strtolower($species) . '.png';
    }
} 