<?php
require_once '../config/database.php';

class AdoptionForm {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function submitForm($data) {
        try {
            $query = "INSERT INTO adoption_form (
                pet_id, user_id, first_name, last_name, email, phone, 
                street_address, city, country, postal_code,
                pet_name_desired, has_yard, housing_status, has_pet_permission,
                has_children, children_ages,
                has_other_pets, other_pets_description,
                hours_alone_per_day, reason_for_alone_time, adoption_reason,
                form_submitted_date
            ) VALUES (
                :pet_id, :user_id, :first_name, :last_name, :email, :phone,
                :street_address, :city, :country, :postal_code,
                :pet_name_desired, :has_yard, :housing_status, :has_pet_permission,
                :has_children, :children_ages,
                :has_other_pets, :other_pets_description,
                :hours_alone_per_day, :reason_for_alone_time, :adoption_reason,
                SYSDATE
            )";

            $stmt = oci_parse($this->conn, $query);
            
            $has_yard = (isset($data['yard']) && $data['yard'] === 'yes') ? 1 : 0;
            $has_pet_permission = (isset($data['landlord_permission']) && $data['landlord_permission'] === 'yes') ? 1 : 0;
            $has_children = (isset($data['children']) && $data['children'] === 'yes') ? 1 : 0;
            $has_other_pets = (isset($data['current_pets']) && $data['current_pets'] === 'yes') ? 1 : 0;
            
            $hours_alone = 0;
            if (preg_match('/(\d+)/', $data['pet_alone_time'], $matches)) {
                $hours_alone = intval($matches[1]);
            }

            oci_bind_by_name($stmt, ":pet_id", $data['pet_id']);
            oci_bind_by_name($stmt, ":user_id", $data['user_id']);
            oci_bind_by_name($stmt, ":first_name", $data['first_name']);
            oci_bind_by_name($stmt, ":last_name", $data['last_name']);
            oci_bind_by_name($stmt, ":email", $data['email']);
            oci_bind_by_name($stmt, ":phone", $data['phone']);
            oci_bind_by_name($stmt, ":street_address", $data['street_address']);
            oci_bind_by_name($stmt, ":city", $data['city']);
            oci_bind_by_name($stmt, ":country", $data['country']);
            oci_bind_by_name($stmt, ":postal_code", $data['postal_code']);
            oci_bind_by_name($stmt, ":pet_name_desired", $data['pet_name']);
            oci_bind_by_name($stmt, ":has_yard", $has_yard);
            oci_bind_by_name($stmt, ":housing_status", $data['housing']);
            oci_bind_by_name($stmt, ":has_pet_permission", $has_pet_permission);
            oci_bind_by_name($stmt, ":has_children", $has_children);
            oci_bind_by_name($stmt, ":children_ages", $data['children_details']);
            oci_bind_by_name($stmt, ":has_other_pets", $has_other_pets);
            oci_bind_by_name($stmt, ":other_pets_description", $data['pet_details']);
            oci_bind_by_name($stmt, ":hours_alone_per_day", $hours_alone);
            oci_bind_by_name($stmt, ":reason_for_alone_time", $data['pet_alone_time']);
            oci_bind_by_name($stmt, ":adoption_reason", $data['adoption_reason']);

            $execute = oci_execute($stmt);
            if (!$execute) {
                $e = oci_error($stmt);
                throw new Exception("Error saving form: " . $e['message']);
            }

            oci_commit($this->conn);
            return true;

        } catch (Exception $e) {
            if (isset($stmt)) {
                oci_rollback($this->conn);
            }
            throw new Exception("Error submitting form: " . $e->getMessage());
        } finally {
            if (isset($stmt)) {
                oci_free_statement($stmt);
            }
        }
    }

    public function updateStatus($formId, $status) {
        try {
            $query = "UPDATE adoption_form SET status = :status WHERE id = :form_id";
            $stmt = oci_parse($this->conn, $query);
            
            oci_bind_by_name($stmt, ":status", $status);
            oci_bind_by_name($stmt, ":form_id", $formId);
            
            if (!oci_execute($stmt)) {
                $e = oci_error($stmt);
                throw new Exception($e['message']);
            }
            
            if ($status === 'approved') {
                $query = "UPDATE pets p 
                         SET p.available_for_adoption = 0 
                         WHERE p.id = (
                             SELECT pet_id 
                             FROM adoption_form 
                             WHERE id = :form_id
                         )";
                $stmt = oci_parse($this->conn, $query);
                oci_bind_by_name($stmt, ":form_id", $formId);
                
                if (!oci_execute($stmt)) {
                    $e = oci_error($stmt);
                    throw new Exception($e['message']);
                }
            }
            
            oci_commit($this->conn);
            return true;
            
        } catch (Exception $e) {
            oci_rollback($this->conn);
            error_log("Error updating adoption status: " . $e->getMessage());
            return false;
        } finally {
            if (isset($stmt)) {
                oci_free_statement($stmt);
            }
        }
    }

    public function hasExistingSubmission($userId, $petId) {
        try {
            $query = "SELECT COUNT(*) AS count FROM adoption_form WHERE user_id = :user_id AND pet_id = :pet_id";
            $stmt = oci_parse($this->conn, $query);
            
            oci_bind_by_name($stmt, ":user_id", $userId);
            oci_bind_by_name($stmt, ":pet_id", $petId);
            
            if (!oci_execute($stmt)) {
                $e = oci_error($stmt);
                throw new Exception($e['message']);
            }
            
            $row = oci_fetch_assoc($stmt);
            return ($row && $row['COUNT'] > 0);
            
        } catch (Exception $e) {
            error_log("Error checking existing submission: " . $e->getMessage());
            throw $e;
        } finally {
            if (isset($stmt)) {
                oci_free_statement($stmt);
            }
        }
    }

    public function getAdopterDetails($formId) {
        try {
            $query = "SELECT email as EMAIL, first_name as FIRST_NAME, last_name as LAST_NAME, pet_name_desired as PET_NAME_DESIRED FROM adoption_form WHERE id = :form_id";
            $stmt = oci_parse($this->conn, $query);
            
            oci_bind_by_name($stmt, ":form_id", $formId);
            
            if (!oci_execute($stmt)) {
                throw new Exception(oci_error($stmt)['message']);
            }
            
            $row = oci_fetch_assoc($stmt);
            return $row ?: null;
            
        } catch (Exception $e) {
            error_log("Error getting adopter details: " . $e->getMessage());
            return null;
        } finally {
            if (isset($stmt)) {
                oci_free_statement($stmt);
            }
        }
    }

    public function __destruct() {
        if ($this->conn) {
            oci_close($this->conn);
        }
    }
}