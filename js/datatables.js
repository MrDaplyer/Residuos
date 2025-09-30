function createTable()
{
  var table = $('#dataTable').DataTable(
  {
      dom: 'Blfrtip',
      scrollX: true,
      language: 
      {
      "aria": 
      {
          "sortAscending": "Activar para ordenar la columna de manera ascendente",
          "sortDescending": "Activar para ordenar la columna de manera descendente"
      },

      "buttons": 
      {
          "collection": "Colección",
          "colvis": "Visibilidad",
          "colvisRestore": "Restaurar visibilidad",
          "copy": "Copiar",
          "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
          "copySuccess": 
          {
          "1": "Copiada 1 fila al portapapeles",
          "_": "Copiadas %d fila al portapapeles"
          },
          "copyTitle": "Copiar al portapapeles",
          "csv": "CSV",
          "excel": "Excel",
          "pageLength": 
          {
          "-1": "Mostrar todas las filas",
          "_": "Mostrar %d filas"
          },
          "pdf": "PDF",
          "print": "Imprimir",
      },

      "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
      "infoFiltered": "(filtrado de un total de _MAX_ registros)",
      "infoThousands": ",",
      "lengthMenu": "Mostrar _MENU_ registros",
      "loadingRecords": "Cargando...",
      "paginate": 
      {
          "first": "Primero",
          "last": "Último",
          "next": "Siguiente",
          "previous": "Anterior"
      },
      "processing": "Procesando...",
      "search": "Buscar:",
      "emptyTable": "No hay datos disponibles en la tabla",
      "info": "Mostrando de _START_ al _END_ de  _TOTAL_ registros",
      "zeroRecords": "No se encontraron coincidencias"
      },
      "order": [0, 'desc'],
      "responsive": true, 
      "paging": true,
      "lengthChange": true, 
      "lengthMenu": [[50, 100, 200,  -1], [50, 100, 200, "Todo"]],
      "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"],
  });
}

$(document).ready(function () 
{
  createTable();
});