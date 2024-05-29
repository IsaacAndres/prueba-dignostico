<?php

require 'config.php';
require_once('app/Services/ErrorLog.php');

use App\Services\ErrorLog;

try {
    $pdo = new PDO(
        'mysql:host='. DB_HOST .';dbname='. DB_NAME .';charset=utf8mb4', 
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE, 
        PDO::ERRMODE_EXCEPTION
    );
} catch(PDOException $e) {        
    ErrorLog::create('Error al conectar a la base de datos: '.$e->getMessage());    
    die('<h3 style="color: red;">Error al conectar a la base de datos. Por favor, inténtelo más tarde.</h3>');
}