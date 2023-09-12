let selectedRowIntegrantes = null;


//DivMapa.style.cssText = 'display: none !important';

const onFormIntegranteSubmit = () => {
    const campos = ['nombre_int', 'apem_int', 'apep_int', 'sexo_int','estado_int', 'alias_int','curp_int', 'udc_int','utc_int', 'face_int', 'asociado_int','antece_int','categoria_int'];

  //  if (validateFormIntegrante(campos)) {
        let formData = readFormIntegrante(campos);
        if (selectedRowIntegrantes === null)
            insertNewRowIntegrante(formData);
        else
            updateRowIntegrante(formData);

        resetFormIntegrante(campos);
    //}

}

const insertNewRowIntegrante = ({ nombre_int, apem_int, apep_int, sexo_int,estado_int, alias_int,curp_int, udc_int,utc_int, face_int, asociado_int,antece_int,categoria_int }, type) => {

    const table = document.getElementById('integrantes_banda').getElementsByTagName('tbody')[0];
    let newRow = table.insertRow(table.length);

    newRow.insertCell(0).innerHTML = nombre_int;
    newRow.insertCell(1).innerHTML = apep_int;
    newRow.insertCell(2).innerHTML = apem_int;
    newRow.insertCell(3).innerHTML = sexo_int;
    newRow.insertCell(4).innerHTML = estado_int;
    newRow.insertCell(5).innerHTML = alias_int;
    newRow.insertCell(6).innerHTML = curp_int.toUpperCase();
    newRow.insertCell(7).innerHTML = udc_int;
    newRow.insertCell(8).innerHTML = utc_int;
    face = newRow.insertCell(9)
    face.innerHTML = face_int;
    face.classList.add('smalltd')

    newRow.insertCell(10).innerHTML = asociado_int;
    newRow.insertCell(11).innerHTML = antece_int;
    newRow.insertCell(12).innerHTML = categoria_int;
    newRow.insertCell(13).innerHTML = `
    <div class="d-flex justify-content-around" id="uploadContent_row${newRow.rowIndex}">
        <div class="form-group">
            <input type="file" name="foto_row${newRow.rowIndex}" accept="image/*" id="fileFoto_row${newRow.rowIndex}" class="inputfile uploadFileFotos mi_hide" onchange="uploadFile(event)" data-toggle="tooltip" data-placement="bottom">
            <label for="fileFoto_row${newRow.rowIndex}" >
                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-cloud-upload" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"/>
                    <path fill-rule="evenodd" d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
            </label>
        </div>
    </div>
    <div id="imageContent_row${newRow.rowIndex}"></div>
`;

    if (type === undefined) {
        newRow.insertCell(14).innerHTML = `<button type="button" class="btn btn-primary" onclick="editIntegrante(this)"> 
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
            </svg>
        </button>`;
        newRow.insertCell(15).innerHTML = `<input type="button" class="btn btn-primary" value="-" onclick="deleteRow(this,integrantes_banda)">`;
    }

}

const editIntegrante = (obj) => {
    const campos = ['nombre_int', 'apem_int', 'apep_int', 'sexo_int','estado_int', 'alias_int','curp_int', 'udc_int','utc_int', 'face_int', 'asociado_int','antece_int','categoria_int'];

    document.getElementById('alertEditIntegrante').style.display = 'block';

    selectedRowIntegrantes = obj.parentElement.parentElement;
    for (let i = 0; i < campos.length; i++) {
        document.getElementById(campos[i]).value = selectedRowIntegrantes.cells[i].innerHTML;
    }
}

const updateRowIntegrante = (data) => {
    for (dataKey in data) {
        let i = Object.keys(data).indexOf(dataKey);
        selectedRowIntegrantes.cells[i].innerHTML = data[dataKey];
    }

    document.getElementById('alertEditIntegrante').style.display = 'none';

}

const readFormIntegrante = (campos) => {
    let formData = {}
    for (let i = 0; i < campos.length; i++) {
        formData[campos[i]] = document.getElementById(campos[i]).value;
    }

    return formData;

}

const resetFormIntegrante = (campos) => {
    for (let i = 0; i < campos.length; i++) {
        document.getElementById(campos[i]).value = '';
    }

    selectedRowIntegrantes = null;

}

const validateFormIntegrante = (campos) => {
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
//----------------para zonas y colonias
let selectedRowZonas = null;
const onFormZonaSubmit = () => {
    const campos = ['zona_int', 'colonias_int'];

  //  if (validateFormZona(campos)) {
        let formData = readFormZona(campos);
        if (selectedRowZonas === null)
            insertNewRowZona(formData);
        else
            updateRowZona(formData);

        resetFormZona(campos);
    //}

}

const insertNewRowZona = ({ zona_int, colonias_int }, type) => {

    const table = document.getElementById('zonas_table').getElementsByTagName('tbody')[0];
    let newRow = table.insertRow(table.length);

    newRow.insertCell(0).innerHTML = zona_int;
    newRow.insertCell(1).innerHTML = colonias_int;
    if (type === undefined) {
        newRow.insertCell(2).innerHTML = `<button type="button" class="btn btn-primary" onclick="editZona(this)"> 
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
            </svg>
        </button>`;
        newRow.insertCell(3).innerHTML = `<input type="button" class="btn btn-primary" value="-" onclick="deleteRow(this,zonas_table)">`;
    }

}

const editZona = (obj) => {
    const campos = ['zona_int', 'colonias_int'];

    document.getElementById('alertEditZona').style.display = 'block';

    selectedRowZonas = obj.parentElement.parentElement;
    for (let i = 0; i < campos.length; i++) {
        document.getElementById(campos[i]).value = selectedRowZonas.cells[i].innerHTML;
    }
}

const updateRowZona = (data) => {
    for (dataKey in data) {
        let i = Object.keys(data).indexOf(dataKey);
        selectedRowZonas.cells[i].innerHTML = data[dataKey];
    }

    document.getElementById('alertEditZona').style.display = 'none';

}

const readFormZona = (campos) => {
    let formData = {}
    for (let i = 0; i < campos.length; i++) {
        formData[campos[i]] = document.getElementById(campos[i]).value;
    }

    return formData;

}

const resetFormZona = (campos) => {
    for (let i = 0; i < campos.length; i++) {
        document.getElementById(campos[i]).value = '';
    }

    selectedRowZonas = null;

}

const validateFormZona = (campos) => {
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