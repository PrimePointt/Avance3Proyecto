<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Asegurarnos de que los errores se registren en el log
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

// Registrar el inicio de la solicitud
error_log("Iniciando solicitud de actualización de perfil");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_log("Estado de la sesión en actualizar_perfil: " . print_r($_SESSION, true));

// Incluir los archivos necesarios usando rutas relativas correctas
require_once __DIR__ . '/../models/perfil.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Método no permitido: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['voluntarioId'])) {
    error_log("Intento de actualización sin sesión de usuario");
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Obtener y validar los datos enviados
$rawData = file_get_contents('php://input');
error_log("Datos recibidos raw: " . $rawData);

// Limpiar posibles BOM u otros caracteres no deseados
$rawData = trim($rawData);
if (!empty($rawData)) {
    // Remover BOM si existe
    if (substr($rawData, 0, 3) === pack('CCC', 0xef, 0xbb, 0xbf)) {
        $rawData = substr($rawData, 3);
    }
}

try {
    $datos = json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);
    error_log("Datos decodificados exitosamente: " . print_r($datos, true));
} catch (JsonException $e) {
    error_log("Error decodificando JSON: " . $e->getMessage());
    error_log("Datos raw recibidos: " . bin2hex($rawData));
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el formato de los datos',
        'debug' => $e->getMessage()
    ]);
    exit;
}

if (!is_array($datos)) {
    error_log("Los datos decodificados no son un array");
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Formato de datos incorrecto'
    ]);
    exit;
}

// Lista de campos permitidos
$camposPermitidos = [
    'telefonoCelular',
    'telefonoParticular',
    'telefonoTrabajo',
    'ocupacionActual',
    'empresaLabora',
    'calle',
    'numeroExterior',
    'colonia',
    'codigoPostal'
];

// Filtrar solo los campos permitidos
$datosFiltrados = array_intersect_key(
    $datos,
    array_flip($camposPermitidos)
);

// Validar teléfono celular requerido
if (!isset($datosFiltrados['telefonoCelular']) || trim($datosFiltrados['telefonoCelular']) === '') {
    error_log("Teléfono celular requerido");
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => "El teléfono celular es requerido"
    ]);
    exit;
}

try {
    $voluntario = new Voluntario();
    
    // Intentar actualizar el perfil
    $resultado = $voluntario->actualizarPerfil($_SESSION['voluntarioId'], $datos);
    
    if ($resultado['success']) {
        error_log("Perfil actualizado exitosamente para el voluntario ID: " . $_SESSION['voluntarioId']);
        echo json_encode([
            'success' => true,
            'message' => $resultado['message']
        ]);
    } else {
        error_log("Error al actualizar el perfil: " . $resultado['message']);
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $resultado['message']
        ]);
    }
} catch (Exception $e) {
    error_log("Error en la actualización del perfil: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}