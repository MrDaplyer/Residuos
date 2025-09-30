<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Areas_generacion extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Areas_generacion_model');
        $this->load->library('session');
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }
    }

    public function index() {
        $data['title'] = 'Gestión de Áreas de Generación';
        $data['page_js'] = 'areas_generacion.js';
        $this->load->view('_layout/header', $data);
        $this->load->view('_layout/sidebar', $data);
        $this->load->view('_layout/topbar', $data);
        $this->load->view('areas_generacion', $data);
        $this->load->view('_layout/footer', $data);
    }

    // =========================================================================
    // MÉTODOS PARA DATA TABLES
    // =========================================================================

    public function list_areas() {
        $list = $this->Areas_generacion_model->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $area) {
            $no++;
            $row = array();
            $row[] = $area->id;
            $row[] = $area->nombre;
            $row[] = $area->activo ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>';
            // Botones de acción con iconos
            $row[] = '<div class="d-flex">
                        <a class="btn btn-sm btn-primary mr-1" href="javascript:void(0)" title="Editar" onclick="edit_area(' . "'" . $area->id . "'" . ')">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Eliminar" onclick="delete_area(' . "'" . $area->id . "'" . ')">
                            <i class="fas fa-trash"></i>
                        </a>
                      </div>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Areas_generacion_model->count_all(),
            "recordsFiltered" => $this->Areas_generacion_model->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    // =========================================================================
    // MÉTODOS CRUD
    // =========================================================================

    public function create() {
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'descripcion' => $this->input->post('descripcion'),
            'activo' => 1
        );
        
        if ($this->Areas_generacion_model->insert($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Área agregada correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al agregar el área']);
        }
    }

    public function edit($id) {
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'descripcion' => $this->input->post('descripcion'),
            'activo' => $this->input->post('activo') ? 1 : 0
        );
        
        if ($this->Areas_generacion_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Área actualizada correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el área']);
        }
    }

    public function delete($id) {
        if ($this->Areas_generacion_model->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Área eliminada correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el área']);
        }
    }

    public function get_by_id($id) {
        $area = $this->Areas_generacion_model->get_by_id($id);
        echo json_encode($area);
    }
}