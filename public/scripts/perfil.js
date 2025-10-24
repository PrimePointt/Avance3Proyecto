// Configuración inicial cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    console.log('Inicializando script de perfil...');
    setupEventListeners();
    
    // Configurar el botón de editar si existe
    const btnEditar = document.getElementById('btn-editar');
    if (btnEditar) {
        btnEditar.addEventListener('click', function() {
            const datosActuales = obtenerDatosActuales();
            abrirModal(datosActuales);
        });
    }
});

function obtenerDatosActuales() {
    const datos = {};
    const campos = [
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

    campos.forEach(campo => {
        const elemento = document.querySelector(`[data-campo="${campo}"]`);
        if (elemento) {
            datos[campo] = elemento.textContent.trim();
        }
    });

    return datos;
}

function setupEventListeners() {
    // Configurar el cierre del modal al hacer clic fuera
    window.onclick = (event) => {
        const modal = document.getElementById('modal-editar');
        if (event.target === modal) {
            cerrarModal();
        }
    };

    // Configurar el formulario de edición
    const form = document.getElementById('form-editar');
    if (form) {
        console.log('Configurando formulario de edición...');
        form.addEventListener('submit', handleFormSubmit);
    }

    // Configurar el botón de cerrar modal
    const btnCerrar = document.querySelector('.cerrar-modal');
    if (btnCerrar) {
        btnCerrar.addEventListener('click', cerrarModal);
    }
}

function abrirModal(datosVoluntario) {
    console.log('Abriendo modal con datos:', datosVoluntario);
    const modal = document.getElementById('modal-editar');
    const form = document.getElementById('form-editar');

    if (!modal || !form) {
        console.error('No se encontró el modal o el formulario');
        return;
    }

    try {
        // Llenar el formulario con los datos actuales
        Object.keys(datosVoluntario).forEach(campo => {
            const input = form.querySelector(`[name="${campo}"]`);
            if (input) {
                // Convertir null a cadena vacía
                input.value = datosVoluntario[campo] !== null ? datosVoluntario[campo] : '';
            }
        });

        modal.style.display = 'block';
    } catch (error) {
        console.error('Error al abrir el modal:', error);
        alert('Error al abrir el formulario de edición');
    }
}

function cerrarModal() {
    console.log('Cerrando modal...');
    const modal = document.getElementById('modal-editar');
    if (modal) {
        modal.style.display = 'none';
    }
}

async function handleFormSubmit(e) {
    e.preventDefault();
    console.log('Iniciando envío del formulario...');
    
    // Deshabilitar el botón de submit para evitar doble envío
    const submitButton = e.target.querySelector('button[type="submit"]');
    if (submitButton) submitButton.disabled = true;
    
    try {
        const form = e.target;
        const datos = {
            telefonoCelular: form.querySelector('[name="telefonoCelular"]')?.value?.trim() || '',
            telefonoParticular: form.querySelector('[name="telefonoParticular"]')?.value?.trim() || '',
            telefonoTrabajo: form.querySelector('[name="telefonoTrabajo"]')?.value?.trim() || '',
            ocupacionActual: form.querySelector('[name="ocupacionActual"]')?.value?.trim() || '',
            empresaLabora: form.querySelector('[name="empresaLabora"]')?.value?.trim() || '',
            calle: form.querySelector('[name="calle"]')?.value?.trim() || '',
            numeroExterior: form.querySelector('[name="numeroExterior"]')?.value?.trim() || '',
            colonia: form.querySelector('[name="colonia"]')?.value?.trim() || '',
            codigoPostal: form.querySelector('[name="codigoPostal"]')?.value?.trim() || ''
        };

        console.log('Datos a enviar:', datos);
        
        // Validar datos antes de enviar
        if (!validarDatos(datos)) {
            if (submitButton) submitButton.disabled = false;
            return;
        }
        
        const datosJSON = JSON.stringify(datos);
        console.log('Datos en formato JSON:', datosJSON);
        
        const response = await fetch('../../controllers/actualizar_perfil.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: datosJSON
        });

        console.log('Status de la respuesta:', response.status);

        let resultado;
        try {
            resultado = await response.json();
            console.log('Resultado de la actualización:', resultado);
        } catch (e) {
            console.error('Error al parsear la respuesta JSON:', e);
            const textoError = await response.text();
            console.error('Respuesta del servidor:', textoError);
            throw new Error('Error al procesar la respuesta del servidor');
        }

        if (!response.ok) {
            throw new Error(resultado.message || `Error del servidor: ${response.status}`);
        }

        if (resultado.success) {
            alert(resultado.message || 'Perfil actualizado exitosamente');
            
            // Actualizar la vista sin recargar la página completa
            try {
                const nuevoPerfil = await obtenerPerfilActualizado();
                actualizarVistaPerfilWithoutReload(nuevoPerfil);
                cerrarModal();
            } catch (error) {
                console.error('Error al actualizar la vista:', error);
                window.location.reload(); // Fallback a recarga completa si falla la actualización parcial
            }
        } else {
            throw new Error(resultado.message || 'Error al actualizar el perfil');
        }
    } catch (error) {
        console.error('Error en el proceso de actualización:', error);
        alert(error.message || 'Error al procesar la solicitud');
    }
}

