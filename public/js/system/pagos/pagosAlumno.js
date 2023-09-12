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
                comentarios: rowsTablePagos[i].Comentarios,
            }
            insertNewRowPago(formData);
        }
        
        
        

    })
}
const onFormPagoSubmit = () => {
    const campos = ['id_pagom', 'fecha_iniciom', 'fecha_finm', 'fecha_pagom','monto_pagom', 'desc_pagom','comentarios'];

    if (validateFormPago(campos)) {
        let formData = readFormPago(campos);
        if (selectedRowPago === null)
            insertNewRowPago2(formData);
        else
            updateRowPago(formData);

        resetFormPago(campos);
    }

}

const insertNewRowPago2 = (obj) => {
    const campos = ['id_pagom', 'fecha_iniciom', 'fecha_finm', 'fecha_pagom','monto_pagom', 'desc_pagom','comentarios'];

    document.getElementById('alertEditPago').style.display = 'none';
    document.getElementById('container_PagosM').style.display = 'block';
   
    const table = document.getElementById('table_pagos_alumno').getElementsByTagName('tbody')[0];
    let newRow = table.insertRow(table.length);
    document.getElementById('fecha_iniciom').readOnly = false;
    document.getElementById('fecha_finm').readOnly = false;
    document.getElementById('fecha_pagom').readOnly = false;

    newRow.insertCell(0).innerHTML = document.getElementById(campos[0]).value;
    newRow.insertCell(1).innerHTML = document.getElementById(campos[1]).value;
    newRow.insertCell(2).innerHTML = document.getElementById(campos[2]).value;
    newRow.insertCell(3).innerHTML = document.getElementById(campos[3]).value;
    newRow.insertCell(4).innerHTML = document.getElementById(campos[4]).value;
    newRow.insertCell(5).innerHTML = document.getElementById(campos[5]).value;
    newRow.insertCell(6).innerHTML = document.getElementById(campos[6]).value;
    if (document.getElementById(campos[5]).value === 'PENDIENTE') {
        newRow.insertCell(7).innerHTML = `<button type="button" class="btn btn-primary" onclick="editPago(this)"> 
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
            </svg>
        </button>`;
        //newRow.insertCell(15).innerHTML = `<input type="button" class="btn btn-primary" value="-" onclick="deleteRow(this,Pagos_banda)">`;
    }
}

const insertNewRowPago = ({ id_pago, fecha_inicio,fecha_fin,fecha_pago, desc_pago, monto_pago,comentarios }, type) => {

    const table = document.getElementById('table_pagos_alumno').getElementsByTagName('tbody')[0];
    let newRow = table.insertRow(table.length);

    newRow.insertCell(0).innerHTML = id_pago;
    newRow.insertCell(1).innerHTML = fecha_inicio;
    newRow.insertCell(2).innerHTML = fecha_fin;
    newRow.insertCell(3).innerHTML = fecha_pago;
    newRow.insertCell(4).innerHTML = monto_pago;
    newRow.insertCell(5).innerHTML = desc_pago;
    newRow.insertCell(6).innerHTML = comentarios;
    if (desc_pago === 'PENDIENTE') {
        newRow.insertCell(7).innerHTML = `<button type="button" class="btn btn-primary" onclick="editPago(this)"> 
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
            </svg>
        </button>`;
        //newRow.insertCell(15).innerHTML = `<input type="button" class="btn btn-primary" value="-" onclick="deleteRow(this,Pagos_banda)">`;
    }
}
const editPago = (obj) => {
    const campos = ['id_pagom', 'fecha_iniciom', 'fecha_finm', 'fecha_pagom','monto_pagom', 'desc_pagom','comentarios'];

    document.getElementById('alertEditPago').style.display = 'block';
    document.getElementById('container_PagosM').style.display = 'block';
    document.getElementById('fecha_iniciom').readOnly = true;
    document.getElementById('fecha_finm').readOnly = true;
    document.getElementById('fecha_pagom').readOnly = true;

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
    //document.getElementById('container_PagosM').style.display = 'none';

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
        if (campos[i]!='id_pagom' && campos[i]!='comentarios'){
            if (document.getElementById(campos[i]).value === "") {
                isValid = false;
                document.getElementById(campos[i] + '_error').style.display = 'block';
            } else {
                document.getElementById(campos[i] + '_error').style.display = 'none';
            }
        }
    }

    return isValid;
}
const resetFormPago = (campos) => {
    for (let i = 0; i < campos.length; i++) {
        document.getElementById(campos[i]).value = '';
    }

    selectedRowPago = null;
    document.getElementById('fecha_iniciom').readOnly = false;
    document.getElementById('fecha_finm').readOnly = false;
    document.getElementById('fecha_pagom').readOnly = false;
}

async function crear_guardarPA(e) {
    var msg_principalesError = document.getElementById('msg_principales')
    //console.log(e.target.id)
    //console.log("para guardar")
    e.preventDefault();

    const button = document.getElementById(e.target.id);
    button.innerHTML = `
        Guardando
        <div class="spinner-grow spinner-grow-sm" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `;
    var urlFetch = base_url_js + "Pagos";
    urlFetch += "/editPagosAFetch";//editar
    //console.log(urlFetch)
    var formInsp = document.getElementById("pagos_alumno");
    var myFormData = new FormData(formInsp);
    myFormData.append('pagos_table',JSON.stringify(readTablePagos()));
    console.log(myFormData)
    fetch(urlFetch, {
        method: 'POST',
        body: myFormData
    })

    .then(res => res.json())

    .then(data => {
        button.innerHTML = `
            Guardar
        `;
        //console.log(data.status)
        if (!data.status) {
            msg_principalesError.innerHTML = '<div class="alert alert-success text-center" role="alert">Hubo un error en la actualización</div>';
        }
        else {
            window.scroll({
                top: 0,
                left: 100,
                behavior: 'smooth'
            });

            msg_principalesError.innerHTML = '<div class="alert alert-success text-center" role="alert">Información actualizada con éxito</div>';
            setInterval(() => {
                window.location = base_url_js + "Pagos/index";
            }, 900);
            // result.tab === '1' ? '' : document.getElementById('save-tab-0').style.display = 'block';
        }
    })

}
const readTablePagos = () => {
    const table = document.getElementById('table_pagos_alumno');
    let lista_pagos = [];

    for (let i = 1; i < table.rows.length; i++) {
        lista_pagos.push({
            ['row']: {
                id_alumno: document.getElementById('id_alumno').value,
                id_pago: table.rows[i].cells[0].innerHTML,
                fecha1: table.rows[i].cells[1].innerHTML,
                fecha2: table.rows[i].cells[2].innerHTML,
                fecha3: table.rows[i].cells[3].innerHTML,
                monto_pago: table.rows[i].cells[4].innerHTML,
                desc_pago: table.rows[i].cells[5].innerHTML,
                comentarios: table.rows[i].cells[6].innerHTML,
            }
        });
    }

    console.log(lista_pagos)
    return lista_pagos;
}
