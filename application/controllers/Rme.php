<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// PHPExcel no requiere use statements, se carga automáticamente

class Rme extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rme_model');
        $this->load->model('Residuo_model');
    }

    public function index()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }

        $data['title'] = 'Bitacora de Generacion (RME)';
        $data['page_js'] = 'rme.js';
        $data['fechas_pendientes'] = $this->Rme_model->get_fechas_ingreso_pendientes();

        $this->load->view('_layout/header', $data);
        $this->load->view('_layout/sidebar', $data);
        $this->load->view('_layout/topbar', $data);
        $this->load->view('rme', $data);
        $this->load->view('_layout/footer', $data);
    }

    public function get_rme_data_ajax()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }

        $list = $this->Rme_model->get_registros();
        $data = array();
        foreach ($list as $registro) {
            $row = array();
            $row['id'] = $registro['id'];
            $row['trabajador'] = $registro['trabajador'];
            $row['residuo'] = $registro['residuo'];
            $row['clave'] = $registro['clave'];
            $row['cantidad'] = $registro['cantidad'];
            $row['unidad'] = $registro['unidad'];
            $row['almacen'] = $registro['almacen'];
            $row['area_generacion'] = $registro['area_generacion'];
            $row['ingreso'] = date("d/m/Y", strtotime($registro['ingreso'])); 

            $data[] = $row;
        }

        $output = array(
            "data" => $data,
        );

        header('Content-Type: application/json');
        echo json_encode($output);
    }

    public function eliminar_terminado()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }

        header('Content-Type: application/json');
        $id = $this->input->post('id');
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            return;
        }

        $ok = $this->Rme_model->delete_terminado($id);
        if ($ok) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar']);
        }
    }

    public function procesar_lote()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }

        header('Content-Type: application/json');
        
        $datos_manifiesto = array(
            'salida' => $this->input->post('salida'),
            'fase_siguiente' => $this->input->post('fase_siguiente'),
            'destino_razon_social' => $this->input->post('destino_razon_social'),
            'manifiesto' => $this->input->post('manifiesto')
        );

        // Los datos de retiros ya vienen como un array gracias a como FormData los estructura.
        // No es necesario decodificar desde JSON.
        $retiros = $this->input->post('retiros');

        if (empty($retiros) || !is_array($retiros)) {
            echo json_encode(['status' => 'error', 'message' => 'No se especificaron retiros válidos o los datos están malformados.']);
            exit();
        }

        $resultado = $this->Rme_model->procesar_retiros_parciales($retiros, $datos_manifiesto);

        if ($resultado) {
            echo json_encode(['status' => 'success', 'message' => '¡Lote procesado con éxito! Las cantidades han sido actualizadas.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Hubo un error al procesar el lote.']);
        }
        
        exit();
    }

    public function get_registros_por_fecha_ajax()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }

        header('Content-Type: application/json');
        $fecha = $this->input->post('fecha');

        if (!$fecha) {
            echo json_encode(['status' => 'error', 'message' => 'Fecha no proporcionada.']);
            exit();
        }

        $this->load->model('Rme_model');
        $registros = $this->Rme_model->get_registros_por_fecha($fecha);

        echo json_encode(['status' => 'success', 'data' => $registros]);
        exit();
    }

    public function get_registros_por_rango_ajax()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }

        header('Content-Type: application/json');
        $fecha_inicio = $this->input->post('fecha_inicio');
        $fecha_fin = $this->input->post('fecha_fin');

        if (!$fecha_inicio && !$fecha_fin) {
            echo json_encode(['status' => 'error', 'message' => 'Debe proporcionar al menos una fecha de inicio o fin.']);
            exit();
        }

        $this->load->model('Rme_model');
        $registros = $this->Rme_model->get_registros_por_rango($fecha_inicio, $fecha_fin);

        echo json_encode(['status' => 'success', 'data' => $registros]);
        exit();
    }

    public function update_registro()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }

        $id = $this->input->post('id');
        $data = array(
            'salida' => $this->input->post('salida'),
            'fase_siguiente' => $this->input->post('fase_siguiente'),
            'destino_razon_social' => $this->input->post('destino_razon_social'),
            'manifiesto' => $this->input->post('manifiesto')
        );

        $this->Rme_model->update_registro($id, $data);

        redirect('rme?update_success=1');
    }

    public function terminados()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }

        $data['title'] = 'RME Terminados';
        $data['page_js'] = 'rme_terminados.js';

        // Cargar residuos RME desde la base de datos para el filtro
        $residuos_obj = $this->Residuo_model->get_all();
        $data['residuos_rme'] = [];
        foreach ($residuos_obj as $residuo) {
            $data['residuos_rme'][] = $residuo->nombre;
        }

        // Si no hay datos en la base de datos, usar los hardcodeados como fallback
        if (empty($data['residuos_rme'])) {
            $data['residuos_rme'] = [
                'Residuos de plástico rígido como PET, HDPE, PVC, PP (Carretes)',
                'Virutas y rebabas de plástico (resinas, sobrantes)',
                'Envases de papel y cartón (Cartón)',
                'Envases de madera (Madera/tarimas)',
                'Metal ferroso (Chatarra)',
                'Metal no ferroso (Aluminio, cobre)',
                'Cobre, cableado',
                'PET (envases plásticos de bebidas)',
                'Basura común (oficinas, sanitarios, comedores)'
            ];
        }

        $this->load->view('_layout/header', $data);
        $this->load->view('_layout/sidebar', $data);
        $this->load->view('_layout/topbar', $data);
        $this->load->view('rme_terminados', $data);
        $this->load->view('_layout/footer', $data);
    }

    public function get_rme_terminados_ajax()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }

        // Recoger filtros de la petición
        $filtros = [
            'residuos'     => $this->input->post('residuos'),
            'fecha_inicio' => $this->input->post('fecha_inicio'),
            'fecha_fin'    => $this->input->post('fecha_fin')
        ];
        
        // Limpiar filtros vacíos
        $filtros = array_filter($filtros);

        $list = $this->Rme_model->get_registros_terminados($filtros);
        $data = array();
        foreach ($list as $registro) {
            $row = array();
            $row['id'] = $registro['id'];
            $row['trabajador'] = $registro['trabajador'];
            $row['residuo'] = $registro['residuo'];
            $row['clave'] = $registro['clave'];
            $row['cantidad'] = $registro['cantidad'];
            $row['unidad'] = $registro['unidad'];
            $row['almacen'] = $registro['almacen'];
            $row['area_generacion'] = $registro['area_generacion'];
            $row['ingreso'] = !empty($registro['ingreso']) ? date('d/m/Y', strtotime($registro['ingreso'])) : '';
            $row['salida'] = !empty($registro['salida']) ? date('d/m/Y', strtotime($registro['salida'])) : '';
            $row['fase_siguiente'] = $registro['fase_siguiente'];
            $row['destino_razon_social'] = $registro['destino_razon_social'];
            $row['manifiesto'] = $registro['manifiesto'];

            $data[] = $row;
        }

        $output = array(
            "data" => $data,
        );

        header('Content-Type: application/json');
        echo json_encode($output);
    }

    public function exportar_excel()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }

        $filtros = [
            'residuos'     => $this->input->get('residuos'),
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin'    => $this->input->get('fecha_fin'),
            'start'        => $this->input->get('start'),
            'length'       => $this->input->get('length')
        ];

        if (!empty($filtros['residuos']) && is_string($filtros['residuos'])) {
            $filtros['residuos'] = json_decode($filtros['residuos']);
        }
        $filtros = array_filter($filtros);
        
        $registros = $this->Rme_model->get_registros_terminados($filtros);

        $start = isset($filtros['start']) ? (int)$filtros['start'] : 0;
        $length = isset($filtros['length']) ? (int)$filtros['length'] : 19;
        $registros = array_slice($registros, $start, $length);

        if (!class_exists('PHPExcel_IOFactory')) {
            show_error('PHPExcel no está instalado. Por favor, instala las dependencias con Composer.', 500);
            return;
        }

        $templatePath = $this->find_excel_template('FO-EHS-018 BITÁCORA DE GENERACIÓN RME.xls');
        
        if (!$templatePath) {
            show_error('No se pudo encontrar la plantilla de RME. Verifica que el archivo existe en la carpeta ExcelTemplate.', 500);
            return;
        }

        if (!file_exists($templatePath)) {
            log_message('error', 'Template file not found: ' . $templatePath);
            show_error('Error: The template file could not be found. Path: ' . $templatePath, 500);
            return;
        }
        
        //PHPExcel
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        $sheet = $objPHPExcel->getActiveSheet();
        
        // Obtener estilos base de la plantilla
        $baseStyle = $sheet->getStyle('A5:K5');

        // los datos para el excel en RME - nuevo formato
        $rowIndex = 5; // Empezar desde la fila 5
        
        // Guardar el contenido de A22 y A23 antes de sobrescribir
        $contenidoA22 = $sheet->getCell('A22')->getValue();
        $contenidoA23 = $sheet->getCell('A23')->getValue();
        
        foreach ($registros as $registro) {
            $ingreso_date = !empty($registro['ingreso']) ? date('d/m/Y', strtotime($registro['ingreso'])) : '';
            $salida_date = !empty($registro['salida']) ? date('d/m/Y', strtotime($registro['salida'])) : '';
            
            // Nuevo formato según especificaciones A5 hasta K5 - CORREGIDO: Cantidad en C, Clave en D
            $sheet->setCellValue('A' . $rowIndex, $registro['trabajador']); // Trabajador
            $sheet->setCellValue('B' . $rowIndex, $registro['residuo']); // Nombre de residuo
            $sheet->setCellValue('C' . $rowIndex, $registro['cantidad']); // Cantidad generada sin unidad
            $sheet->setCellValue('D' . $rowIndex, $registro['clave']); // Clave
            $sheet->setCellValue('E' . $rowIndex, $registro['almacen']); // Almacén
            $sheet->setCellValue('F' . $rowIndex, $registro['area_generacion']); // Área de generación
            $sheet->setCellValue('G' . $rowIndex, $ingreso_date); // Fecha ingreso
            $sheet->setCellValue('H' . $rowIndex, $salida_date); // Fecha salida
            $sheet->setCellValue('I' . $rowIndex, $registro['fase_siguiente']); // Fase siguiente
            $sheet->setCellValue('J' . $rowIndex, $registro['destino_razon_social']); // Razón social
            $sheet->setCellValue('K' . $rowIndex, $registro['manifiesto']); // Num. Manifiesto
            
            // Aplicar estilos consistentes
            $newRange = 'A' . $rowIndex . ':K' . $rowIndex;
            
            // Aplicar estilos con bordes, alineación y fuente adecuada
            $sheet->getStyle($newRange)->applyFromArray([
                'borders' => [
                    'allborders' => [
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => ['rgb' => '808080']
                    ]
                ],
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'wrap' => true
                ],
                'font' => [
                    'bold' => false,
                    'size' => 10,
                    'color' => ['rgb' => '000000']
                ]
            ]);
            
            // Ajustar altura de la fila
            $sheet->getRowDimension($rowIndex)->setRowHeight(30);
            
            $rowIndex++;
        }

        // Añadir las filas especiales al final de los datos
        // Primera fila especial: "Tara bote azul"
        $sheet->mergeCells('A' . $rowIndex . ':B' . $rowIndex);
        $sheet->setCellValue('A' . $rowIndex, 'Tara bote azul');
        $sheet->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray([
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'size' => 10,
                'bold' => false,
                'color' => ['rgb' => '000000']
            ]
        ]);
        $sheet->getRowDimension($rowIndex)->setRowHeight(30);
        
        // Segunda fila especial: "4.3"
        $rowIndex++;
        $sheet->mergeCells('A' . $rowIndex . ':B' . $rowIndex);
        $sheet->setCellValue('A' . $rowIndex, '4.3');
        $sheet->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray([
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'size' => 10,
                'bold' => false,
                'color' => ['rgb' => '000000']
            ]
        ]);
        $sheet->getRowDimension($rowIndex)->setRowHeight(30);

        $pathInfo = pathinfo($templatePath);
        $extension = strtolower($pathInfo['extension']);
        
        if ($extension === 'xls') {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $filename = 'RME_Terminados.xls';
            $contentType = 'application/vnd.ms-excel';
        } else {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $filename = 'RME_Terminados.xlsx';
            $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        }

        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        
        $objWriter->save('php://output');
        exit();
    }

    public function exportar_excel_pendientes()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }

        // Verificar si se enviaron IDs específicos
        $ids_seleccionados = $this->input->get('ids');
        
        if ($ids_seleccionados) {
            // Decodificar los IDs seleccionados
            $ids_array = json_decode($ids_seleccionados, true);
            if (is_array($ids_array) && !empty($ids_array)) {
                // Obtener solo los registros seleccionados
                $registros = $this->Rme_model->get_registros_por_ids($ids_array);
            } else {
                // Si hay error en la decodificación, obtener todos
                $registros = $this->Rme_model->get_registros();
            }
        } else {
            // Si no hay IDs seleccionados, obtener todos los registros pendientes
            $registros = $this->Rme_model->get_registros();
        }

        if (!class_exists('PHPExcel_IOFactory')) {
            show_error('PHPExcel no está instalado. Por favor, instala las dependencias con Composer.', 500);
            return;
        }

        $templatePath = $this->find_excel_template('FO-EHS-018 BITÁCORA DE GENERACIÓN RME.xls');
        
        if (!$templatePath) {
            show_error('No se pudo encontrar la plantilla de RME. Verifica que el archivo existe en la carpeta ExcelTemplate.', 500);
            return;
        }

        if (!file_exists($templatePath)) {
            log_message('error', 'Template file not found: ' . $templatePath);
            show_error('Error: The template file could not be found. Path: ' . $templatePath, 500);
            return;
        }
        
        // PHPExcel
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        $sheet = $objPHPExcel->getActiveSheet();
        
        // Estilo para los datos
        $dataStyle = array(
            'font' => array(
                'color' => array('rgb' => '000000'),
                'size' => 10,
                'bold' => false
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        );

        // Llenar datos: columnas A..K, con 4 adicionales en blanco (H..K cuando aplique)
        $rowIndex = 5; // Empezar desde la fila 5
        
        // Guardar el contenido de A22 y A23 antes de sobrescribir
        $contenidoA22 = $sheet->getCell('A22')->getValue();
        $contenidoA23 = $sheet->getCell('A23')->getValue();
        
        foreach ($registros as $registro) {
            $ingreso_date = !empty($registro['ingreso']) ? date('d/m/Y', strtotime($registro['ingreso'])) : '';

            $sheet->setCellValue('A' . $rowIndex, $registro['trabajador']);
            $sheet->setCellValue('B' . $rowIndex, $registro['residuo']);
            $sheet->setCellValue('C' . $rowIndex, $registro['cantidad'] . ' ' . $registro['unidad']);
            $sheet->setCellValue('D' . $rowIndex, $registro['clave']);
            $sheet->setCellValue('E' . $rowIndex, $registro['almacen']);
            $sheet->setCellValue('F' . $rowIndex, $registro['area_generacion']);
            $sheet->setCellValue('G' . $rowIndex, $ingreso_date);
            // Columnas extra requeridas, sin datos en pendientes
            $sheet->setCellValue('H' . $rowIndex, ''); // Fecha salida
            $sheet->setCellValue('I' . $rowIndex, ''); // Fase manejo a la siguiente salida
            $sheet->setCellValue('J' . $rowIndex, ''); // Razón social (destino)
            $sheet->setCellValue('K' . $rowIndex, ''); // Número de manifiesto

            // Copiar el estilo base de la plantilla
            $baseStyle = $sheet->getStyle('A5:K5');
            $sheet->duplicateStyle($baseStyle, 'A' . $rowIndex . ':K' . $rowIndex);

            // Ajustar altura de la fila
            $sheet->getRowDimension($rowIndex)->setRowHeight(30);

            // Aplicar bordes y alineación suaves
            $sheet->getStyle('A' . $rowIndex . ':K' . $rowIndex)->applyFromArray([
                'borders' => [
                    'allborders' => [
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => ['rgb' => '808080']
                    ]
                ],
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    'wrap' => true
                ],
                'font' => [
                    'bold' => false,
                    'size' => 10,
                    'color' => ['rgb' => '000000']
                ]
            ]);

            // Ajustar anchos de columna si es necesario
            if ($rowIndex === 5) {
                $sheet->getColumnDimension('A')->setWidth(20); // Trabajador
                $sheet->getColumnDimension('B')->setWidth(25); // Residuo
                $sheet->getColumnDimension('C')->setWidth(15); // Cantidade
                $sheet->getColumnDimension('D')->setWidth(15); // Clave
                $sheet->getColumnDimension('E')->setWidth(15); // Almacén
                $sheet->getColumnDimension('F')->setWidth(20); // Área
                $sheet->getColumnDimension('G')->setWidth(12); // Ingreso
                $sheet->getColumnDimension('H')->setWidth(12); // Salida
                $sheet->getColumnDimension('I')->setWidth(20); // Fase
                $sheet->getColumnDimension('J')->setWidth(25); // Destino
                $sheet->getColumnDimension('K')->setWidth(15); // Manifiesto
            }
            $rowIndex++;
        }

        // Aplicar bordes externos a toda la tabla de datos
        $fullRange = 'A5:K' . ($rowIndex - 1);
        $sheet->getStyle($fullRange)->applyFromArray([
            'borders' => [
                'outline' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Asegurar que todas las filas tengan altura adecuada
        for ($i = 5; $i < $rowIndex; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(30);
        }

        // Añadir las filas especiales al final de los datos
        // Definir estilo común
        $commonStyle = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'font' => array(
                'size' => 10,
                'bold' => false,
                'color' => array('rgb' => '000000')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );

        // Agregar filas especiales con verificación
        $specialRows = array(
            array('text' => 'Tara bote azul', 'value_check' => false),
            array('text' => '4.3', 'value_check' => false)
        );

        foreach ($specialRows as $index => $specialRow) {
            $currentRow = $rowIndex + $index;
            
            // Limpiar la fila actual
            foreach (range('A', 'K') as $col) {
                $sheet->setCellValue($col . $currentRow, '');
            }

            // Intentar deshacer merge si existe
            try {
                $sheet->unmergeCells('A' . $currentRow . ':B' . $currentRow);
            } catch (Exception $e) {
                // Ignorar error si no había merge
            }

            // Establecer el valor y verificar
            $sheet->setCellValue('A' . $currentRow, $specialRow['text']);
            $sheet->mergeCells('A' . $currentRow . ':B' . $currentRow);

            // Aplicar estilo
            $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->applyFromArray(array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'font' => array(
                    'size' => 10,
                    'bold' => false,
                    'color' => array('rgb' => '000000')
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    )
                )
            ));

            // Establecer altura de fila
            $sheet->getRowDimension($currentRow)->setRowHeight(30);

            // Verificar que el valor se estableció correctamente
            $actualValue = $sheet->getCell('A' . $currentRow)->getValue();
            error_log('Fila ' . $currentRow . ' - Valor esperado: ' . $specialRow['text'] . ' - Valor actual: ' . $actualValue);

            // Si el valor no se estableció, intentar nuevamente
            if ($actualValue !== $specialRow['text']) {
                $sheet->setCellValue('A' . $currentRow, $specialRow['text']);
                error_log('Reintento de establecer valor en fila ' . $currentRow);
            }
        }

        // Actualizar el índice de fila final
        $rowIndex = $rowIndex + count($specialRows);

        // Limpiar la siguiente fila para el contenido especial
        foreach (range('A', 'K') as $col) {
            $sheet->setCellValue($col . $rowIndex, '');
        }
        
        // Copiar el contenido de A22 y extenderlo a través de todas las columnas
        $sheet->setCellValue('A' . $rowIndex, $contenidoA22);
        $mergeRange = 'A' . $rowIndex . ':K' . $rowIndex;
        if (!$sheet->mergeCells($mergeRange)) {
            $sheet->mergeCells($mergeRange);
        }
        $sheet->getStyle('A' . $rowIndex)->applyFromArray([
            'font' => [
                'size' => 10,
                'bold' => false,
                'color' => ['rgb' => '000000']
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension($rowIndex)->setRowHeight(30);
        $rowIndex++;
        
        // Copiar el contenido de A23 y extenderlo a través de todas las columnas
        $sheet->setCellValue('A' . $rowIndex, $contenidoA23);
        $sheet->mergeCells('A' . $rowIndex . ':K' . $rowIndex);
        $sheet->getStyle('A' . $rowIndex)->applyFromArray([
            'font' => [
                'size' => 10,
                'bold' => false,
                'color' => ['rgb' => '000000']
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension($rowIndex)->setRowHeight(30);

        $pathInfo = pathinfo($templatePath);
        $extension = strtolower($pathInfo['extension']);
        
        if ($extension === 'xls') {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $filename = 'RME_Pendientes.xls';
            $contentType = 'application/vnd.ms-excel';
        } else {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $filename = 'RME_Pendientes.xlsx';
            $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        }

        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        
        $objWriter->save('php://output');
        exit();
    }

    private function find_excel_template($templateName)
    {
        $possiblePaths = [
            FCPATH . 'ExcelTemplate' . DIRECTORY_SEPARATOR . $templateName,
            FCPATH . 'ExcelTemplate/' . $templateName,
            dirname(FCPATH) . '/ExcelTemplate/' . $templateName,
            __DIR__ . '/../../ExcelTemplate/' . $templateName,
            realpath(FCPATH . 'ExcelTemplate') . DIRECTORY_SEPARATOR . $templateName
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_readable($path)) {
                return $path;
            }
        }

        $excelDir = FCPATH . 'ExcelTemplate';
        if (is_dir($excelDir)) {
            $files = scandir($excelDir);
            foreach ($files as $file) {
                if ($file === $templateName) {
                    $fullPath = $excelDir . DIRECTORY_SEPARATOR . $file;
                    if (is_readable($fullPath)) {
                        return $fullPath;
                    }
                }
            }
        }

        if (is_dir($excelDir)) {
            $files = scandir($excelDir);
            foreach ($files as $file) {
                if (stripos($file, 'RME') !== false && 
                    (stripos($file, '.xlsx') !== false || stripos($file, '.xls') !== false)) {
                    $fullPath = $excelDir . DIRECTORY_SEPARATOR . $file;
                    if (is_readable($fullPath)) {
                        return $fullPath;
                    }
                }
                if (stripos($file, 'FO-EHS-018') !== false && 
                    (stripos($file, '.xlsx') !== false || stripos($file, '.xls') !== false)) {
                    $fullPath = $excelDir . DIRECTORY_SEPARATOR . $file;
                    if (is_readable($fullPath)) {
                        return $fullPath;
                    }
                }
            }
        }

        return false;
    }
}