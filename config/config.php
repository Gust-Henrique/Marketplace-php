<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'marketplace');
define('DB_USER', 'root');
define('DB_PASS', '');


define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); 
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('UPLOAD_PATH', '../public/images/');


define('SESSION_TIMEOUT', 3600); 


date_default_timezone_set('America/Sao_Paulo');


if ($_SERVER['HTTP_HOST'] === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}


function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}


function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11) return false;

    if (preg_match('/(\d)\1{10}/', $cpf)) return false;

    
    for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--) {
        $soma += $cpf[$i] * $j;
    }
    $resto = $soma % 11;
    if ($cpf[9] != ($resto < 2 ? 0 : 11 - $resto)) return false;

    
    for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--) {
        $soma += $cpf[$i] * $j;
    }
    $resto = $soma % 11;
    return $cpf[10] == ($resto < 2 ? 0 : 11 - $resto);
}


function formatarPreco($preco) {
    return 'R$ ' . number_format($preco, 2, ',', '.');
}


function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}
?>