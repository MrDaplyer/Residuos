<?php   
defined('BASEPATH') OR exit('No direct script access allowed');

class Residuos_peligrosos_model extends CI_Model {
    //---------------------------Para conectar con la base de datos---------------------------- 
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //---------------------------Para guardar el registro----------------------------   
    /**
     * Inserta un nuevo registro de Residuos Peligrosos en la base de datos.
     * @param array $data Los datos del residuo a insertar.
     * @return bool TRUE si la inserción fue exitosa, FALSE en caso contrario.
     */
    public function guardar_registro($data)
    {
        return $this->db->insert('residuos_peligrosos', $data);
    }

    //---------------------------Para obtener los registros---------------------------- 
    /**
     * Obtiene todos los registros de la tabla residuos_peligrosos.
     * @return array Un array con todos los registros.
     */
    public function get_registros()
    {
        $query = $this->db->get('residuos_peligrosos');
        return $query->result_array();
    }

    /**
     * Obtiene registros específicos por sus IDs.
     * @param array $ids Array de IDs a obtener.
     * @return array Un array con los registros solicitados.
     */
    public function get_registros_por_ids($ids)
    {
        if (empty($ids) || !is_array($ids)) {
            return array();
        }
        
        $this->db->where_in('id', $ids);
        $query = $this->db->get('residuos_peligrosos');
        return $query->result_array();
    }

    /**
     * Obtiene todos los registros de una fecha de ingreso específica.
     * @param string $fecha La fecha de ingreso en formato YYYY-MM-DD.
     * @return array Un array con los registros de esa fecha.
     */
    public function get_registros_por_fecha($fecha)
    {
        $this->db->where('DATE(ingreso)', $fecha);
        $query = $this->db->get('residuos_peligrosos');
        return $query->result_array();
    }

    /**
     * Obtiene registros entre un rango de fechas de ingreso (inclusive).
     */
    public function get_registros_por_rango($fecha_inicio, $fecha_fin)
    {
        if ($fecha_inicio) {
            $this->db->where('DATE(ingreso) >=', $fecha_inicio);
        }
        if ($fecha_fin) {
            $this->db->where('DATE(ingreso) <=', $fecha_fin);
        }
        $this->db->order_by('ingreso', 'ASC');
        $query = $this->db->get('residuos_peligrosos');
        return $query->result_array();
    }

