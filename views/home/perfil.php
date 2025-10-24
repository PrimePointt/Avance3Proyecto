<?php 
$base_url = '/ProyectoSGV/';

// Para debugging - Verifica el estado de la sesión
error_log('Estado de la sesión en vista perfil: ' . print_r($_SESSION, true));

// Incluir el controlador - usando la ruta correcta
require_once $_SERVER['DOCUMENT_ROOT'] . '/ProyectoSGV/controllers/perfilcontrolador.php';

// Crear una instancia del controlador y obtener los datos del perfil
$controller = new VoluntarioController();
error_log('Intentando obtener perfil para voluntarioId: ' . $_SESSION['voluntarioId']);
$resultado = $controller->perfil($_SESSION['voluntarioId']);
error_log('Resultado de obtener perfil: ' . print_r($resultado, true));

// Si hay un error, mostrar mensaje
if (!$resultado['success']) {
    $error_message = $resultado['message'];
}

// Si hay datos, asignarlos a variables para usar en la vista
// Función auxiliar para manejar valores seguros
function obtenerValorSeguro($array, $key, $default = '') {
    return isset($array[$key]) && $array[$key] !== null ? $array[$key] : $default;
}

if (isset($resultado['datos'])) {
    $voluntario = $resultado['datos']['voluntario'] ?? [];
    $direccion = $resultado['datos']['direccion'] ?? [];
    $catalogos = $resultado['datos']['catalogos'] ?? [];
    $contactos = $resultado['datos']['contactosEmergencia'] ?? [];
    $disponibilidad = $resultado['datos']['disponibilidad'] ?? [];
    
    // Debug para ver la estructura de los datos
    error_log('Estructura de datos completa: ' . print_r($resultado['datos'], true));
}
?>
    
    <aside class="perfil-aside">
      <h3>Panel de administración</h3>
      <ul class="perfil-menu">
        <li class="activo" data-section="perfil"><i class="fa-solid fa-id-card"></i> Mi perfil</li>
        <li data-section="solicitudes"><i class="fa-solid fa-file-pen"></i> Mis solicitudes</li>
        <li data-section="cargo"><i class="fa-solid fa-users"></i> A mi cargo</li>
        <li data-section="coordinadores"><i class="fa-solid fa-user-tie"></i> Coordinadores</li>
      </ul>
    </aside>

    <section class="perfil-contenido" id="perfil-contenido">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php else: ?>
            <div class="perfil-info">
                <!-- Función auxiliar para manejar valores nulos -->
                <?php 
                function mostrarValor($valor, $valorPorDefecto = 'No especificado') {
                    return !empty($valor) ? htmlspecialchars($valor) : '<em>' . $valorPorDefecto . '</em>';
                }
                ?>

                <div class="header-seccion">
                    <h2>Información Personal</h2>
                    <button type="button" class="btn-editar" id="btn-editar">
                        <i class="fas fa-edit"></i> Editar Información
                    </button>
                </div>
                <div class="info-grupo">
                    <p><strong>Nombre completo:</strong> <?php 
                        $nombreCompleto = trim(implode(' ', array_filter([
                            $voluntario['nombres'] ?? '',
                            $voluntario['apellidoPaterno'] ?? '',
                            $voluntario['apellidoMaterno'] ?? ''
                        ])));
                        echo mostrarValor($nombreCompleto);
                    ?></p>
                    <p><strong>CURP:</strong> <?php echo mostrarValor($voluntario['curp']); ?></p>
                    <p><strong>Fecha de nacimiento:</strong> <?php echo mostrarValor($voluntario['fechaNacimiento']); ?></p>
                    <p><strong>Tipo de sangre:</strong> <?php echo mostrarValor($catalogos['grupoSanguineo']); ?></p>
                    <p><strong>Estado civil:</strong> <?php echo mostrarValor($catalogos['estadoCivil']); ?></p>
                </div>

                <h2>Contacto</h2>
                <div class="info-grupo">
                    <p><strong>Email:</strong> <span data-campo="email"><?php echo mostrarValor($voluntario['email']); ?></span></p>
                    <p><strong>Teléfono celular:</strong> <span data-campo="telefono-celular"><?php echo mostrarValor($voluntario['telefonoCelular'], 'No proporcionado'); ?></span></p>
                    <p><strong>Teléfono particular:</strong> <span data-campo="telefono-particular"><?php echo mostrarValor($voluntario['telefonoParticular'], 'No proporcionado'); ?></span></p>
                    <p><strong>Teléfono trabajo:</strong> <span data-campo="telefono-trabajo"><?php echo mostrarValor($voluntario['telefonoTrabajo'], 'No proporcionado'); ?></span></p>
                </div>

                <h2>Domicilio</h2>
                <div class="info-grupo">
                    <p><strong>Dirección:</strong> 
                        <span data-campo="direccion-completa"><?php 
                            $direccionCompleta = trim(
                                ($direccion['calle'] ?? '') . ' ' . 
                                ($direccion['numeroExterior'] ?? '') . 
                                (!empty($direccion['numeroInterior']) ? ' Int. ' . $direccion['numeroInterior'] : '')
                            );
                            echo mostrarValor($direccionCompleta, 'Dirección no proporcionada');
                        ?></span>
                    </p>
                    <p><strong>Colonia:</strong> <span data-campo="colonia"><?php echo mostrarValor($direccion['colonia']); ?></span></p>
                    <p><strong>Ciudad:</strong> <span data-campo="ciudad"><?php echo mostrarValor($direccion['ciudad']); ?></span></p>
                    <p><strong>Estado:</strong> <span data-campo="estado"><?php echo mostrarValor($direccion['estado']); ?></span></p>
                    <p><strong>Código Postal:</strong> <span data-campo="codigo-postal"><?php echo mostrarValor($direccion['codigoPostal']); ?></span></p>
                </div>

                <h2>Información Médica</h2>
                <div class="info-grupo">
                    <p><strong>Enfermedades:</strong> <?php echo mostrarValor($voluntario['enfermedades'], 'Ninguna reportada'); ?></p>
                    <p><strong>Alergias:</strong> <?php echo mostrarValor($voluntario['alergias'], 'Ninguna reportada'); ?></p>
                </div>

                <h2>Información Profesional</h2>
                <div class="info-grupo">
                    <p><strong>Grado de estudios:</strong> <?php echo mostrarValor($voluntario['gradoEstudios']); ?></p>
                    <p><strong>Profesión:</strong> <?php echo mostrarValor($voluntario['profesion']); ?></p>
                    <p><strong>Ocupación actual:</strong> <?php echo mostrarValor($voluntario['ocupacionActual']); ?></p>
                    <p><strong>Empresa donde labora:</strong> <?php echo mostrarValor($voluntario['empresaLabora'], 'No especificada'); ?></p>
                </div>

                <h2>Información de Voluntariado</h2>
                <div class="info-grupo">
                    <p><strong>Área:</strong> <?php echo mostrarValor($catalogos['area']); ?></p>
                    <p><strong>Delegación:</strong> <?php echo mostrarValor($catalogos['delegacion']); ?></p>
                    <p><strong>Perfil:</strong> <?php echo mostrarValor($catalogos['perfil']); ?></p>
                    <p><strong>Rol:</strong> <?php echo mostrarValor($catalogos['rol']); ?></p>
                    <p><strong>Estatus:</strong> <?php echo mostrarValor($catalogos['estatus']); ?></p>
                    <p><strong>Fecha de registro:</strong> <?php echo mostrarValor($voluntario['fechaRegistro']); ?></p>
                </div>

                <h2>Contactos de Emergencia</h2>
                <div class="info-grupo">
                    <?php if (!empty($contactos)): ?>
                        <?php foreach ($contactos as $contacto): ?>
                            <div class="contacto-emergencia">
                                <p><strong>Nombre:</strong> <?php echo mostrarValor($contacto['NombreCompleto'], 'Nombre no especificado'); ?></p>
                                <p><strong>Parentesco:</strong> <?php echo mostrarValor($contacto['Parentesco'], 'Parentesco no especificado'); ?></p>
                                <p><strong>Teléfono:</strong> <?php echo mostrarValor($contacto['Telefono'], 'Teléfono no especificado'); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><em>No se han registrado contactos de emergencia</em></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
    </main>

    <!-- Modal de edición -->
    <div id="modal-editar" class="modal">
        <div class="modal-content">
            <span class="cerrar-modal" onclick="cerrarModal()">&times;</span>
            <h2>Editar Información</h2>
            <form id="form-editar">
                <div class="form-seccion">
                    <h3>Información de Contacto</h3>
                    <div class="campo-form">
                        <label for="telefonoCelular">Teléfono Celular:</label>
                        <input type="tel" id="telefonoCelular" name="telefonoCelular" value="<?php echo htmlspecialchars(obtenerValorSeguro($voluntario, 'telefonoCelular')); ?>">
                    </div>
                    <div class="campo-form">
                        <label for="telefonoParticular">Teléfono Particular:</label>
                        <input type="tel" id="telefonoParticular" name="telefonoParticular" value="<?php echo htmlspecialchars(obtenerValorSeguro($voluntario, 'telefonoParticular')); ?>">
                    </div>
                    <div class="campo-form">
                        <label for="telefonoTrabajo">Teléfono Trabajo:</label>
                        <input type="tel" id="telefonoTrabajo" name="telefonoTrabajo" value="<?php echo htmlspecialchars(obtenerValorSeguro($voluntario, 'telefonoTrabajo')); ?>">`
                    </div>
                </div>

                <div class="form-seccion">
                    <h3>Dirección</h3>
                    <div class="campo-form">
                        <label for="calle">Calle:</label>
                        <input type="text" id="calle" name="calle" value="<?php echo htmlspecialchars($direccion['calle'] ?? ''); ?>">
                    </div>
                    <div class="campo-form">
                        <label for="numeroExterior">Número Exterior:</label>
                        <input type="text" id="numeroExterior" name="numeroExterior" value="<?php echo htmlspecialchars($direccion['numeroExterior'] ?? ''); ?>">
                    </div>
                    <div class="campo-form">
                        <label for="numeroInterior">Número Interior:</label>
                        <input type="text" id="numeroInterior" name="numeroInterior" value="<?php echo htmlspecialchars($direccion['numeroInterior'] ?? ''); ?>">
                    </div>
                    <div class="campo-form">
                        <label for="colonia">Colonia:</label>
                        <input type="text" id="colonia" name="colonia" value="<?php echo htmlspecialchars($direccion['colonia'] ?? ''); ?>">
                    </div>
                    <div class="campo-form">
                        <label for="codigoPostal">Código Postal:</label>
                        <input type="text" id="codigoPostal" name="codigoPostal" value="<?php echo htmlspecialchars($direccion['codigoPostal'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-seccion">
                    <h3>Información Médica</h3>
                    <div class="campo-form">
                        <label for="enfermedades">Enfermedades:</label>
                        <textarea id="enfermedades" name="enfermedades"><?php echo htmlspecialchars($voluntario['enfermedades']); ?></textarea>
                    </div>
                    <div class="campo-form">
                        <label for="alergias">Alergias:</label>
                        <textarea id="alergias" name="alergias"><?php echo htmlspecialchars($voluntario['alergias']); ?></textarea>
                    </div>
                </div>

                <div class="form-seccion">
                    <h3>Información Profesional</h3>
                    <div class="campo-form">
                        <label for="gradoEstudios">Grado de Estudios:</label>
                        <input type="text" id="gradoEstudios" name="gradoEstudios" value="<?php echo htmlspecialchars($voluntario['gradoEstudios']); ?>">
                    </div>
                    <div class="campo-form">
                        <label for="profesion">Profesión:</label>
                        <input type="text" id="profesion" name="profesion" value="<?php echo htmlspecialchars($voluntario['profesion']); ?>">
                    </div>
                    <div class="campo-form">
                        <label for="ocupacionActual">Ocupación Actual:</label>
                        <input type="text" id="ocupacionActual" name="ocupacionActual" value="<?php echo htmlspecialchars($voluntario['ocupacionActual']); ?>">
                    </div>
                    <div class="campo-form">
                        <label for="empresaLabora">Empresa donde Labora:</label>
                        <input type="text" id="empresaLabora" name="empresaLabora" value="<?php echo htmlspecialchars($voluntario['empresaLabora']); ?>">
                    </div>
                </div>

                <div class="form-acciones">
                    <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .btn-editar {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-editar:hover {
            background-color: #2980b9;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
            position: relative;
            max-height: 80vh;
            overflow-y: auto;
        }

        .cerrar-modal {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .form-seccion {
            margin-bottom: 25px;
        }

        .form-seccion h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #3498db;
        }

        .campo-form {
            margin-bottom: 15px;
        }

        .campo-form label {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
            font-weight: 500;
        }

        .campo-form input,
        .campo-form textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .campo-form textarea {
            min-height: 80px;
            resize: vertical;
        }

        .form-acciones {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-guardar,
        .btn-cancelar {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn-guardar {
            background-color: #2ecc71;
            color: white;
        }

        .btn-guardar:hover {
            background-color: #27ae60;
        }

        .btn-cancelar {
            background-color: #95a5a6;
            color: white;
        }

        .btn-cancelar:hover {
            background-color: #7f8c8d;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar el formulario cuando el documento esté listo
            const form = document.getElementById('form-editar');
            if (form) {
                form.addEventListener('submit', guardarCambios);
            }

            // Configurar el botón de editar
            const btnEditar = document.getElementById('btn-editar');
            if (btnEditar) {
                btnEditar.addEventListener('click', mostrarFormularioEdicion);
            }
        });

        function mostrarFormularioEdicion() {
            document.getElementById('modal-editar').style.display = 'block';
        }

        function cerrarModal() {
            document.getElementById('modal-editar').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('modal-editar');
            if (event.target === modal) {
                cerrarModal();
            }
        }

        async function guardarCambios(event) {
            event.preventDefault();
            
            try {
                const form = document.getElementById('form-editar');
                const formData = new FormData(form);
                const datos = Object.fromEntries(formData.entries());

                console.log('Enviando datos:', datos); // Debug

                const response = await fetch('/ProyectoSGV/controllers/actualizar_perfil.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(datos)
                });

                console.log('Respuesta status:', response.status); // Debug

                // Si la respuesta no es OK, lanzar error
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error response:', errorText); // Debug
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const resultado = await response.json();
                console.log('Resultado:', resultado); // Debug

                if (resultado.success) {
                    alert('Los cambios se guardaron correctamente');
                    window.location.reload(); // Recargar para mostrar los cambios
                } else {
                    throw new Error(resultado.message || 'Error al guardar los cambios');
                }
            } catch (error) {
                console.error('Error detallado:', error);
                alert('Error al procesar la solicitud: ' + error.message);
            }
        }
    </script>

    <!-- Modal de edición -->
    <div id="modal-editar" class="modal">
        <div class="modal-content">
            <span id="cerrar-modal" class="cerrar-modal">&times;</span>
            <h2>Editar información</h2>
            <form id="form-editar">
                <h3>Información de Contacto</h3>
                <div class="form-grupo">
                    <label for="email">Email*:</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="telefonoCelular">Teléfono Celular:</label>
                    <input type="tel" id="telefonoCelular" name="telefonoCelular">
                    
                    <label for="telefonoParticular">Teléfono Particular:</label>
                    <input type="tel" id="telefonoParticular" name="telefonoParticular">
                    
                    <label for="telefonoTrabajo">Teléfono Trabajo:</label>
                    <input type="tel" id="telefonoTrabajo" name="telefonoTrabajo">
                </div>

                <h3>Domicilio</h3>
                <div class="form-grupo">
                    <label for="calle">Calle:</label>
                    <input type="text" id="calle" name="calle">
                    
                    <label for="numeroExterior">Número Exterior:</label>
                    <input type="text" id="numeroExterior" name="numeroExterior">
                    
                    <label for="numeroInterior">Número Interior:</label>
                    <input type="text" id="numeroInterior" name="numeroInterior">
                    
                    <label for="colonia">Colonia:</label>
                    <input type="text" id="colonia" name="colonia">
                    
                    <label for="codigoPostal">Código Postal:</label>
                    <input type="text" id="codigoPostal" name="codigoPostal">
                    
                    <label for="ciudad">Ciudad:</label>
                    <input type="text" id="ciudad" name="ciudad">
                    
                    <label for="estado">Estado:</label>
                    <input type="text" id="estado" name="estado">
                </div>

                <h3>Información Médica</h3>
                <div class="form-grupo">
                    <label for="enfermedades">Enfermedades:</label>
                    <textarea id="enfermedades" name="enfermedades"></textarea>
                    
                    <label for="alergias">Alergias:</label>
                    <textarea id="alergias" name="alergias"></textarea>
                </div>

                <h3>Información Profesional</h3>
                <div class="form-grupo">
                    <label for="gradoEstudios">Grado de Estudios:</label>
                    <input type="text" id="gradoEstudios" name="gradoEstudios">
                    
                    <label for="profesion">Profesión:</label>
                    <input type="text" id="profesion" name="profesion">
                    
                    <label for="ocupacionActual">Ocupación Actual:</label>
                    <input type="text" id="ocupacionActual" name="ocupacionActual">
                    
                    <label for="empresaLabora">Empresa donde Labora:</label>
                    <input type="text" id="empresaLabora" name="empresaLabora">
                </div>

                <div class="form-acciones">
                    <button type="submit" class="btn-guardar">Guardar cambios</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 700px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .cerrar-modal {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .cerrar-modal:hover {
            color: #333;
        }

        /* Estilos para el formulario */
        .form-grupo {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }

        #form-editar h3 {
            color: #2c3e50;
            margin: 20px 0 10px;
            font-size: 1.2em;
        }

        #form-editar label {
            display: block;
            margin: 10px 0 5px;
            color: #2c3e50;
            font-weight: 500;
        }

        #form-editar input,
        #form-editar textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        #form-editar textarea {
            min-height: 80px;
            resize: vertical;
        }

        .form-acciones {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-guardar,
        .btn-cancelar {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn-guardar {
            background-color: #3498db;
            color: white;
        }

        .btn-guardar:hover {
            background-color: #2980b9;
        }

        .btn-cancelar {
            background-color: #95a5a6;
            color: white;
        }

        .btn-cancelar:hover {
            background-color: #7f8c8d;
        }

        /* Estilos específicos para el perfil */
        .perfil-info {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .info-grupo {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .info-grupo p {
            margin: 10px 0;
            line-height: 1.6;
            color: #333;
            display: flex;
            align-items: baseline;
        }

        .info-grupo strong {
            color: #2c3e50;
            min-width: 180px;
            display: inline-block;
            position: relative;
        }

        .info-grupo strong::after {
            content: ":";
            position: absolute;
            right: 10px;
        }

        .info-grupo em {
            color: #7f8c8d;
            font-style: italic;
        }

        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin: 25px 0 15px 0;
            font-size: 1.5em;
        }

        /* Estilos específicos para contactos de emergencia */
        .contacto-emergencia {
            border-left: 4px solid #3498db;
            padding: 10px 15px;
            margin: 15px 0;
            background-color: white;
            border-radius: 0 6px 6px 0;
            transition: transform 0.2s ease;
        }

        .contacto-emergencia:hover {
            transform: translateX(5px);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .info-grupo p {
                flex-direction: column;
            }

            .info-grupo strong {
                min-width: unset;
                margin-bottom: 5px;
            }

            .info-grupo strong::after {
                content: none;
            }
        }
    </style>