<?php
require_once '../models/AdoptionStatus.php';

class AdoptionStatusController {
    private $model;

    public function __construct() {
        $this->model = new AdoptionStatus();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        try {
            $requests = $this->model->getUserAdoptionStatus($_SESSION['user_id']);
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

    public function formatDate($dateString) {
        $date = new DateTime($dateString);
        return $date->format('F j, Y');
    }

    public function getStatusClass($status) {
        return 'status-' . strtolower($status);
    }
} 