    /**
     * Obtiene todas las fechas de ingreso únicas de los registros pendientes.
     * @return array Un array con las fechas de ingreso.
     */
    public function get_fechas_ingreso_pendientes()
    {
        $this->db->select('DATE(ingreso) as fecha_ingreso_unica');
        $this->db->distinct();
        $this->db->from('residuos_peligrosos');
        $this->db->order_by('fecha_ingreso_unica', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Elimina un registro de la tabla residuos_peligrosos_terminados por ID.
     */
    public function delete_terminado($id)
    {
        $this->db->where('id', (int)$id);
        return $this->db->delete('residuos_peligrosos_terminados');
    }

    /**
     * Verifica si hay lotes pendientes de Residuos Peligrosos.
     * @return bool TRUE si hay lotes pendientes, FALSE en caso contrario.
     */
    public function hay_lotes_pendientes()
    {
        $this->db->from('residuos_peligrosos');
        return $this->db->count_all_results() > 0;
    }

    /**
     * Cuenta el número de lotes pendientes (fechas únicas) de Residuos Peligrosos.
     * @return int Número de lotes pendientes.
     */
    public function contar_lotes_pendientes()
    {
        $this->db->select('DATE(ingreso) as fecha_ingreso_unica');
        $this->db->distinct();
        $this->db->from('residuos_peligrosos');
        return $this->db->count_all_results();
    }

    public function get_registros_terminados($filtros = [])
    {
        $this->db->from('residuos_peligrosos_terminados');

        // Filtro por IDs seleccionados (tiene prioridad sobre otros filtros)
        if (!empty($filtros['ids'])) {
            $this->db->where_in('id', $filtros['ids']);
        } else {
            // Filtro por tipo de residuo (array)
            if (!empty($filtros['residuos'])) {
                $this->db->where_in('residuo', $filtros['residuos']);
            }

            // Filtro por rango de fecha de ingreso
            if (!empty($filtros['fecha_inicio'])) {
                $this->db->where('DATE(ingreso) >=', $filtros['fecha_inicio']);
            }
            if (!empty($filtros['fecha_fin'])) {
                $this->db->where('DATE(ingreso) <=', $filtros['fecha_fin']);
            }
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtiene un registro específico por su ID.
     * @param int $id El ID del registro.
     * @return array|null El registro como un array, o null si no se encuentra.
     */
    public function get_registro_by_id($id)
    {
        $query = $this->db->get_where('residuos_peligrosos', array('id' => $id));
        return $query->row_array();
    }

    /**
     * Procesa un lote de registros por fecha, moviéndolos a la tabla de terminados.
     * @param string $fecha_ingreso La fecha de ingreso del lote a procesar.
     * @param array $data Los nuevos datos para los registros (salida, fase, etc.).
     * @return bool TRUE si la operación fue exitosa, FALSE en caso contrario.
     */
    public function procesar_lote_por_fecha($fecha_ingreso, $data)
    {
        // Iniciar transacción
        $this->db->trans_begin();

        // 1. Obtener todos los registros de la tabla original para esa fecha
        $this->db->where('DATE(ingreso)', $fecha_ingreso);
        $registros_a_mover = $this->db->get('residuos_peligrosos')->result_array();

        if (empty($registros_a_mover)) {
            $this->db->trans_rollback();
            return FALSE; // No hay registros para esa fecha
        }

        // 2. Iterar y mover cada registro
        foreach ($registros_a_mover as $registro_original) {
            // Combinar los datos originales con los nuevos
            $registro_completo = array_merge($registro_original, $data);

            // Insertar el registro completo en la tabla de terminados
            $this->db->insert('residuos_peligrosos_terminados', $registro_completo);
        }

        // 3. Eliminar todos los registros de la tabla original para esa fecha
        $this->db->where('DATE(ingreso)', $fecha_ingreso);
        $this->db->delete('residuos_peligrosos');

        // Finalizar transacción
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    //---------------------------Para actualizar el registro----------------------------
    /**
     * Actualiza un registro, lo mueve a la tabla de terminados y lo elimina de la tabla original.
     * @param int $id El ID del registro a actualizar.
     * @param array $data Los nuevos datos para el registro.
     * @return bool TRUE si la operación fue exitosa, FALSE en caso contrario.
     */
    public function update_registro($id, $data)
    {
        // Iniciar transacción
        $this->db->trans_begin();

        // 1. Obtener el registro original completo
        $registro_original = $this->get_registro_by_id($id);

        if (!$registro_original) {
            $this->db->trans_rollback();
            return FALSE;
        }

        // 2. Combinar los datos originales con los nuevos
        $registro_completo = array_merge($registro_original, $data);

        // 3. Insertar el registro completo en la tabla de terminados
        $this->db->insert('residuos_peligrosos_terminados', $registro_completo);

        // 4. Eliminar el registro de la tabla original
        $this->db->where('id', $id);
        $this->db->delete('residuos_peligrosos');

        // Finalizar transacción
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }
        else
        {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    /**
     * Actualiza la cantidad de un registro específico.
     * @param int $id El ID del registro a actualizar.
     * @param float $nueva_cantidad La nueva cantidad para el registro.
     * @return bool
     */
    public function update_cantidad_registro($id, $nueva_cantidad)
    {
        $this->db->where('id', $id);
        return $this->db->update('residuos_peligrosos', ['cantidad' => $nueva_cantidad]);
    }

    /**
     * Procesa retiros parciales de un lote.
     * Mueve la cantidad retirada a la tabla de terminados y actualiza la cantidad restante en la tabla original.
     */
    public function procesar_retiros_parciales($retiros, $datos_manifiesto)
    {
        $this->db->trans_begin();

        foreach ($retiros as $retiro) {
            $id = $retiro['id'];
            $cantidad_retirada = (float)$retiro['cantidad_retirada'];

            error_log('Processing retiro ID: ' . $id . ', cantidad_retirada: ' . $cantidad_retirada);

            // 1. Obtener el registro original
            $registro_original = $this->get_registro_by_id($id);

            if (!$registro_original || $cantidad_retirada <= 0) {
                error_log('Skipping retiro ID: ' . $id . ' - no record or zero cantidad');
                continue; // Si no existe el registro o no se retira nada, saltar
            }
            
            error_log('Registro original: ' . print_r($registro_original, true));
            
            // 2. Crear el registro para la tabla de terminados
            $registro_terminado = $registro_original;
            $registro_terminado['cantidad'] = $cantidad_retirada;
            $registro_terminado = array_merge($registro_terminado, $datos_manifiesto);
            unset($registro_terminado['id']);
            
            error_log('Inserting into terminados: ' . print_r($registro_terminado, true));
            // Insertar en terminados
            $this->db->insert('residuos_peligrosos_terminados', $registro_terminado);

            if ($this->db->affected_rows() == 0) {
                error_log('Insert failed for ID: ' . $id . ' - DB Error: ' . print_r($this->db->error(), true));
            }

            // 3. Actualizar la cantidad en el registro original
            $cantidad_restante = (float)$registro_original['cantidad'] - $cantidad_retirada;
            error_log('Cantidad restante for ID: ' . $id . ' = ' . $cantidad_restante);
            
            if ($cantidad_restante > 0.001) {
                $this->update_cantidad_registro($id, $cantidad_restante);
                if ($this->db->affected_rows() == 0) {
                    error_log('Update cantidad failed for ID: ' . $id . ' - DB Error: ' . print_r($this->db->error(), true));
                }
            } else {
                // Si no queda residuo  eliminamos el registro original
                $this->db->where('id', $id);
                $this->db->delete('residuos_peligrosos');
                if ($this->db->affected_rows() == 0) {
                    error_log('Delete failed for ID: ' . $id . ' - DB Error: ' . print_r($this->db->error(), true));
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }
} 