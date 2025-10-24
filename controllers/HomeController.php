<?php
class HomeController
{
    private $user_role;
    private $base_url = '/ProyectoSGV/';
    
    // Definir los roles y sus niveles de acceso
    private $roles = [
        'Voluntario' => 1,
        'Coordinador de Area' => 2,
        'Administrador' => 3,
        'Superadministrador' => 4
    ];

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['voluntarioId']) || !isset($_SESSION['user'])) {
            header('Location: ' . $this->base_url . 'login.php');
            exit();
        }
        
        // Obtener el rol del usuario de la sesión
        $this->user_role = isset($_SESSION['user']['rol']) ? $_SESSION['user']['rol'] : 'Voluntario';
        
        // Registrar información de depuración
        error_log('Estado de la sesión en HomeController: ' . print_r($_SESSION, true));
    }

    private function tienePermiso($nivelMinimo)
    {
        return $this->roles[$this->user_role] >= $nivelMinimo;
    }

    public function index()
    {
        $titulo_pagina = "Inicio RVS | Red de Voluntarios";

        $styles = ['/ProyectoSGV/public/css/i.style.css'];

        $scripts = ['/ProyectoSGV/public/scripts/i.script.js']; 

        require_once "views/layout/header.php";

        require_once "views/home/index.php";

        require_once "views/layout/footer.php";
    }

    public function especialidades()
    {
        // Coordinador o superior puede gestionar
        $ver_cont_gest = $this->tienePermiso(2);

        // Coordinador o superior puede editar
        $ver_card_edit = $this->tienePermiso(2);
        
        $titulo_pagina = "Especialidades | Red de Voluntarios";

        $styles = ['/ProyectoSGV/public/css/e.style.css'];

        $scripts = ['/ProyectoSGV/public/scripts/e.script.js']; 
        
        require_once "views/layout/header.php";

        require_once "views/home/especialidades.php";

        require_once "views/layout/footer.php";
    }
    
    public function tramites()
    {
        // Coordinador o superior puede gestionar
        $ver_cont_gest = $this->tienePermiso(2);

        // Coordinador o superior puede editar
        $ver_card_edit = $this->tienePermiso(2);

        $titulo_pagina = "Trámites | Red de Voluntarios";

        $styles = ['/ProyectoSGV/public/css/t.style.css']; 

        $scripts = ['/ProyectoSGV/public/scripts/t.script.js']; 

        require_once "views/layout/header.php";

        require_once "views/home/tramites.php";

        require_once "views/layout/footer.php";
    }

        public function documentacion()
    {
        // Coordinador o superior puede gestionar
        $ver_cont_gest = $this->tienePermiso(2);

        // Coordinador o superior puede editar
        $ver_card_edit = $this->tienePermiso(2);

        $titulo_pagina = "Documentación | Red de Voluntarios";

        $styles = ['/ProyectoSGV/public/css/d.style.css']; 

        $scripts = ['/ProyectoSGV/public/scripts/d.script.js'];

        require_once "views/layout/header.php";

        require_once "views/home/documentacion.php";

        require_once "views/layout/footer.php";
    }
        public function notificaciones()
    {
        $titulo_pagina = "Notificaciones | Red de Voluntarios";

        $styles = ['/ProyectoSGV/public/css/n.style.css'];

        $scripts = ['/ProyectoSGV/public/scripts/n.script.js'];

        require_once "views/layout/header.php";

        require_once "views/home/notificaciones.php";

        require_once "views/layout/footer.php";
    } 

        public function perfil()
    {
        $titulo_pagina = "Mi Perfil | Red de Voluntarios";

        $styles = ['/ProyectoSGV/public/css/p.style.css']; 

        $scripts = [
            '/ProyectoSGV/public/scripts/p.script.js',
            '/ProyectoSGV/public/scripts/perfil.js'
        ]; 

        require_once "views/layout/header.php";

        require_once "views/home/perfil.php";

        require_once "views/layout/footer.php";
    }
}
?>