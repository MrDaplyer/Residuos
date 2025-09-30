# Sistema de Gestión de Residuos

## Requisitos del Sistema

- **PHP**: 5.4.12 o superior (compatible hasta PHP 7.4)
- **MySQL**: 5.6 o superior
- **Servidor Web**: Apache con mod_rewrite
- **Composer**: Para gestión de dependencias

## Instalación

### 1. Clonar/Copiar el proyecto
```bash
# Copiar todos los archivos del proyecto a tu directorio web
```

### 2. Instalar dependencias
```bash
cd ruta/del/proyecto
composer install --no-dev --ignore-platform-reqs
```

### 3. Configurar base de datos
1. Crear base de datos MySQL
2. Importar el archivo SQL (si existe)
3. Configurar `application/config/database.php`:
   ```php
   $db['default']['hostname'] = 'localhost';
   $db['default']['username'] = 'tu_usuario';
   $db['default']['password'] = 'tu_password';
   $db['default']['database'] = 'tu_base_datos';
   ```

### 4. Configurar permisos
- Dar permisos de escritura a `application/logs/`
- Dar permisos de escritura a `application/cache/`

## Credenciales por Defecto

- **Usuario**: admin
- **Contraseña**: hola123

## Funcionalidades

- ✅ Gestión de RME (Residuos de Manejo Especial)
- ✅ Gestión de Residuos Peligrosos
- ✅ Sistema de roles (Admin/Empleados)
- ✅ Exportación a Excel
- ✅ Procesamiento por lotes
- ✅ Retiros parciales

## Solución de Problemas

### Error de PHP/Composer
Si aparecen errores de compatibilidad:
```bash
composer install --ignore-platform-reqs
```

### Error de PHPExcel
Si las exportaciones no funcionan, verificar que PHPExcel esté instalado:
```bash
composer require phpoffice/phpexcel:1.8.2 --ignore-platform-reqs
```

### Error de Base de Datos
Verificar configuración en `application/config/database.php`

## Estructura del Proyecto

```
├── application/          # Aplicación CodeIgniter
│   ├── controllers/     # Controladores MVC
│   ├── models/         # Modelos de datos
│   ├── views/          # Vistas/Templates
│   └── config/         # Configuraciones
├── system/             # Framework CodeIgniter
├── assets/             # CSS, JS, imágenes
├── vendor/             # Dependencias de Composer
├── ExcelTemplate/      # Plantillas de Excel
└── js/                # JavaScript personalizado

## Tecnologías Utilizadas

- **Backend**: CodeIgniter 3, PHP
- **Frontend**: Bootstrap 4, jQuery, DataTables
- **Base de Datos**: MySQL
- **Exportación**: PHPExcel
- **Notificaciones**: SweetAlert2

## DevLog (privado)

- Formato: `[YYYY-MM-DD HH:mm] TAGS :: archivos :: nota`
- Regla: sin errores; solo keywords útiles para mí.

[2025-08-20 12:31] INIT, MAP, TERMINADOS :: application/views/{rme_terminados.php,residuos_peligrosos_terminados.php}, js/{rme_terminados.js,residuos_peligrosos_terminados.js} :: ubicacion OK, listo nueva logica
[2025-08-20 12:32] README :: add DevLog privado :: base format
[2025-08-20 12:45] TERMINADOS, UI :: add columna Acciones (3 puntos) :: application/views/{rme_terminados.php,residuos_peligrosos_terminados.php}
[2025-08-20 12:46] TERMINADOS, JS, CONFIRM :: menu eliminar + SweetAlert + AJAX :: js/{rme_terminados.js,residuos_peligrosos_terminados.js}
[2025-08-20 12:47] TERMINADOS, API :: endpoints eliminar_terminado (admin only) :: application/controllers/{Rme.php,Residuos_peligrosos.php}
[2025-08-20 12:48] TERMINADOS, MODEL :: delete_terminado() :: application/models/{Rme_model.php,Residuos_peligrosos_model.php}
[2025-08-20 13:02] TERMINADOS, UI :: mover columna Acciones (⋮) a la izquierda de Trabajador; header vacío y angosto; excluir de export :: application/views/{rme_terminados.php,residuos_peligrosos_terminados.php}, js/{rme_terminados.js,residuos_peligrosos_terminados.js}
[2025-08-20 13:02] TERMINADOS, BUGFIX :: agregar Rme_model::delete_terminado() para 500 en eliminar :: application/models/Rme_model.php
[2025-08-20 13:11] LOTES, UI+JS :: agregar columna "No Retirar" con checkbox en modal editable; al marcar pone 0, deshabilita input y actualiza restante :: application/views/{rme.php,residuos_peligrosos.php}, js/{rme.js,residuos_peligrosos.js}
[2025-08-20 13:21] LOTES, VALIDACION :: bloquear finalización si todos los retiros son 0; mostrar error :: js/{rme.js,residuos_peligrosos.js}
[2025-08-20 15:30] LOTES, UX, PELIGROSOS :: replicar "lote personalizado" (botón finalizar + contador, limpiar, tarjetas clickeables con data-fecha, modal selector por residuo, buffer global, finalizar usando modal existente) :: application/views/residuos_peligrosos.php, js/residuos_peligrosos.js
[2025-08-20 15:31] LOTES, UI, HOVER :: agregar hover sutil en tarjetas de Peligrosos condicionado al modo personalizado (clase body personalizado-on) :: application/views/residuos_peligrosos.php, js/residuos_peligrosos.js