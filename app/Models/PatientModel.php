<?php
/**
 * Created by PhpStorm.
 * User: Del
 * Date: 11/05/2021
 * Time: 2:21 PM
 */

namespace App\Models;
use CodeIgniter\Model;
use Exception;

class PatientModel extends Model
{
    protected $table='paciente';
    protected $allowedFields = [
        'edad',
        'sexo',
        'peso',
        'estatura',
        'direccionParticular',
        'prediagnostico',
        'identificacionMed',
        'identificacionCI',
        'idtelefonoMovil',
        'usuario',
        'contrasenna',
    ];
    protected $updatedField = 'updated_at';


    public function findPatientById($id_usuario)
    {
        $paciente = $this
//            ->select(["id", "identificacionCI", "contrasenna"])
            ->asArray()
            ->where(['id' => $id_usuario])
            ->first();
        if (!$paciente) {
            throw new Exception('Could not find client for specified ID');
        }
        return $paciente;
    }

    public function findPatientByPhoneNumberID($idtelefonoMovil)
    {
        $paciente = $this
//            ->select(["id", "identificacionCI", "contrasenna"])
            ->asArray()
            ->where(['idtelefonoMovil' => $idtelefonoMovil])
            ->first();
        if (!$paciente) {
            throw new Exception('Could not find client for specified ID del telefono movil');
        }
        return $paciente;
    }
}