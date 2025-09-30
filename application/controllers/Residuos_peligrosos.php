<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// PHPExcel no requiere use statements, se carga automáticamente

class Residuos_peligrosos extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Residuos_peligrosos_model');
        $this->load->model('Residuo_model');
    }

    public function index()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }
        $data['title'] = 'Bitacora de Generacion (Peligrosos)';
        $data['page_js'] = 'residuos_peligrosos.js';
        $data['fechas_pendientes'] = $this->Residuos_peligrosos_model->get_fechas_ingreso_pendientes();

        $this->load->view('_layout/header', $data);
        $this->load->view('_layout/sidebar', $data);
        $this->load->view('_layout/topbar', $data);
        $this->load->view('residuos_peligrosos', $data);
        $this->load->view('_layout/footer', $data);
    }

    public function get_peligrosos_data_ajax()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }
        $list = $this->Residuos_peligrosos_model->get_registros();
        $data = array();
        foreach ($list as $registro) {
            $row = array();
            $row['id'] = $registro['id'];
            $row['trabajador'] = $registro['trabajador'];
            $row['residuo'] = $registro['residuo'];
            $row['cantidad'] = $registro['cantidad'];
            $row['unidad'] = $registro['unidad'];
            $row['crp'] = $registro['crp'];
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

        $ok = $this->Residuos_peligrosos_model->delete_terminado($id);
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

        $retiros = $this->input->post('retiros');

        error_log('Raw POST: ' . print_r($this->input->post(), true));
        error_log('Retiros array: ' . print_r($retiros, true));

        if (empty($retiros) || !is_array($retiros)) {
            echo json_encode(['status' => 'error', 'message' => 'No se especificaron retiros válidos o los datos están malformados.']);
            exit();
        }

        try {
            $resultado = $this->Residuos_peligrosos_model->procesar_retiros_parciales($retiros, $datos_manifiesto);
        } catch (\Throwable $e) {
            error_log('Exception in procesar_lote: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Error interno: ' . $e->getMessage()]);
            exit();
        }

        if ($resultado) {
            echo json_encode(['status' => 'success', 'message' => '¡Lote procesado con éxito! Las cantidades han sido actualizadas.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Hubo un error al procesar el lote.']);
        }
        
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

        $this->Residuos_peligrosos_model->update_registro($id, $data);

        redirect('residuos_peligrosos?update_success=1');
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

        $this->load->model('Residuos_peligrosos_model');
        $registros = $this->Residuos_peligrosos_model->get_registros_por_fecha($fecha);

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

        $registros = $this->Residuos_peligrosos_model->get_registros_por_rango($fecha_inicio, $fecha_fin);
        echo json_encode(['status' => 'success', 'data' => $registros]);
        exit();
    }

    public function terminados()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('login');
        }
        $data['title'] = 'Residuos Peligrosos Terminados';
        $data['page_js'] = 'residuos_peligrosos_terminados.js';

        // Cargar residuos peligrosos desde la base de datos para el filtro
        $residuos_obj = $this->Residuo_model->get_all_peligrosos();
        $data['residuos_peligrosos'] = [];
        foreach ($residuos_obj as $residuo) {
            $data['residuos_peligrosos'][] = $residuo->nombre;
        }

        // Si no hay datos en la base de datos, usar los hardcodeados como fallback
        if (empty($data['residuos_peligrosos'])) {
            $data['residuos_peligrosos'] = [
                'Agua contaminada con aceite',
                'Basura industrial impregnada con material peligroso',
                'Cubeta impregnada de material peligroso',
                'Cubeta con material peligroso',
                'Lampara fluorescente usada',
                'Balastros usados',
                'Baterias o pilas alcalinas usadas',
                'Metalicos contaminados'
            ];
        }

        $this->load->view('_layout/header', $data);
        $this->load->view('_layout/sidebar', $data);
        $this->load->view('_layout/topbar', $data);
        $this->load->view('residuos_peligrosos_terminados', $data);
        $this->load->view('_layout/footer', $data);
    }

    public function get_peligrosos_terminados_ajax()
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

        $list = $this->Residuos_peligrosos_model->get_registros_terminados($filtros);
        $data = array();
        foreach ($list as $registro) {
            $row = array();
            $row['id'] = $registro['id'];
            $row['trabajador'] = $registro['trabajador'];
            $row['residuo'] = $registro['residuo'];
            $row['cantidad'] = $registro['cantidad'];
            $row['unidad'] = $registro['unidad'];
            $row['crp'] = $registro['crp'];
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
            'fecha_fin'    => $this->input->get('fecha_fin')
        ];

        if (!empty($filtros['residuos']) && is_string($filtros['residuos'])) {
            $filtros['residuos'] = json_decode($filtros['residuos']);
        }
        $filtros = array_filter($filtros);
        
        $registros = $this->Residuos_peligrosos_model->get_registros_terminados($filtros);

        if (!class_exists('PHPExcel_IOFactory')) {
            show_error('PHPExcel no está instalado. Por favor, instala las dependencias con Composer.', 500);
            return;
        }

        $templatePath = $this->find_excel_template('FO-EHS-016 BITÁCORA DE GENERACIÓN RESIDUOS PELIGROSOS.xlsx');
        if (!$templatePath) {
            show_error('No se pudo encontrar la plantilla de Residuos Peligrosos.', 500);
            return;
        }

        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        $sheet = $objPHPExcel->getActiveSheet();
        
        // Obtener el estilo completo de la primera fila con datos
        $templateStyle = $sheet->getStyle('A5:J5');
        
        // Obtener los bordes de toda la tabla de la plantilla
        $templateBorders = $sheet->getStyle('A5:J21')->getBorders();

        $rowIndex = 5;
        foreach ($registros as $registro) {
            $ingreso_date = !empty($registro['ingreso']) ? date('d/m/Y', strtotime($registro['ingreso'])) : '';
            $salida_date = !empty($registro['salida']) ? date('d/m/Y', strtotime($registro['salida'])) : '';
            
            $sheet->setCellValue('A' . $rowIndex, $registro['trabajador']);
            $sheet->setCellValue('B' . $rowIndex, $registro['residuo']);
            $sheet->setCellValue('C' . $rowIndex, $registro['cantidad'] . ' ' . $registro['unidad']);
            $sheet->setCellValue('D' . $rowIndex, $registro['crp']);
            $sheet->setCellValue('E' . $rowIndex, $registro['area_generacion']);
            $sheet->setCellValue('F' . $rowIndex, $ingreso_date);
            $sheet->setCellValue('G' . $rowIndex, $salida_date);
            $sheet->setCellValue('H' . $rowIndex, $registro['fase_siguiente']);
            $sheet->setCellValue('I' . $rowIndex, $registro['destino_razon_social']);
            $sheet->setCellValue('J' . $rowIndex, $registro['manifiesto']);

            // Copiar el estilo de la plantilla y aplicar los bordes
            $newRange = 'A' . $rowIndex . ':J' . $rowIndex;
            $sheet->duplicateStyle($templateStyle, $newRange);

            // Asegurar que los bordes estén presentes
            $sheet->getStyle($newRange)->applyFromArray([
                'borders' => [
                    'allborders' => [
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ],
                    'outline' => [
                        'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);

            $rowIndex++;
        }

        // Aplicar bordes externos a toda la tabla
        $fullRange = 'A5:J' . ($rowIndex - 1);
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

        $extension = pathinfo($templatePath, PATHINFO_EXTENSION);
        $filename = 'Bitacora_Residuos_Peligrosos.' . $extension;
        $contentType = ($extension === 'xls') 
            ? 'application/vnd.ms-excel'
            : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        
        $writerType = ($extension === 'xls') ? 'Excel5' : 'Excel2007';
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $writerType);

        if (ob_get_level()) ob_end_clean();

        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
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
                $registros = $this->Residuos_peligrosos_model->get_registros_por_ids($ids_array);
            } else {
                // Si hay error en la decodificación, obtener todos
                $registros = $this->Residuos_peligrosos_model->get_registros();
            }
        } else {
            // Si no hay IDs seleccionados, obtener todos los registros pendientes
            $registros = $this->Residuos_peligrosos_model->get_registros();
        }

        // Verificar si PHPExcel está disponible
        if (!class_exists('PHPExcel_IOFactory')) {
            show_error('PHPExcel no está instalado. Por favor, instala las dependencias con Composer.', 500);
            return;
        }

        // Buscar la plantilla
        $templatePath = $this->find_excel_template('FO-EHS-016 BITÁCORA DE GENERACIÓN RESIDUOS PELIGROSOS.xlsx');
        
        if (!$templatePath) {
            show_error('No se pudo encontrar la plantilla de Residuos Peligrosos. Verifica que el archivo existe en la carpeta ExcelTemplate.', 500);
            return;
        }

        // Cargar el archivo con PHPExcel
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

        // Llenar datos desde fila 5. Columnas A..J; G = ingreso; H..J extras en blanco (salida, fase, razón social, manifiesto)
        $rowIndex = 5;
        foreach ($registros as $registro) {
            $ingreso_date = !empty($registro['ingreso']) ? date('d/m/Y', strtotime($registro['ingreso'])) : '';

            $sheet->setCellValue('A' . $rowIndex, $registro['trabajador']);
            $sheet->setCellValue('B' . $rowIndex, $registro['residuo']);
            $sheet->setCellValue('C' . $rowIndex, $registro['cantidad'] . ' ' . $registro['unidad']);
            $sheet->setCellValue('D' . $rowIndex, $registro['crp']);
            $sheet->setCellValue('E' . $rowIndex, $registro['area_generacion']);
            $sheet->setCellValue('F' . $rowIndex, $ingreso_date);
            // Columnas adicionales en blanco
            $sheet->setCellValue('G' . $rowIndex, ''); // Fecha salida
            $sheet->setCellValue('H' . $rowIndex, ''); // Fase manejo a la siguiente salida
            $sheet->setCellValue('I' . $rowIndex, ''); // Razón social (destino)
            $sheet->setCellValue('J' . $rowIndex, ''); // Número de manifiesto

            // Copiar el estilo base de la plantilla
            $baseStyle = $sheet->getStyle('A5:J5');
            $sheet->duplicateStyle($baseStyle, 'A' . $rowIndex . ':J' . $rowIndex);

            // Ajustar altura de la fila
            $sheet->getRowDimension($rowIndex)->setRowHeight(30);

            // Aplicar bordes y alineación suaves
            $sheet->getStyle('A' . $rowIndex . ':J' . $rowIndex)->applyFromArray([
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
                    'size' => 10
                ]
            ]);

            // Ajustar anchos de columna si es necesario
            if ($rowIndex === 5) {
                $sheet->getColumnDimension('A')->setWidth(20); // Trabajador
                $sheet->getColumnDimension('B')->setWidth(25); // Residuo
                $sheet->getColumnDimension('C')->setWidth(15); // Cantidad
                $sheet->getColumnDimension('D')->setWidth(15); // CRP
                $sheet->getColumnDimension('E')->setWidth(20); // Area
                $sheet->getColumnDimension('F')->setWidth(12); // Ingreso
                $sheet->getColumnDimension('G')->setWidth(12); // Salida
                $sheet->getColumnDimension('H')->setWidth(20); // Fase
                $sheet->getColumnDimension('I')->setWidth(25); // Destino
                $sheet->getColumnDimension('J')->setWidth(15); // Manifiesto
            }
            $rowIndex++;
        }

        // Crear el writer apropiado
        $pathInfo = pathinfo($templatePath);
        $extension = strtolower($pathInfo['extension']);
        
        if ($extension === 'xls') {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $filename = 'Peligrosos_Pendientes.xls';
            $contentType = 'application/vnd.ms-excel';
        } else {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $filename = 'Peligrosos_Pendientes.xlsx';
            $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        }

        // Limpiar cualquier salida previa
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
        // Lista de posibles ubicaciones para la plantilla
        $possiblePaths = [
            FCPATH . 'ExcelTemplate' . DIRECTORY_SEPARATOR . $templateName,
            FCPATH . 'ExcelTemplate/' . $templateName,
            dirname(FCPATH) . '/ExcelTemplate/' . $templateName,
            __DIR__ . '/../../ExcelTemplate/' . $templateName,
            realpath(FCPATH . 'ExcelTemplate') . DIRECTORY_SEPARATOR . $templateName
        ];

        // Intentar encontrar el archivo
        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_readable($path)) {
                return $path;
            }
        }

        // Si no se encuentra con rutas automáticas, buscar en el directorio
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
                if (stripos($file, 'RESIDUOS PELIGROSOS') !== false && 
                    (stripos($file, '.xlsx') !== false || stripos($file, '.xls') !== false)) {
                    $fullPath = $excelDir . DIRECTORY_SEPARATOR . $file;
                    if (is_readable($fullPath)) {
                        return $fullPath;
                    }
                }
                if (stripos($file, 'FO-EHS-016') !== false && 
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