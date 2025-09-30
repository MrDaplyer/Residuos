<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Residuo_model extends CI_Model {

    // Tabla para RME
    var $table_rme = 'residuos_rme';
    var $column_order_rme = array(null, 'nombre', 'clave', 'unidad', 'almacen', null);
    var $column_search_rme = array('nombre', 'clave');
    var $order_rme = array('id' => 'desc');

    // Tabla para Peligrosos
    var $table_peligrosos = 'peligro_residuos';
    var $column_order_peligrosos = array(null, 'nombre', 'unidad', 'crp', null);
    var $column_search_peligrosos = array('nombre', 'crp');
    var $order_peligrosos = array('id' => 'desc');

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // =========================================================================
    // MÉTODOS PARA DATA TABLES (RME)
    // =========================================================================

    private function _get_datatables_query_rme() {
        $this->db->from($this->table_rme);
        $i = 0;
        foreach ($this->column_search_rme as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search_rme) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order_rme[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order_rme)) {
            $order = $this->order_rme;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables_rme() {
        $this->_get_datatables_query_rme();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_rme() {
        $this->_get_datatables_query_rme();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all_rme() {
        $this->db->from($this->table_rme);
        return $this->db->count_all_results();
    }

    // =========================================================================
    // MÉTODOS PARA DATA TABLES (PELIGROSOS)
    // =========================================================================

    private function _get_datatables_query_peligrosos() {
        $this->db->from($this->table_peligrosos);
        $i = 0;
        foreach ($this->column_search_peligrosos as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search_peligrosos) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order_peligrosos[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order_peligrosos)) {
            $order = $this->order_peligrosos;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables_peligrosos() {
        $this->_get_datatables_query_peligrosos();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_peligrosos() {
        $this->_get_datatables_query_peligrosos();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all_peligrosos() {
        $this->db->from($this->table_peligrosos);
        return $this->db->count_all_results();
    }
    
    // =========================================================================
    // MÉTODOS CRUD (EXISTENTES)
    // =========================================================================
    
    public function get_all() {
        return $this->db->get('residuos_rme')->result();
    }

    public function get_all_peligrosos() {
        return $this->db->get('peligro_residuos')->result();
    }
    
    public function get_by_id($id) {
        return $this->db->get_where('residuos_rme', array('id' => $id))->row();
    }

    public function insert($data) {
        return $this->db->insert('residuos_rme', $data);
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('residuos_rme', $data);
    }

    public function delete($id) {
        return $this->db->delete('residuos_rme', array('id' => $id));
    }

    public function get_peligroso_by_id($id) {
        return $this->db->get_where('peligro_residuos', array('id' => $id))->row();
    }

    public function insert_peligroso($data) {
        return $this->db->insert('peligro_residuos', $data);
    }

    public function update_peligroso($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('peligro_residuos', $data);
    }

    public function delete_peligroso($id) {
        return $this->db->delete('peligro_residuos', array('id' => $id));
    }
}