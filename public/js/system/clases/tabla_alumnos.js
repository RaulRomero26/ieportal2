let selectedRowAlumnos = null;


//DivMapa.style.cssText = 'display: none !important';

const onFormAlumnoSubmit = () => {
    const campos = ['alumno_asignar'];

  //  if (validateFormIntegrante(campos)) {
        let formData = readFormAlumno(campos);
        if (selectedRowAlumnos === null)
            insertNewRowAlumno(formData);
        else
            updateRowAlumno(formData);

        resetFormAlumno(campos);
    //}

}

const insertNewRowAlumno = ({ alumno_asignar }, type) => {

    const table = document.getElementById('alumnos_table').getElementsByTagName('tbody')[0];
    let newRow = table.insertRow(table.length);

    newRow.insertCell(0).innerHTML = alumno_asignar;

    if (type === undefined) {
        newRow.insertCell(1).innerHTML = `<button type="button" class="btn btn-primary" onclick="editAlumno(this)"> 
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
            </svg>
        </button>`;
        newRow.insertCell(2).innerHTML = `<input type="button" class="btn btn-primary" value="-" onclick="deleteRow(this,alumnos_table)">`;
    }

}

const editAlumno = (obj) => {
    const campos = ['alumno_asignar'];

    document.getElementById('alertEditAlumno').style.display = 'block';

    selectedRowIntegrantes = obj.parentElement.parentElement;
    for (let i = 0; i < campos.length; i++) {
        document.getElementById(campos[i]).value = selectedRowIntegrantes.cells[i].innerHTML;
    }
}

const updateRowAlumno = (data) => {
    for (dataKey in data) {
        let i = Object.keys(data).indexOf(dataKey);
        selectedRowIntegrantes.cells[i].innerHTML = data[dataKey];
    }

    document.getElementById('alertEditAlumno').style.display = 'none';

}

const readFormAlumno = (campos = ['alumno_asignar']) => {
    let formData = {}
    for (let i = 0; i < campos.length; i++) {
        formData[campos[i]] = document.getElementById(campos[i]).value;
    }

    return formData;

}

const resetFormAlumno = (campos) => {
    for (let i = 0; i < campos.length; i++) {
        document.getElementById(campos[i]).value = '';
    }

    selectedRowIntegrantes = null;

}

const validateFormAlumno = (campos) => {
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
// Funcion re utilizable que puede eliminar los renglones de una tabla
const deleteRow = (obj, tableId) => {

    if (confirm('¿Desea eliminar este elemento?')) {
        const row = obj.parentElement.parentElement;
        document.getElementById(tableId.id).deleteRow(row.rowIndex);
        if (tableId.id === 'elementosParticipantes') {

            let band = true;

            for (let i = 1; i < tableId.rows.length; i++) {
                if (i > 1 && tableId.rows[i].cells[6].childNodes[1].innerHTML != tableId.rows[i - 1].cells[6].childNodes[1].innerHTML) {
                    band = false;
                }
            }

            if (band) {
                for (let i = 1; i < tableId.rows.length; i++) {
                    tableId.rows[i].cells[6].innerHTML = `
                        <p class="mb-0">${tableId.rows[i].cells[6].childNodes[1].innerHTML}</p>
                    `
                }
            }
        }

        if (tableId.id === 'integrantes_banda') {
            table = document.getElementById('integrantes_banda')
            for(let i=1;i<table.rows.length;i++){

                let contenedorImg = table.rows[i].cells[13].childNodes[3];
    
                contenedorImg.setAttribute('id', 'imageContent_row'+i);
                contenedorImg.childNodes[1].childNodes[3].setAttribute('id', 'images_row_'+i);
    
                let contenedorInput = table.rows[i].cells[13].childNodes[1];
    
                contenedorInput.setAttribute('id', 'uploadContent_row'+i);
                contenedorInput.childNodes[1].childNodes[1].setAttribute('id', 'fileFoto_row'+i);
                contenedorInput.childNodes[1].childNodes[1].setAttribute('name', 'foto_row'+i);
                contenedorInput.childNodes[1].childNodes[3].setAttribute('for', 'fileFoto_row'+i);
                //contenedorInput.childNodes[3].childNodes[1].setAttribute('id', 'row-'+i);
    
            }
        }
    }

}