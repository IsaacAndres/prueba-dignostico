<?php 

require_once('app/Models/Comuna.php');
require_once('app/Services/ErrorLog.php');

use App\Services\ErrorLog;

class ComunaController
{
    private $comunaModel;

    public function __construct()
    {
        $this->comunaModel = new Comuna();
    }
    
    public function index()
    {
        header('Content-Type: application/json');

        try {
            if ( !isset($_GET['region']) || !is_numeric($_GET['region']) ) {
                http_response_code(400);
                echo json_encode(['error' => 'Parámetro "Región" inválido.']);
                return;
            }

            $regionId = (int) $_GET['region'];
            $comunas = $this->comunaModel->obtenerComunas($regionId);
            
            http_response_code(200);
            echo json_encode(['data' => $comunas]);
        } catch (Exception $e) {                        

            ErrorLog::create('Error interno del servidor: '.$e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor.']);
        }
    }
}
