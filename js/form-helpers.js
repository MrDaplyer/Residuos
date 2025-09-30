document.addEventListener("DOMContentLoaded", function() {
    const dateInput = document.getElementById('ingreso');
    //La logica para la fecha
    if (dateInput) {
        const today = new Date();

        const year = today.getFullYear();//consigue el año
        const month = String(today.getMonth() + 1).padStart(2, '0'); //consigue el mes
        const day = String(today.getDate()).padStart(2, '0');//consigue el dia
        
        const formattedDate = `${year}-${month}-${day}`;//formato de la fecha

        dateInput.value = formattedDate;//asigna la fecha formateada al campo de fecha
    }
}); 