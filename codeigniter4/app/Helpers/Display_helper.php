<?php
/**
 * Show player function
 */
if (!function_exists("showPlayerFunction")) {
    function showPlayerFunction($function_id)
    {
        $res = '';
        switch($function_id)
        {
            case 'AR1':
                break;
            case 'AR2':
                break;
            case 'MAR':
                break;
            case 'LIB':
                $res = 'Libéro';
                break;
            case 'EA':
                $res = 'Capitaine';
                break;
            default:
                $res = $function_id;
        }
        return $res;
    }
}