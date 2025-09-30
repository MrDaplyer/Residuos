<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Residuos extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database(); // Cargar la base de datos
        $this->load->model('Residuo_model');
        $this->load->library('session');
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }
    }

    public function index() {
        $data['title'] = 'Gestión de Residuos';
        $data['page_js'] = 'residuos.js';
        $this->load->view('_layout/header', $data);
        $this->load->view('_layout/sidebar', $data);
        $this->load->view('_layout/topbar', $data);
        $this->load->view('residuos', $data);
        $this->load->view('_layout/footer', $data);
    }

    //  DATA TABLES de RME

    public function list_rme() {
        $list = $this->Residuo_model->get_datatables_rme();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $residuo) {
            $no++;
            $row = array();
            $row[] = $residuo->id;
            $row[] = $residuo->nombre;
            $row[] = $residuo->clave;
            $row[] = $residuo->unidad;
            $row[] = $residuo->almacen;
            $row[] = '<a class="btn btn-sm btn-primary mr-1" href="javascript:void(0)" title="Editar" onclick="edit_residuo(' . "'" . $residuo->id . "'" . ')"><i class="fas fa-edit"></i></a><a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Eliminar" onclick="delete_residuo(' . "'" . $residuo->id . "'" . ')"><i class="fas fa-trash"></i></a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Residuo_model->count_all_rme(),
            "recordsFiltered" => $this->Residuo_model->count_filtered_rme(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    //  DATA TABLES DE PELIGROSOS

    public function list_peligrosos() {
        $list = $this->Residuo_model->get_datatables_peligrosos();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $residuo) {
            $no++;
            $row = array();
            $row[] = $residuo->id;
            $row[] = $residuo->nombre;
            $row[] = $residuo->unidad;
            $row[] = $residuo->crp;
            // Botones de acción con iconos
            $row[] = '<a class="btn btn-sm btn-primary mr-1" href="javascript:void(0)" title="Editar" onclick="edit_peligroso(' . "'" . $residuo->id . "'" . ')"><i class="fas fa-edit"></i></a><a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Eliminar" onclick="delete_peligroso(' . "'" . $residuo->id . "'" . ')"><i class="fas fa-trash"></i></a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Residuo_model->count_all_peligrosos(),
            "recordsFiltered" => $this->Residuo_model->count_filtered_peligrosos(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    //  CRUD

    public function create() {
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'clave' => $this->input->post('clave'),
            'unidad' => $this->input->post('unidad'),
            'almacen' => $this->input->post('almacen')
        );
        
        if ($this->Residuo_model->insert($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Residuo agregado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al agregar el residuo']);
        }
    }

    public function edit($id) {
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'clave' => $this->input->post('clave'),
            'unidad' => $this->input->post('unidad'),
            'almacen' => $this->input->post('almacen')
        );
        
        if ($this->Residuo_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Residuo actualizado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el residuo']);
        }
    }

    public function delete($id) {
        if ($this->Residuo_model->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Residuo eliminado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el residuo']);
        }
    }

    public function get_by_id($id) {
        $residuo = $this->Residuo_model->get_by_id($id);
        echo json_encode($residuo);
    }

    // Métodos para Peligrosos
    public function create_peligroso() {
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'unidad' => $this->input->post('unidad'),
            'crp' => $this->input->post('crp')
        );
        
        if ($this->Residuo_model->insert_peligroso($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Residuo peligroso agregado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al agregar el residuo peligroso']);
        }
    }

    public function edit_peligroso($id) {
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'unidad' => $this->input->post('unidad'),
            'crp' => $this->input->post('crp')
        );
        
        if ($this->Residuo_model->update_peligroso($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Residuo peligroso actualizado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el residuo peligroso']);
        }
    }

    public function delete_peligroso($id) {
        if ($this->Residuo_model->delete_peligroso($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Residuo peligroso eliminado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el residuo peligroso']);
        }
    }

    public function get_peligroso_by_id($id) {
        $residuo = $this->Residuo_model->get_peligroso_by_id($id);
        echo json_encode($residuo);
    }
}