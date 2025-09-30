<?php
class User_model extends CI_Model {
    public function get_user($numEmpleado) {
        return $this->db->get_where('usuarios', ['NumEmpleado' => $numEmpleado])->row_array();
    }
    
    public function get_all_empleados() {
        $this->db->where('rol', 2); // Solo empleados
        $this->db->order_by('nombre', 'ASC');
        return $this->db->get('usuarios')->result_array();
    }
    
    public function insert_empleado($data) {
        return $this->db->insert('usuarios', $data);
    }
    
    public function update_empleado($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('usuarios', $data);
    }
    
    public function delete_empleado($id) {
        $this->db->where('id', $id);
        $this->db->where('rol', 2); // Solo permitir eliminar empleados, no admins
        return $this->db->delete('usuarios');
    }
    
    public function get_empleado_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('usuarios')->row_array();
    }
    
    public function get_siguiente_numero_empleado() {
        // Obtener todos los números de empleado que son numéricos
        $this->db->select('NumEmpleado');
        $this->db->where('NumEmpleado REGEXP', '^[0-9]+$'); // Solo números
        $this->db->order_by('CAST(NumEmpleado AS UNSIGNED)', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('usuarios');
        
        if ($query->num_rows() > 0) {
            $ultimoNumero = (int)$query->row()->NumEmpleado;
            return $ultimoNumero + 1;
        } else {
            return 1; // Si no hay empleados, empezar con 1
        }
    }
}