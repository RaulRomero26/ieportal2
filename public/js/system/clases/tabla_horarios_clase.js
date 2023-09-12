let selectedRowHorario = null;

function insertTablaHorario(dias, horarios){
    console.log(dias,horarios)
    const table = document.getElementById('TableHorarios').getElementsByTagName('tbody')[0];
    dias=(dias).split(",")
    horarios=(horarios).split(",")
    let i=0
    dias.forEach((dia, index, array) => {
        if (dia.trim() != ''){
            let newRow = table.insertRow(table.length);
            
            newRow.insertCell(0).innerHTML = dia.toUpperCase();
            newRow.insertCell(1).innerHTML = horarios[i];
            newRow.insertCell(2).innerHTML = `<button class="btn btn-primary action_row" onclick="editHorario(this)"> 
                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                </svg>
            </button>`;
            newRow.insertCell(3).innerHTML = `<input type="button" class="btn btn-primary action_row" value="-" onclick="deleteRow(this,TableHorarios)">`;

            }
        i+=1
    })

}

const editHorario = (obj) => {
    document.getElementById('alertEditHorario').classList.remove("mi_hide");
    document.getElementById('alertEditHorario').innerHTML = `
        <div class="alert alert-warning text-center" role="alert">
            Está editando una horario
        </div>
    `;
        
    selectedRowHorario = obj.parentElement.parentElement;

    const campos = ['dia_disponible', 'hora_disponible'];
    
    campos.forEach((elem, i) => {
        document.getElementById(elem).value = selectedRowHorario.cells[i].innerHTML;
    });

}
const onFormOtroSubmit = () => {

    const campos = ['dia_disponible', 'hora_disponible'];

    if (validateFormOtro(campos)) {
        let formData = readFormOtros(campos);
        if (selectedRowHorario === null)
            insertNewRowOtro(formData);
        else
            updateRowOtro(formData);

        resetFormOtros(campos);
    }
}
const resetFormOtros = (campos) => {

    for (let i = 0; i < campos.length; i++) {
        document.getElementById(campos[i]).value = '';
    }
    selectedRowHorario = null;

}

const readFormOtros = (campos =  ['dia_disponible', 'hora_disponible']) => {
    console.log('es del read, ', campos);
    let formData = {}
    for (let i = 0; i < campos.length; i++) {
        formData[campos[i]] = document.getElementById(campos[i]).value;
    }

    return formData;

}
const validateFormOtro = (campos) => {

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

const insertNewRowOtro = ({ dia_disponible,hora_disponible }, type) => {
    const table = document.getElementById('TableHorarios').getElementsByTagName('tbody')[0];
    let newRow = table.insertRow(table.length);

    newRow.insertCell(0).innerHTML = dia_disponible.toUpperCase();
    newRow.insertCell(1).innerHTML = hora_disponible;
    if (type === undefined) {
        newRow.insertCell(2).innerHTML = `<button type="button" class="btn btn-primary" onclick="editHorario(this)"> 
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
            </svg>
        </button>`;
        newRow.insertCell(3).innerHTML = `<input type="button" class="btn btn-primary" value="-" onclick="deleteRow(this,TableHorarios)">`;
    }

}
const updateRowOtro = (data) => {
    
    for (dataKey in data) {
        let i = Object.keys(data).indexOf(dataKey);
        selectedRowHorario.cells[i].innerHTML = data[dataKey].toUpperCase();
    }

    document.getElementById('alertEditHorario').classList.add("mi_hide");

}