<?php
/**
 * Created by PhpStorm.
 * User: Del
 * Date: 17/05/2021
 * Time: 11:36 AM
 */

namespace App\Models;

use CodeIgniter\Model;

class RegisterModel extends Model
{
    protected $table='registro';
    protected $primaryKey ='identificacionRegistro';
    protected $returnType ='array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'diagnosticado',
        'registro_Diagnostico',
        'nombreRegistro',
        'fecha',
        'fechaprueba',
        'hora',
        'FrecuenciaCardiaca',
        'FrecuenciaCardiacaMax',
        'FrecuenciaCardiacaMin',
        'idPaciente',
        'registroecg',
        'RR',
        'revisar'
    ];
}