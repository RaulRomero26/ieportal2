let selectedRowPago = null;
window.onload = function() {
    var id_alumno = document.getElementById('id_alumno')
    var myFormData = new FormData()
    myFormData.append('id_alumno', id_alumno.value)
    //console.log(myFormData.get('id_alumno'))
    fetch(base_url_js + 'Pagos/getAlumno', {
        method: 'POST',
        body: myFormData
    })

    .then(res => res.json())

    .then(data => {
        console.log(data)
        //document.getElementById('no_grupo').value=
        //console.log("banda",data['grupo']['ID_BANDA'])
        document.getElementById('id_alumno').value=data['alumno']['Id_alumno']
        document.getElementById('nombre_a').value=data['alumno']['Nombre']
        document.getElementById('apellido_pm').value=data['alumno']['Apellido_paterno']
        document.getElementById('apellido_am').value=data['alumno']['Apellido_materno']
        document.getElementById('correo_a').value=data['alumno']['Correo']
        document.getElementById('telefono_a').value=data['alumno']['Telefono']
        document.getElementById('nivel_a').value=data['alumno']['Nivel']
        document.getElementById('activo_a').value=data['alumno']['Activo']
        document.getElementById('tipop_a').value=data['alumno']['Tipo_pago']


        
        
        const rowsTablePagos = data.pagos;
        //console.log(rowsTableIntegrantes)
        for (let i = 0; i < rowsTablePagos.length; i++) {
    
            let formData = {
                id_pago: rowsTablePagos[i].Id_pagosa,
                fecha_inicio: rowsTablePagos[i].Fecha_inicio,
                fecha_fin: rowsTablePagos[i].Fecha_final,
                fecha_pago: rowsTablePagos[i].Fecha,
                desc_pago: rowsTablePagos[i].Descripcion,
                monto_pago: rowsTablePagos[i].Monto,
            }
            insertNewRowPago(formData);
        }
        
        
        

    })
}
const onFormPagoSubmit = () => {
    const campos = ['id_pagom', 'fecha_iniciom', 'fecha_finm', 'fecha_pagom','monto_pagom', 'desc_pagom'];

  //  if (validateFormIntegrante(campos)) {
        let formData = readFormPago(campos);
        if (selectedRowPago === null)
            insertNewRowPago(formData);
        else
            updateRowPago(formData);

        resetFormPago(campos);
    //}

}
const insertNewRowPago = ({ id_pago, fecha_inicio,fecha_fin,fecha_pago, desc_pago, monto_pago }, type) => {

    const table = document.getElementById('table_pagos_alumno').getElementsByTagName('tbody')[0];
    let newRow = table.insertRow(table.length);

    newRow.insertCell(0).innerHTML = id_pago;
    newRow.insertCell(1).innerHTML = fecha_inicio;
    newRow.insertCell(2).innerHTML = fecha_fin;
    newRow.insertCell(3).innerHTML = fecha_pago;
    newRow.insertCell(4).innerHTML = monto_pago;
    newRow.insertCell(5).innerHTML = desc_pago;
    
}
const editPago = (obj) => {
    const campos = ['id_pagom', 'fecha_iniciom', 'fecha_finm', 'fecha_pagom','monto_pagom', 'desc_pagom'];

    document.getElementById('alertEditPago').style.display = 'block';
    document.getElementById('container_PagosM').style.display = 'block';

    selectedRowPago = obj.parentElement.parentElement;
    for (let i = 0; i < campos.length; i++) {
        
        if (campos[i]=='fecha_pagom'){
            // crea un nuevo objeto `Date`
            var today = new Date();

            // `getDate()` devuelve el día del mes (del 1 al 31)
            var day = today.getDate();

            // `getMonth()` devuelve el mes (de 0 a 11)
            var month = (today.getMonth() + 1).toString().padStart(2, "0");

            // `getFullYear()` devuelve el año completo
            var year = today.getFullYear();

            // muestra la fecha de hoy en formato `MM/DD/YYYY`
            console.log(`${month}/${day}/${year}`);
            document.getElementById(campos[i]).value = `${year}-${month}-${day}`
        }
        else{
            document.getElementById(campos[i]).value = selectedRowPago.cells[i].innerHTML;
        }
    }
}

const updateRowPago = (data) => {
    for (dataKey in data) {
        let i = Object.keys(data).indexOf(dataKey);
        selectedRowPago.cells[i].innerHTML = data[dataKey];
    }

    document.getElementById('alertEditPago').style.display = 'none';
    document.getElementById('container_PagosM').style.display = 'none';

}

const readFormPago = (campos) => {
    let formData = {}
    for (let i = 0; i < campos.length; i++) {
        formData[campos[i]] = document.getElementById(campos[i]).value;
    }
    return formData;

}
const validateFormPago = (campos) => {
    let isValid = true;

    for (i = 0; i < campos.length; i++) {
        if (document.getElementById(campos[i]).value === "") {
            isValid = false;
            document.getElementById(campos[i] + '-invalid').style.display = 'block';
        } else {
            document.getElementById(campos[i] + '-invalid').style.display = 'none';
        }
    }

    return isValid;
}
const resetFormPago = (campos) => {
    for (let i = 0; i < campos.length; i++) {
        document.getElementById(campos[i]).value = '';
    }

    selectedRowPago = null;
}
