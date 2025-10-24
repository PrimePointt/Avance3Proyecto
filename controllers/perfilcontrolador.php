<?php
// /controllers/VoluntarioController.php

require_once __DIR__ . '/../models/perfil.php';

class VoluntarioController
{
    /**
     * Obtiene y estructura el perfil completo de un voluntario.
     * @param int $voluntarioId El ID del voluntario a buscar.
     * @return array El resultado de la operación con los datos del perfil.
     */
    public function perfil($voluntarioId)
    {
        // 1. Validar la entrada
        if (empty($voluntarioId) || !is_numeric($voluntarioId)) {
            // Se devuelve una respuesta estandarizada para errores del cliente
            http_response_code(400); // Bad Request
            return ['success' => false, 'message' => 'ID de voluntario no válido.'];
        }

        // 2. Interactuar con el Modelo
        $voluntarioModel = new Voluntario();
        $perfilCompleto = $voluntarioModel->obtenerPerfil($voluntarioId);

        // 3. Validar si el modelo devolvió datos
        // Asumimos que el modelo devuelve un array con una clave 'perfil' para los datos principales.
        // Si no existe el voluntario, esta clave estará vacía o el resultado será false.
        if (!$perfilCompleto || empty($perfilCompleto['perfil'])) {
            http_response_code(404); // Not Found
            return ['success' => false, 'message' => 'Perfil de voluntario no encontrado.'];
        }
        
        // Extraemos los datos principales para facilitar su uso
        $datosPrincipales = $perfilCompleto['perfil'];

        // 4. Lógica de negocio: Verificar el estatus del voluntario
        if ($datosPrincipales['Estatus'] !== 'Activo') {
            http_response_code(403); // Forbidden
            return ['success' => false, 'message' => 'Tu cuenta está en proceso de validación o no se encuentra activa.'];
        }

        // 5. Estructurar la respuesta exitosa con todos los campos separados
        // Se agrupan los datos de forma lógica para que sea más fácil de consumir en el frontend.
        return [
            'success' => true,
            'message' => 'Perfil completo obtenido exitosamente.',
            'datos' => [
                'voluntario' => [
                    'nombres' => $datosPrincipales['Nombres'],
                    'apellidoPaterno' => $datosPrincipales['ApellidoPaterno'],
                    'apellidoMaterno' => $datosPrincipales['ApellidoMaterno'],
                    'fechaNacimiento' => $datosPrincipales['FechaNacimiento'],
                    'lugarNacimiento' => $datosPrincipales['LugarNacimiento'],
                    'nacionalidad' => $datosPrincipales['Nacionalidad'],
                    'sexo' => $datosPrincipales['Sexo'],
                    'curp' => $datosPrincipales['CURP'],
                    'email' => $datosPrincipales['Email'],
                    'telefonoCelular' => $datosPrincipales['TelefonoCelular'],
                    'telefonoParticular' => $datosPrincipales['TelefonoParticular'],
                    'telefonoTrabajo' => $datosPrincipales['TelefonoTrabajo'],
                    'enfermedades' => $datosPrincipales['Enfermedades'],
                    'alergias' => $datosPrincipales['Alergias'],
                    'gradoEstudios' => $datosPrincipales['GradoEstudios'],
                    'profesion' => $datosPrincipales['Profesion'],
                    'ocupacionActual' => $datosPrincipales['OcupacionActual'],
                    'empresaLabora' => $datosPrincipales['EmpresaLabora'],
                    'fechaRegistro' => $datosPrincipales['FechaRegistro'],
                ],
                'direccion' => [
                    'calle' => $datosPrincipales['Calle'],
                    'numeroExterior' => $datosPrincipales['NumeroExterior'],
                    'numeroInterior' => $datosPrincipales['NumeroInterior'],
                    'colonia' => $datosPrincipales['Colonia'],
                    'codigoPostal' => $datosPrincipales['CodigoPostal'],
                    'ciudad' => $datosPrincipales['Ciudad'],
                    'estado' => $datosPrincipales['Estado'],
                ],
                'catalogos' => [
                    'rol' => $datosPrincipales['Rol'],
                    'estatus' => $datosPrincipales['Estatus'],
                    'area' => $datosPrincipales['Area'],
                    'delegacion' => $datosPrincipales['Delegacion'],
                    'perfil' => $datosPrincipales['Perfil'],
                    'estadoCivil' => $datosPrincipales['EstadoCivil'],
                    'grupoSanguineo' => $datosPrincipales['GrupoSanguineo'],
                ],
                'contactosEmergencia' => $perfilCompleto['contactos'] ?? [], // Usamos el operador de fusión de null para asegurar que sea un array
                'disponibilidad' => $perfilCompleto['disponibilidad'] ?? [],
            ]
        ];
    }

    /**
     * Actualiza el perfil del voluntario
     * @param int $voluntarioId
     * @param array $datos
     * @return array
     */
    public function actualizarPerfil($voluntarioId, $datos) {
        // 1. Validar la entrada
        if (empty($voluntarioId) || !is_numeric($voluntarioId)) {
            return ['success' => false, 'message' => 'ID de voluntario no válido.'];
        }

        // Validar campos requeridos
        $camposRequeridos = ['email'];
        foreach ($camposRequeridos as $campo) {
            if (empty($datos[$campo])) {
                return ['success' => false, 'message' => 'El campo ' . $campo . ' es requerido.'];
            }
        }

        // Validar formato de email
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'El formato del email no es válido.'];
        }

        // 2. Sanitizar datos
        $datosSanitizados = [];
        foreach ($datos as $campo => $valor) {
            $datosSanitizados[$campo] = strip_tags(trim($valor));
        }

        // 3. Intentar actualizar
        $voluntarioModel = new Voluntario();
        $actualizado = $voluntarioModel->actualizarPerfil($voluntarioId, $datosSanitizados);

        if ($actualizado) {
            return [
                'success' => true,
                'message' => 'Perfil actualizado exitosamente.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo actualizar el perfil. Por favor, intente nuevamente.'
            ];
        }
    }
}
?>