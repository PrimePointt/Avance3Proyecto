<?php
// Esta es la forma correcta, usando el nombre real del archivo
require_once 'controllers/Perfilcontrolador.php';
$voluntarioId=7;

$voluntariocontroller = new VoluntarioController();

$perfilCompleto = $voluntariocontroller->perfil($voluntarioId);

var_dump($perfilCompleto);
?>