<?php

namespace App\Validation;

use App\Models\PatientModel;
use Exception;

class UserRules
{
    public function validateUser(string $str, string $fields, array $data): bool
    {
        try {
            $model = new PatientModel();
            $patient = $model->findPatientById($data['user']);
            return password_verify($data['password'], $patient['contrasenna']);
        } catch (Exception $e) {
            return false;
        }
    }
}
