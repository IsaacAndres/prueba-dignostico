<?php

class RutValidator {
    public function validate($rut) 
    {
        // El formato válido es: números, puntos y guión, y un dígito verificador al final
        if (!preg_match("/^[0-9.]+[-]?+[0-9kK]{1}/", $rut)) {
            return false;
        }
    
        // Elimina puntos y guiones del RUT
        $rut = preg_replace('/[\.\-]/i', '', $rut);
    
        // Extrae el dígito verificador y el número del RUT
        $dv = substr($rut, -1);
        $numero = substr($rut, 0, strlen($rut) - 1);
    
        // Calcula el dígito verificador esperado
        $i = 2;
        $suma = 0;
        foreach (array_reverse(str_split($numero)) as $v) {
            if ($i == 8)
                $i = 2;
            $suma += $v * $i;
            ++$i;
        }
        $dvr = 11 - ($suma % 11);
    
        // Si el dígito verificador esperado es 11, se reemplaza por 0
        if ($dvr == 11)
            $dvr = 0;
    
        // Si el dígito verificador esperado es 10, se reemplaza por 'K'
        if ($dvr == 10)
            $dvr = 'K';
    
        // Compara el dígito verificador esperado con el dígito verificador del RUT
        // Devuelve true si son iguales, false si no lo son
        return $dvr == strtoupper($dv);        
    }

}