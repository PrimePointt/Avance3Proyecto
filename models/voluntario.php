<?php
// /models/Voluntario.php
// En models/voluntario.php

require_once __DIR__ . '/../config/Database.php';
class Voluntario {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }
    
    // ... (método obtenerDatosLogin sin cambios) ...
    public function obtenerDatosLogin($email) {
        try {
            $sql = "EXEC sp_ObtenerDatosLogin @Email = :email";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerDatosLogin: " . $e->getMessage());
            return false;
        }
    }
    public function aprobarVoluntario($voluntarioIDaAprobar, $adminIDqueAprueba) {
        try {
            $sql = "EXEC sp_AprobarVoluntario 
                        @VoluntarioIDaAprobar = :voluntarioID, 
                        @AdminIDqueAprueba = :adminID";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':voluntarioID', $voluntarioIDaAprobar, PDO::PARAM_INT);
            $stmt->bindParam(':adminID', $adminIDqueAprueba, PDO::PARAM_INT);
            $stmt->execute();
            return true; // Si no hay error, la ejecución fue exitosa
        } catch (PDOException $e) {
            error_log("Error en aprobarVoluntario: " . $e->getMessage());
            return false;
        }
    }
    public function voluntariosSinAprobar() {
    try {
        $sql = "SELECT 
                    v.VoluntarioID, 
                    v.Nombres, 
                    v.ApellidoPaterno, 
                    v.Email, 
                    ev.Nombre AS EstatusNombre
                FROM 
                    dbo.Voluntarios AS v
                INNER JOIN 
                    dbo.EstatusVoluntario AS ev ON v.EstatusID = ev.EstatusID
                WHERE 
                    ev.Nombre = 'Pendiente de Aprobación'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        // ----> CAMBIO PRINCIPAL AQUÍ <----
        // Usamos fetchAll() para obtener TODOS los voluntarios, no solo el primero.
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 

    } catch (PDOException $e) {
        // ----> CAMBIO MENOR AQUÍ <----
        // Corregimos el mensaje de error para que apunte a la función correcta.
        error_log("Error en voluntariosSinAprobar: " . $e->getMessage());
        return false;
    }
}


    /**
     * Llama al SP para registrar un nuevo voluntario con todos sus datos.
     * @param array $datos Un array asociativo con todos los datos del voluntario.
     * @return array El resultado de la operación.
     */
    public function registrarNuevoVoluntario($datos) {
        try {
            $sql = "EXEC sp_RegistrarNuevoVoluntarioCompleto
                        @Nombres = :nombres, @ApellidoPaterno = :apellidoPaterno, @ApellidoMaterno = :apellidoMaterno,
                        @FechaNacimiento = :fechaNacimiento, @Email = :email, @PasswordHash = :passwordHash,
                        @CURP = :curp, @Sexo = :sexo, @LugarNacimiento = :lugarNacimiento, @Nacionalidad = :nacionalidad,
                        @EstadoCivilID = :estadoCivilID, @GrupoSanguineoID = :grupoSanguineoID,
                        @TelefonoCelular = :telefonoCelular, @TelefonoParticular = :telefonoParticular, @TelefonoTrabajo = :telefonoTrabajo,
                        @Calle = :calle, @NumeroExterior = :numeroExterior, @Colonia = :colonia, @CodigoPostal = :codigoPostal,
                        @CiudadID = :ciudadID, @EstadoID = :estadoID,
                        @GradoEstudios = :gradoEstudios, @Profesion = :profesion, @OcupacionActual = :ocupacionActual,
                        @EmpresaLabora = :empresaLabora, @TieneLicenciaConducir = :tieneLicenciaConducir,
                        @Enfermedades = :enfermedades, @Alergias = :alergias,
                        @AreaID = :areaID, @DelegacionID = :delegacionID,
                        @TutorNombreCompleto = :tutorNombre, @TutorParentesco = :tutorParentesco, @TutorTelefono = :tutorTelefono,
                        @ContactoEmergenciaNombre = :contactoEmergenciaNombre,
                        @ContactoEmergenciaParentesco = :contactoEmergenciaParentesco,
                        @ContactoEmergenciaTelefono = :contactoEmergenciaTelefono,
                        @DisponibilidadDiaSemana = :disponibilidadDia,
                        @DisponibilidadTurno = :disponibilidadTurno";

            $stmt = $this->pdo->prepare($sql);

            // Asignar cada valor del array a su parámetro correspondiente.
            $stmt->bindParam(':nombres', $datos['nombres']);
            $stmt->bindParam(':apellidoPaterno', $datos['apellidoPaterno']);
            $stmt->bindParam(':apellidoMaterno', $datos['apellidoMaterno']);
            $stmt->bindParam(':fechaNacimiento', $datos['fechaNacimiento']);
            $stmt->bindParam(':email', $datos['email']);
            $stmt->bindParam(':passwordHash', $datos['passwordHash']);
            $stmt->bindParam(':curp', $datos['curp']);
            $stmt->bindParam(':sexo', $datos['sexo']);
            $stmt->bindParam(':lugarNacimiento', $datos['lugarNacimiento']);
            $stmt->bindParam(':nacionalidad', $datos['nacionalidad']);
            $stmt->bindParam(':estadoCivilID', $datos['estadoCivilID'], PDO::PARAM_INT);
            $stmt->bindParam(':grupoSanguineoID', $datos['grupoSanguineoID'], PDO::PARAM_INT);
            $stmt->bindParam(':telefonoCelular', $datos['telefonoCelular']);
            $stmt->bindParam(':telefonoParticular', $datos['telefonoParticular']);
            $stmt->bindParam(':telefonoTrabajo', $datos['telefonoTrabajo']);
            $stmt->bindParam(':calle', $datos['calle']);
            $stmt->bindParam(':numeroExterior', $datos['numeroExterior']);
            $stmt->bindParam(':colonia', $datos['colonia']);
            $stmt->bindParam(':codigoPostal', $datos['codigoPostal']);
            $stmt->bindParam(':ciudadID', $datos['ciudadID'], PDO::PARAM_INT);
            $stmt->bindParam(':estadoID', $datos['estadoID'], PDO::PARAM_INT);
            $stmt->bindParam(':gradoEstudios', $datos['gradoEstudios']);
            $stmt->bindParam(':profesion', $datos['profesion']);
            $stmt->bindParam(':ocupacionActual', $datos['ocupacionActual']);
            $stmt->bindParam(':empresaLabora', $datos['empresaLabora']);
            $stmt->bindParam(':tieneLicenciaConducir', $datos['tieneLicenciaConducir'], PDO::PARAM_BOOL);
            $stmt->bindParam(':enfermedades', $datos['enfermedades']);
            $stmt->bindParam(':alergias', $datos['alergias']);
            $stmt->bindParam(':areaID', $datos['areaID'], PDO::PARAM_INT);
            $stmt->bindParam(':delegacionID', $datos['delegacionID'], PDO::PARAM_INT);
            $stmt->bindParam(':tutorNombre', $datos['tutorNombre']);
            $stmt->bindParam(':tutorParentesco', $datos['tutorParentesco']);
            $stmt->bindParam(':tutorTelefono', $datos['tutorTelefono']);
            $stmt->bindParam(':contactoEmergenciaNombre', $datos['contactoEmergenciaNombre']);
            $stmt->bindParam(':contactoEmergenciaParentesco', $datos['contactoEmergenciaParentesco']);
            $stmt->bindParam(':contactoEmergenciaTelefono', $datos['contactoEmergenciaTelefono']);
            $stmt->bindParam(':disponibilidadDia', $datos['disponibilidadDia'], PDO::PARAM_INT);
            $stmt->bindParam(':disponibilidadTurno', $datos['disponibilidadTurno']);

            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // CORRECCIÓN: Devolver el mensaje de error específico de la base de datos.
            // Esto nos dirá si el error es por un email duplicado, CURP duplicado, etc.
            return ['error' => $e->getMessage()];
        }
    }

    public function actualizarPerfil($voluntarioId, $datos) {
        try {
            error_log("Iniciando actualización de perfil para voluntario ID: " . $voluntarioId);
            error_log("Datos recibidos: " . print_r($datos, true));

            $sql = "EXEC sp_ActualizarMiPerfil 
                    @VoluntarioID = :voluntarioId,
                    @TelefonoCelular = :telefonoCelular,
                    @TelefonoParticular = :telefonoParticular,
                    @TelefonoTrabajo = :telefonoTrabajo,
                    @OcupacionActual = :ocupacionActual,
                    @EmpresaLabora = :empresaLabora,
                    @Calle = :calle,
                    @NumeroExterior = :numeroExterior,
                    @Colonia = :colonia,
                    @CodigoPostal = :codigoPostal";

            $stmt = $this->pdo->prepare($sql);
            
            // Bindear los parámetros
            $stmt->bindParam(':voluntarioId', $voluntarioId, PDO::PARAM_INT);
            $stmt->bindParam(':telefonoCelular', $datos['telefonoCelular']);
            $stmt->bindParam(':telefonoParticular', $datos['telefonoParticular']);
            $stmt->bindParam(':telefonoTrabajo', $datos['telefonoTrabajo']);
            $stmt->bindParam(':ocupacionActual', $datos['ocupacionActual']);
            $stmt->bindParam(':empresaLabora', $datos['empresaLabora']);
            $stmt->bindParam(':calle', $datos['calle']);
            $stmt->bindParam(':numeroExterior', $datos['numeroExterior']);
            $stmt->bindParam(':colonia', $datos['colonia']);
            $stmt->bindParam(':codigoPostal', $datos['codigoPostal']);

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log("Resultado de la actualización: " . print_r($resultado, true));

            if ($resultado === false) {
                return [
                    'success' => true,
                    'message' => 'Perfil actualizado exitosamente'
                ];
            }

            return [
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
                'data' => $resultado
            ];

        } catch (PDOException $e) {
            error_log("Error PDO en actualizarPerfil: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al actualizar el perfil: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            error_log("Error general en actualizarPerfil: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error inesperado al actualizar el perfil'
            ];
        }
    }
}
?>

