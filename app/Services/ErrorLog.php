<?php

namespace App\Services;

class ErrorLog {

    public static function create($message){
        $logFile = __DIR__ . '/../../logs/error.log';    
        // die($logFile);      
        error_log('['.date('d-m-Y H:i').']'.$message.PHP_EOL, 3, $logFile);    
    }

}