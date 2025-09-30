<?php
class Auth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('session');
    }

    public function index() {
        // Si ya está logueado, redirigir al dashboard correspondiente
        if ($this->session->userdata('logged_in')) {
            $role = $this->session->userdata('role');
            
            switch ($role) {
                case 'admin':
                    redirect('rme');
                    break;
                case 'empleado':
                    redirect('empleado/dashboard');
                    break;
                default:
                    redirect('rme'); // Por defecto ir a RME
                    break;
            }
        }
        
        // Si no está logueado, mostrar página de login
        $this->load->view('login');
    }

    public function login() {
        $numEmpleado = $this->input->post('usuario');
        $password = $this->input->post('contrasena');
        $loginType = $this->input->post('login_type'); // 'admin' o 'empleado'

        // Buscar usuario en la base de datos
        $user = $this->User_model->get_user($numEmpleado);

        if ($loginType === 'admin') {
            // Login de Administrador: requiere NumEmpleado + Contraseña
            if ($user && $user['rol'] == 1 && $password === $user['contrasena']) {
                // Login exitoso para admin
                $this->session->set_userdata([
                    'user_id' => $user['id'],
                    'NumEmpleado' => $user['NumEmpleado'],
                    'nombre' => $user['nombre'],
                    'role' => 'admin',
                    'logged_in' => true
                ]);
                redirect('rme');
            } else {
                $this->session->set_flashdata('error', 'Credenciales de administrador incorrectas.');
                redirect('login');
            }
        } elseif ($loginType === 'empleado') {
            // Login de Empleado: solo requiere NumEmpleado
            if ($user && $user['rol'] == 2) {
                // Login exitoso para empleado
                $this->session->set_userdata([
                    'user_id' => $user['id'],
                    'NumEmpleado' => $user['NumEmpleado'],
                    'nombre' => $user['nombre'],
                    'role' => 'empleado',
                    'logged_in' => true
                ]);
                redirect('empleado/dashboard');
            } else {
                $this->session->set_flashdata('error', 'Número de empleado no encontrado.');
                redirect('login');
            }
        } else {
            $this->session->set_flashdata('error', 'Tipo de login no válido.');
            redirect('login');
        }
    }

    public function logout() {
        $this->session->unset_userdata('role');
        $this->session->unset_userdata('logged_in');
        $this->session->sess_destroy();
        redirect('login');
    }
} 