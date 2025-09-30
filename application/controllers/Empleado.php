<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empleado extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rme_model');
        $this->load->model('Residuos_peligrosos_model');
        $this->load->model('Residuo_model');
        $this->load->model('Areas_generacion_model');
        $this->load->library('session');
        $this->load->helper('url');
        
        // Verificar que sea empleado logueado
        $role = $this->session->userdata('role');
        if (!$this->session->userdata('logged_in') || ($role !== 'empleado' && $role != 2)) {
            redirect('login');
        }
    }

    public function dashboard()
    {
        $data['title'] = 'Seleccionar Tipo de Residuo';
        $data['empleado_nombre'] = $this->session->userdata('nombre');
        $data['empleado_numero'] = $this->session->userdata('NumEmpleado');

        $this->load->view('_layout/header', $data);
        $this->load->view('_layout/sidebar', $data);
        $this->load->view('_layout/topbar', $data);
        $this->load->view('dashboard', $data);
        $this->load->view('_layout/footer', $data);
    }

    public function peligrosos()
    {
        $data['title'] = 'Empleado Peligrosos';
        $data['page_js'] = 'empleado_peligrosos.js';
        $data['empleado_numero'] = $this->session->userdata('NumEmpleado');
        $data['empleado_nombre'] = $this->session->userdata('nombre');

        // Cargar residuos peligrosos desde la base de datos
        $peligrosos_obj = $this->Residuo_model->get_all_peligrosos();
        $data['peligrosos_data'] = [];
        
        foreach ($peligrosos_obj as $residuo) {
            $data['peligrosos_data'][$residuo->nombre] = [
                'unidad' => $residuo->unidad,
                'crp' => $residuo->crp
            ];
        }

        // Si no hay datos en la base de datos, usar los hardcodeados como fallback
        if (empty($data['peligrosos_data'])) {
            $data['peligrosos_data'] = [
                'Agua contaminada con aceite' => ['unidad' => 'KG', 'crp' => 'Te'],
                'Basura industrial impregnada con material peligroso' => ['unidad' => 'KG', 'crp' => 'Te, I'],
                'Cubeta impregnada de material peligroso' => ['unidad' => 'PZA/KG', 'crp' => 'Te, I'],
                'Cubeta con material peligroso' => ['unidad' => 'PZA/KG', 'crp' => 'Te, I'],
                'Lampara fluorescente usada' => ['unidad' => 'PZA/KG', 'crp' => 'Te'],
                'Balastros usados' => ['unidad' => 'PZA/KG', 'crp' => 'Te'],
                'Baterias o pilas alcalinas usadas' => ['unidad' => 'KG', 'crp' => 'Te'],
                'Metalicos contaminados' => ['unidad' => 'PZA/KG', 'crp' => 'Te'],
            ];
        }

        $data['almacen_peligrosos'] = 'ALMACEN TEMPORAL DE RESIDUOS PELIGROSOS';

        // Cargar áreas de generación desde la base de datos
        $areas_obj = $this->Areas_generacion_model->get_all();
        $data['areas_generacion'] = [];
        foreach ($areas_obj as $area) {
            $data['areas_generacion'][] = $area->nombre;
        }

        // Si no hay datos en la base de datos, usar los hardcodeados como fallback
        if (empty($data['areas_generacion'])) {
            $data['areas_generacion'] = [
                'Producción ensamble',
                'Producción moldeo',
                'Mantenimiento',
                'Ingenieria',
                'Calidad',
                'Almacén',
                'Otros'
            ];
        }
        
        // Comprobar si venimos de un guardado exitoso
        if ($this->input->get('success')) {
            $data['success_message'] = '¡Registro guardado correctamente!';
        }

        $this->load->view('_layout/header', $data);
        $this->load->view('_layout/sidebar', $data);
        $this->load->view('_layout/topbar', $data);
        $this->load->view('empleado_peligrosos', $data);
        $this->load->view('_layout/footer', $data);
    }

    public function rme()
    {
        $data['title'] = 'Empleado RME';
        $data['page_js'] = 'empleado_rme.js';
        $data['empleado_numero'] = $this->session->userdata('NumEmpleado');
        $data['empleado_nombre'] = $this->session->userdata('nombre');

        // Cargar residuos desde el modelo y convertir a array
        $residuos_obj = $this->Residuo_model->get_all();
        $data['residuos'] = [];
        foreach ($residuos_obj as $residuo) {
            $data['residuos'][] = [
                'id' => $residuo->id,
                'nombre' => $residuo->nombre,
                'clave' => $residuo->clave,
                'unidad' => $residuo->unidad,
                'almacen' => $residuo->almacen
            ];
        }

        // Datos para autocompletado (para el JS)
        $data['rme_autocomplete_data'] = $data['residuos'];

        // Cargar áreas de generación desde la base de datos
        $areas_obj = $this->Areas_generacion_model->get_all();
        $data['areas_generacion'] = [];
        foreach ($areas_obj as $area) {
            $data['areas_generacion'][] = $area->nombre;
        }

        // Si no hay datos en la base de datos, usar los hardcodeados como fallback
        if (empty($data['areas_generacion'])) {
            $data['areas_generacion'] = [
                'Producción ensamble',
                'Producción moldeo',
                'Mantenimiento',
                'Ingenieria',
                'Calidad',
                'Almacén',
                'Comedor',
                'Otros'
            ];
        }
        
        // Comprobar si venimos de un guardado exitoso
        if ($this->input->get('success')) {
            $data['success_message'] = '¡Registro guardado correctamente!';
        }

        $this->load->view('_layout/header', $data);
        $this->load->view('_layout/sidebar', $data);
        $this->load->view('_layout/topbar', $data);
        $this->load->view('empleado_rme', $data);
        $this->load->view('_layout/footer', $data);
    }

    public function guardar_rme()
    {
        // Extraer solo el primer nombre
        $nombre_completo = $this->session->userdata('nombre');
        $primer_nombre = explode(' ', trim($nombre_completo))[0];
        
        $data = array(
            'trabajador' => $primer_nombre, // Usar solo el primer nombre del empleado
            'residuo' => $this->input->post('residuo'),
            'clave' => $this->input->post('clave'),
            'cantidad' => $this->input->post('cantidad'),
            'unidad' => $this->input->post('unidad'),
            'almacen' => $this->input->post('almacen'),
            'area_generacion' => $this->input->post('area_generacion'),
            'ingreso' => $this->input->post('ingreso')
        );

        $this->Rme_model->guardar_registro($data);

        // Redirigir con un parámetro de éxito en la URL
        redirect('empleado/rme?success=1');
    }

    public function guardar_peligroso()
    {
        // Extraer solo el primer nombre
        $nombre_completo = $this->session->userdata('nombre');
        $primer_nombre = explode(' ', trim($nombre_completo))[0];
        
        $data = array(
            'trabajador' => $primer_nombre, // Usar solo el primer nombre del empleado
            'residuo' => $this->input->post('residuo'),
            'cantidad' => $this->input->post('cantidad'),
            'unidad' => $this->input->post('unidad'),
            'crp' => $this->input->post('crp'),
            'area_generacion' => $this->input->post('area_generacion'),
            'ingreso' => $this->input->post('ingreso')
        );

        $this->Residuos_peligrosos_model->guardar_registro($data);

        // Redirigir con un parámetro de éxito en la URL
        redirect('empleado/peligrosos?success=1');
    }
} 