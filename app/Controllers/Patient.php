<?php

namespace App\Controllers;

use App\Models\PatientModel;
use App\Models\RegisterModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Files\UploadedFile;
use Exception;

class Patient extends BaseController
{
    /**
     * Get all Patients
     * @return Response
     */
    public function index()
    {
        $model = new PatientModel();
        return $this->getResponse(
            [
                'message' => 'Patients retrieved successfully',
                'patients' => $model->findAll()
            ]
        );
    }

    /**
     * Get a single patient by ID
     */
    public function show($id)
    {
        try {
            $model = new PatientModel();
            $patient = $model->findPatientById($id);

            return $this->getResponse(
                [
                    'message' => 'Patient retrieved successfully',
                    'patient' => $patient
                ]
            );

        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find patient for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }

    public function  addRegister()
    {
        //$dir = '../../public_html/registrosECG/';
        // $dir = '../../registrosECG/';
        //$dir = '../public_html/registrosECG/';
        $dir = './assets/registrosECG/';
        if (file_exists($dir))//si existe la carpeta
        {
            $directorio = opendir($dir);
            //mientras exista un fichero dentro del directorio
            while ($varNameFile = readdir($directorio))
            {
                $segmentos = strtok($varNameFile, ".");//obtenemos la cadena de que se encuentra delante el caracter " . "
//                echo "primera parte del nombre " . $segmentos . "<br>";
                $segmentos = strtok(".");
//                echo "segunda parte del nombre " . $segmentos . "<br>";

                if (!($varNameFile == '.' or $varNameFile == '..') && $segmentos == 'SUB')//deshecho los ficheros . y .. y todos los ficheros que no tengan la extension SUB
                {
//                    echo "Nombre fichero:" . $varNameFile . "<br>";

                    $handle = fopen("$dir$varNameFile", "r");
                    if ($handle == false) continue;// si es igual a false no sigue hacia abajo

                    // $size = filesize("$dir$varNameFile");//da el tamaño del fichero

                    //obtengo la fecha
                    $fecha = fread($handle, 6);// en el fichero va de la posicion 0-5  = que son 6 byte
//                    echo "esta es la fecha:" . $fecha . "<br>";

                    //obtengo la hora
                    fseek($handle, 7);//hace que el puntero se ponga en el 8mo byte, es decir en la posicion 7
                    $hora = fread($handle, 5);//se lee desde la posicion 7 a la 11, se lee desde el 8 byte hasta el byte 12,
//                    echo "esta es la hora" . $hora . "<br>";

                    ////este segmento de codigo es para obtener la fecha convertida al formato datatime de BD
                    //es solamente para el año

                    $anno = fread($handle, 2);// se lee la posicion 12 y 13, es decir los del byte 13-14 contando el byte 13
                    $anno = unpack("v", $anno);
//                    echo "este es la año:" . $anno[1] . "<br>";

                    //  fseek($handle,12);
                    //$i = 132;
                    //while($i<=15){
                    //$año[$i-12]= fread($handle,1);
                    // $año[$i-13] =  ord($año);
                    // echo "esta es la año".$año."<br>";
                    //  $i++;
                    //}

                    // es para el año
                    //$año =  ord($año);
                    // echo "esta es la año".$año."<br>";


                    // $año = date("Y");
                    $mes = substr($fecha, 0, -3);  //nos da la cadena sin los ultimo 6 caracteres
                    switch ($mes) {
                        case "Jan":
                            $mes = "01";
                            break;
                        case "Feb":
                            $mes = "02";
                            break;
                        case "Mar":
                            $mes = "03";
                            break;
                        case "Apr":
                            $mes = "04";
                            break;
                        case "May":
                            $mes = "05";
                            break;
                        case "Jun":
                            $mes = "06";
                            break;
                        case "Jul":
                            $mes = "07";
                            break;
                        case "Aug":
                            $mes = "08";
                            break;
                        case "Sep":
                            $mes = "09";
                            break;
                        case "Oct":
                            $mes = "10";
                            break;
                        case "Nov":
                            $mes = "11";
                            break;
                        case "Dec":
                            $mes = "12";
                            break;
                    }
                    $dia = filter_var($fecha, FILTER_SANITIZE_NUMBER_INT);//nos da solamente los enteros que se encuentran en la cadena
//                    echo "dia solo: " . $dia. "<br>";

                    //frecuencia Cardiaca
                    $FrecuenciaCardiaca = fread($handle, 2);// se lee el byte 15 y 16 de las posiciones 14,15
                    $FrecuenciaCardiaca = unpack("v", $FrecuenciaCardiaca);
//                    echo "esta es la Frecuencia Cardiaca:" . $FrecuenciaCardiaca[1] . "<br>";

                    //frecuencia Cardiaca MAX
                    $FrecuenciaCardiacaMax = fread($handle, 2);// se lee el byte 17 y 18 de las posiciones 16,17
                    $FrecuenciaCardiacaMax = unpack("v", $FrecuenciaCardiacaMax);
//                    echo "esta es la Frecuencia Cardiaca Max:" . $FrecuenciaCardiacaMax[1] . "<br>";

                    //frecuencia Cardiaca Min
                    $FrecuenciaCardiacaMin = fread($handle, 2);// se lee el byte 19 y 20 de la posicion 18,19
                    $FrecuenciaCardiacaMin = unpack("v", $FrecuenciaCardiacaMin);
//                    echo "esta es la Frecuencia Cardiaca Min" . $FrecuenciaCardiacaMin[1] . "<br>";

                    $x = date('Y-m-d H:i:s', strtotime("$mes/$dia/$anno[1] $hora"));//$año[1].'-'.$mes.'-'.$dia.' '.$hora;//
//                    echo $x . '<br>';

                    $countTempECG = fread($handle, 1); //// se lee el byte 21 de la posicion 19. se obtiene la cantidad de min del ecg
                    $countTempECG = ord($countTempECG); // se convierte a decimal.
//                    echo "tiempo de ECG:" . $countTempECG . '<br>';


                    $ECG = '';
                    if ($countTempECG == 1) {
                        $ECG = fread($handle, 30000);
                        fseek($handle, 30025);
                    }
                    if ($countTempECG == 2) {
                        $ECG = fread($handle, 60000);
                        fseek($handle, 60025);
                    }
                    if ($countTempECG == 3) {
                        $ECG = fread($handle, 90000);
                        fseek($handle, 90025);
                    }
                    if ($countTempECG == 4) {
                        $ECG = fread($handle, 120000);
                        fseek($handle, 120025);
                    }
                    if ($countTempECG == 5) {
                        $ECG = fread($handle, 150000);
                        fseek($handle, 150025);
                    }
                    $ECG = addslashes($ECG);
                    //echo " ECG:" . $ECG . '<br>';

                    $RR = '';
                    while (!feof($handle)) {
                        $RR .= fread($handle, 1);

                    }
                    $RR = addslashes($RR);
//                    echo " RR:" . $RR . '<br>';
                    //cerrar el fichero que estoy leyendo
                    fclose($handle);

                    //obtengo los valores numericos de la cadena (nombre del fichero) para
                    $rest = strtok($varNameFile,"-");//obtenemos la cadena de que se encuentra delante el caracter " - "

                    //echo $segmentos. "<br>";
                    //$segmentos=strtok("-");
                    //echo $segmentos . "<br>";
                    $idtelefonoMovil = filter_var($rest, FILTER_SANITIZE_NUMBER_INT);//nos da solamente los enteros que se encuentran en la cadena
//                    echo 'id telefono:'.$idtelefonoMovil.'<br>';

                    $model = new PatientModel();
                    $patient = $model->findPatientByPhoneNumberID($idtelefonoMovil);

                    $modelRegister = new RegisterModel();
                    $registro = [
                        'identificacionRegistro'=>'',
                        'diagnosticado'=>'',
                        'registro_Diagnostico'=>'',
                        'nombreRegistro' =>$varNameFile,
                        'fecha'=>$fecha,
                        'fechaprueba'=>$x,
                        'hora'=>$hora,
                        'FrecuenciaCardiaca'=>$FrecuenciaCardiaca[1],
                        'FrecuenciaCardiacaMax'=>$FrecuenciaCardiacaMax[1],
                        'FrecuenciaCardiacaMin'=>$FrecuenciaCardiacaMin[1],
                        'idPaciente'=>$patient['id'],
                        'registroecg'=>$ECG,
                        'RR'=>$RR,
                        'revisar'=>''
                    ];
                    $resultado = $modelRegister->insert($registro);

                   // preguntar y probar
//                    $db = db_connect();
//                    $pQuery = $db->prepare(function($db)
//                    {
//                        $sql = "INSERT INTO registro (identificacionRegistro, diagnosticado, registro_Diagnostico,nombreRegistro,fecha,fechaprueba,hora,FrecuenciaCardiaca,FrecuenciaCardiacaMax,FrecuenciaCardiacaMin,idPaciente,registroecg,RR,revisar) VALUES('','','','$varNameFile','$fecha','$x','$hora','$FrecuenciaCardiaca[1]','$FrecuenciaCardiacaMax[1]','$FrecuenciaCardiacaMin[1]','$idpacienteyMed[id]','$ECG','$RR','')";
//                        $sql = "INSERT INTO user (name, email, country) VALUES (?, ?, ?)";
//                        return (new Query($db))->setQuery($sql);
//                    });

                    if(!$resultado)
                    {
                        return $this->getResponse(
                            [
                                'message' => 'El registro no se pudo guardar en la base de datos.'
                            ],
                            ResponseInterface::HTTP_UNSUPPORTED_MEDIA_TYPE
                        );
                    }
                    else
                    {
                        //chmod("$dir$varNameFile",0777);
                        if(is_file("$dir$varNameFile"))
                        {
                            unlink("$dir$varNameFile");// elimina el archivo, si ok retorna 1 sino 0
                        }
                    }
                }
            }
            closedir($directorio);
        }
        else
        {
            return $this->getResponse(
                [
                    'message' => 'No se ha encontrado la carpeta en la dirección indicada.'
                ],
                ResponseInterface::HTTP_UNSUPPORTED_MEDIA_TYPE
            );
        }
        return $this->getResponse(
            [
                'All ok'
            ],
            ResponseInterface::HTTP_OK
        );
//        echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
    }

    public function do_upload()
    {
        $file = $this->request->getFile('files');
        if (!$file->isValid()) {
            return $this -> getResponse(
                [
                    $file->getErrorString().'('.$file->getError().')'
                ],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }
        $file->move('./assets/registrosECG');
        $this->addRegister();
    }
}