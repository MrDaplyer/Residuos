<?php
defined('BASEPATH') OR exit('No direct script access allowed');
    //---------------------------Para conectar con la base de datos---------------------------- 
class Rme_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //---------------------------Para guardar el registro----------------------------   
    /**
     * Inserta un nuevo registro de RME en la base de datos.
     * @param array $data Los datos del residuo a insertar.
     * @return bool TRUE si la inserción fue exitosa, FALSE en caso contrario.
     */
    public function guardar_registro($data)
    {
        return $this->db->insert('rme', $data);
    }

    //---------------------------Para obtener los registros---------------------------- 
    /**
     * Obtiene todos los registros de la tabla rme.
     * @return array Un array con todos los registros.
     */
    public function get_registros()
    {
        $query = $this->db->get('rme');
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
        $query = $this->db->get('rme');
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
        $this->db->from('rme');
        $this->db->order_by('fecha_ingreso_unica', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Elimina un registro de la tabla rme_terminados por ID.
     */
    public function delete_terminado($id)
    {
        $this->db->where('id', (int)$id);
        return $this->db->delete('rme_terminados');
    }

    /**
     * Verifica si hay lotes pendientes de RME.
     * @return bool TRUE si hay lotes pendientes, FALSE en caso contrario.
     */
    public function hay_lotes_pendientes()
    {
        $this->db->from('rme');
        return $this->db->count_all_results() > 0;
    }

    /**
     * Cuenta el número de lotes pendientes (fechas únicas) de RME.
     * @return int Número de lotes pendientes.
     */
    public function contar_lotes_pendientes()
    {
        $this->db->select('DATE(ingreso) as fecha_ingreso_unica');
        $this->db->distinct();
        $this->db->from('rme');
        return $this->db->count_all_results();
    }

    public function get_registros_terminados($filtros = [])
    {
        $this->db->from('rme_terminados');

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
        $query = $this->db->get_where('rme', array('id' => $id));
        return $query->row_array();
    }

    /**
     * Obtiene todos los registros de una fecha de ingreso específica.
     * @param string $fecha La fecha de ingreso en formato YYYY-MM-DD.
     * @return array Un array con los registros de esa fecha.
     */
    public function get_registros_por_fecha($fecha)
    {
        $this->db->where('DATE(ingreso)', $fecha);
        $query = $this->db->get('rme');
        return $query->result_array();
    }

    /**
     * Obtiene todos los registros entre un rango de fechas (ingreso) inclusive.
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
        $query = $this->db->get('rme');
        return $query->result_array();
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
        return $this->db->update('rme', ['cantidad' => $nueva_cantidad]);
    }

    //---------------------------Para actualizar el registro----------------------------
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

            // 1. Obtener el registro original
            $registro_original = $this->get_registro_by_id($id);

            if (!$registro_original || $cantidad_retirada <= 0) {
                continue; // Si no existe el registro o no se retira nada, saltar
            }
            
            // 2. Crear el registro para la tabla de terminados
            $registro_terminado = $registro_original;
            $registro_terminado['cantidad'] = $cantidad_retirada; // Solo la cantidad retirada
            $registro_terminado = array_merge($registro_terminado, $datos_manifiesto);
            unset($registro_terminado['id']); // Quitar el ID para que se cree uno nuevo
            
            // Insertar en terminados
            $this->db->insert('rme_terminados', $registro_terminado);

            // 3. Actualizar la cantidad en el registro original
            $cantidad_restante = (float)$registro_original['cantidad'] - $cantidad_retirada;
            
            if ($cantidad_restante > 0.001) { // Usamos un umbral pequeño para evitar problemas con floats
                // Si queda residuo, solo actualizamos la cantidad
                $this->update_cantidad_registro($id, $cantidad_restante);
            } else {
                // Si no queda residuo (o es una cantidad insignificante), eliminamos el registro original
                $this->db->where('id', $id);
                $this->db->delete('rme');
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

        // 2. Combinar los datos originales con los nuevos (los que llena el admin)
        $registro_completo = array_merge($registro_original, $data);

        // 3. Insertar el registro completo en la tabla de terminados
        $this->db->insert('rme_terminados', $registro_completo);

        // 4. Eliminar el registro de la tabla original
        $this->db->where('id', $id);
        $this->db->delete('rme');

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
} 