// Función para obtener los datos actualizados del perfil
async function obtenerPerfilActualizado() {
    try {
        const response = await fetch('../../controllers/obtener_perfil.php');
        if (!response.ok) {
            throw new Error('Error al obtener los datos actualizados del perfil');
        }
        return await response.json();
    } catch (error) {
        console.error('Error al obtener perfil actualizado:', error);
        throw error;
    }
}

// Función para actualizar la vista sin recargar
function actualizarVistaPerfilWithoutReload(nuevoPerfil) {
    console.log('Actualizando vista con nuevos datos:', nuevoPerfil);

    try {
        // Actualizar campos de información personal
        const camposPersonales = {
            'telefono-celular': nuevoPerfil.telefonoCelular,
            'telefono-particular': nuevoPerfil.telefonoParticular,
            'telefono-trabajo': nuevoPerfil.telefonoTrabajo,
            'grado-estudios': nuevoPerfil.gradoEstudios,
            'profesion': nuevoPerfil.profesion,
            'ocupacion': nuevoPerfil.ocupacionActual,
            'empresa': nuevoPerfil.empresaLabora,
            'enfermedades': nuevoPerfil.enfermedades,
            'alergias': nuevoPerfil.alergias
        };

        Object.entries(camposPersonales).forEach(([id, valor]) => {
            const elemento = document.querySelector(`[data-campo="${id}"]`);
            if (elemento) {
                elemento.textContent = valor || 'No especificado';
            }
        });

        // Actualizar campos de dirección
        if (nuevoPerfil.direccion) {
            const direccion = nuevoPerfil.direccion;
            const direccionCompleta = [
                direccion.calle,
                direccion.numeroExterior,
                direccion.numeroInterior ? `Int. ${direccion.numeroInterior}` : '',
                direccion.colonia,
                direccion.codigoPostal
            ].filter(Boolean).join(' ');

            const elementoDireccion = document.querySelector('[data-campo="direccion-completa"]');
            if (elementoDireccion) {
                elementoDireccion.textContent = direccionCompleta || 'Dirección no especificada';
            }
        }

        console.log('Vista actualizada exitosamente');
    } catch (error) {
        console.error('Error al actualizar la vista:', error);
        throw error;
    }
}

function validarDatos(datos) {
    console.log('Validando datos:', datos);
    
    // Validar teléfono celular requerido
    if (!datos.telefonoCelular) {
        alert('El teléfono celular es requerido');
        return false;
    }

    // Limpiar y validar formato de teléfono
    const telefonoLimpio = datos.telefonoCelular.replace(/\D/g, '');
    if (telefonoLimpio.length !== 10) {
        alert('El teléfono celular debe tener 10 dígitos');
        return false;
    }
    datos.telefonoCelular = telefonoLimpio;

    // Validar código postal si está presente
    if (datos.codigoPostal) {
        const cpLimpio = datos.codigoPostal.replace(/\D/g, '');
        if (cpLimpio.length !== 5) {
            alert('El código postal debe tener 5 dígitos');
            return false;
        }
        datos.codigoPostal = cpLimpio;
    }

    return true;
}