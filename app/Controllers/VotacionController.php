<?php 

require_once('app/Models/Candidato.php');
require_once('app/Models/Region.php');
require_once('app/Models/Voto.php');
require_once('app/Services/RutValidator.php');
require_once('app/Services/ErrorLog.php');
require_once('app/Services/ValidationException.php');

use App\Services\ErrorLog;
use App\Services\ValidationException;


class VotacionController
{
    private $candidatoModel;
    private $regionModel;
    private $votoModel;
    private $rutValidator;

    public function __construct()
    {
        $this->candidatoModel = new Candidato();
        $this->regionModel = new Region();
        $this->votoModel = new Voto();
        $this->rutValidator = new RutValidator();
    }
    
    public function index()
    {
        $candidatos = $this->candidatoModel->listar();
        $regiones = $this->regionModel->listar();
        require_once('app/Views/Index.php');
    }

    public function store()
    {
        header('Content-Type: application/json');

        try {

            $nombre     = $_POST['nombre'];
            $alias      = $_POST['alias'];
            $rut        = $_POST['rut'];
            $email      = $_POST['email'];
            $comuna     = $_POST['comuna'];
            $candidato  = $_POST['candidato'];
            $como       = $_POST['como'];

            //  Validaciones
            // Nombre
            if ( strlen($nombre) < 5 ) {
                throw new ValidationException('Debe ingresar su Nombre y Apellido');
            }
            //  Alias
            if ( strlen($alias) < 6) {
                throw new ValidationException('El Alias debe tener más de 5 caracteres');
            }

            if ( !preg_match('/\d/',$alias) ) {
                throw new ValidationException('El Alias debe contener números');
            }

            if ( !preg_match('/[a-zA-Z]/', $alias) ) {
                throw new ValidationException('El Alias debe contener letras');
            }
            // RUT
            if ( !$this->rutValidator->validate($rut) ) {
                throw new ValidationException('El RUT es incorrecto');
            }

            if ( count($this->votoModel->obtenerPorRut($rut)) ) {
                throw new ValidationException('Solo se puede ingresar un voto por RUT');
            }
            // Email
            if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
                throw new ValidationException('El Email no es valido');
            }
            // comuna
            if ( !$comuna ) {
                throw new ValidationException('Debe ingresar su Comuna');
            }
            // Candidato
            if ( !$candidato ) {
                throw new ValidationException('Debe ingresar su Candidato');
            }
            // Como se enteró
            if ( count($como) < 2 ) {
                throw new ValidationException('Debe seleccionar al menos dos campos "Como se enteró de Nosotros"');
            }

            $voto = array(
                'nombre'    => $nombre,
                'alias'     => $alias,
                'rut'       => $rut,
                'email'     => $email,
                'comuna'    => $comuna,
                'candidato' => $candidato,
                'como'      => json_encode($como)
            );

            $votoId = $this->votoModel->store($voto);

            if ( !$votoId ) {
                throw new ValidationException('Hubo un error inesperado, revise los datos y vuelva a intentar');
            }
            
            http_response_code(200);
            echo json_encode(['data' => $votoId]);
        } catch (ValidationException $e) {
            
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }catch (Exception $e) {

            ErrorLog::create('Error interno del servidor: '.$e->getMessage());

            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
}
