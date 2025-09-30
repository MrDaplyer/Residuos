<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empleados extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->helper('url');
        
        // Verificar que sea admin logueado PORQUE CASI SIEMPRE NO FUNCIONA
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }
    }

    public function index()
    {
        $data['title'] = 'Gestión de Empleados';
        $data['page_js'] = 'empleados.js';

        $this->load->view('_layout/header', $data);
        $this->load->view('_layout/sidebar', $data);
        $this->load->view('_layout/topbar', $data);
        $this->load->view('empleados/index', $data);
        $this->load->view('_layout/footer', $data);
    }

    public function get_empleados_ajax()
    {
        $empleados = $this->User_model->get_all_empleados();
        $data = array();
        
        foreach ($empleados as $empleado) {
            $row = array();
            $row['id'] = $empleado['id'];
            $row['NumEmpleado'] = $empleado['NumEmpleado'];
            $row['nombre'] = $empleado['nombre'];
            $row['rol'] = $empleado['rol'] == 1 ? 'Administrador' : 'Empleado';
            $row['acciones'] = '
                <button class="btn btn-sm btn-primary mr-1" onclick="editarEmpleado(' . $empleado['id'] . ')" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="eliminarEmpleado(' . $empleado['id'] . ')" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            ';
            $data[] = $row;
        }

        $output = array(
            "data" => $data,
        );

        header('Content-Type: application/json');
        echo json_encode($output);
    }

    public function crear()
    {
        $numEmpleado = $this->input->post('NumEmpleado');
        $nombre = $this->input->post('nombre');
        $esAdmin = $this->input->post('es_admin') ? true : false;
        $contrasena = $this->input->post('contrasena');

        // Valida datos
        if (empty($numEmpleado) || empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'Número de empleado y nombre son requeridos.']);
            return;
        }

        // Verificar que no exista el numero  de empleado
        $existeEmpleado = $this->User_model->get_user($numEmpleado);
        if ($existeEmpleado) {
            echo json_encode(['status' => 'error', 'message' => 'El número de empleado ya existe.']);
            return;
        }

        $data = array(
            'NumEmpleado' => $numEmpleado,
            'nombre' => $nombre,
            'rol' => $esAdmin ? 1 : 2,
            'contrasena' => $esAdmin ? $contrasena : null
        );

        // Validar contraseña para admin
        if ($esAdmin && empty($contrasena)) {
            echo json_encode(['status' => 'error', 'message' => 'La contraseña es requerida para administradores.']);
            return;
        }

        if ($this->User_model->insert_empleado($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Empleado creado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al crear el empleado.']);
        }
    }

    public function editar($id)
    {
        $numEmpleado = $this->input->post('NumEmpleado');
        $nombre = $this->input->post('nombre');
        $esAdmin = $this->input->post('es_admin') ? true : false;
        $contrasena = $this->input->post('contrasena');

        // Validar datos
        if (empty($numEmpleado) || empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'Número de empleado y nombre son requeridos.']);
            return;
        }

        // Verificar que no exista el número de empleado en otro registro
        $existeEmpleado = $this->User_model->get_user($numEmpleado);
        if ($existeEmpleado && $existeEmpleado['id'] != $id) {
            echo json_encode(['status' => 'error', 'message' => 'El número de empleado ya existe en otro registro.']);
            return;
        }

        $data = array(
            'NumEmpleado' => $numEmpleado,
            'nombre' => $nombre,
            'rol' => $esAdmin ? 1 : 2
        );

        // Solo actualizar contraseña si se proporciona y es admin
        if ($esAdmin && !empty($contrasena)) {
            $data['contrasena'] = $contrasena;
        } elseif ($esAdmin && empty($contrasena)) {
            // Si es admin pero no se proporciona contraseña, mantener la actual
            $empleadoActual = $this->User_model->get_empleado_by_id($id);
            $data['contrasena'] = $empleadoActual['contrasena'];
        } else {
            // Si no es admin, quitar contraseña
            $data['contrasena'] = null;
        }

        if ($this->User_model->update_empleado($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Empleado actualizado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el empleado.']);
        }
    }

    public function eliminar($id)
    {
        if ($this->User_model->delete_empleado($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Empleado eliminado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el empleado.']);
        }
    }

    public function obtener($id)
    {
        $empleado = $this->User_model->get_empleado_by_id($id);
        if ($empleado) {
            echo json_encode(['status' => 'success', 'data' => $empleado]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Empleado no encontrado.']);
        }
    }
}