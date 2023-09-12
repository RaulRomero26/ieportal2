$(function() {
    $('[data-toggle="tooltip"]').tooltip()
})

var search = document.getElementById('id_search')
var catalogoActual = document.getElementById('catalogoActual')
var search_button = document.getElementById('search_button')
let selectedRowHorario = null;
document.getElementById('id_form_catalogo').style.display = 'none'

//cararteres máximos en texarea
const MAXLENGTH = 300
var textareas = document.querySelectorAll('textarea')

textareas.forEach(function(element, index, array) {
    element.maxLength = MAXLENGTH
})

search_button.addEventListener('click', buscarUsuarioCad)

function buscarUsuarioCad(e) {
    var myform = new FormData()
    myform.append("cadena", search.value)
    myform.append("catalogoActual", catalogoActual.value)

    fetch(base_url_js + 'Catalogos/buscarPorCadena', {
            method: 'POST',
            body: myform
        })
        .then(function(response) {
            if (response.ok) {
                return response.json()
            } else {
                throw "Error en la llamada Ajax";
            }
        })
        .then(function(myJson) {
            if (!(typeof(myJson) == 'string')) {
                document.getElementById('id_tbody').innerHTML = myJson.infoTable.body
                document.getElementById('id_thead').innerHTML = myJson.infoTable.header
                document.getElementById('id_pagination').innerHTML = myJson.links
                document.getElementById('id_link_excel').href = myJson.export_links.excel
                document.getElementById('id_link_pdf').href = myJson.export_links.pdf
                document.getElementById('id_total_rows').innerHTML = myJson.total_rows
            } else {
                console.log("myJson: " + myJson)
            }

        })
        .catch(function(error) {
            console.log("Error desde Catch _  " + error)
        })

}

function checarCadena(e) {
    if (search.value == "") {
        buscarUsuarioCad()
    }
}
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

const readFormOtros = (campos) => {

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
    console.log("inserneew")
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
const deleteRow = (obj, tableId) => {

    if (confirm('¿Desea eliminar este elemento?')) {
        const row = obj.parentElement.parentElement;
        document.getElementById(tableId.id).deleteRow(row.rowIndex);
        
    }

}

//function para precargar la información del registro seleccionado y visualizar formulario de edición
function editAction(catalogo, id_reg) {
    console.log("catalogo: " + catalogo + "\n Id: " + id_reg)
    document.getElementById('id_form_catalogo').style.display = 'block'
    document.getElementById('send_button').innerHTML = 'Guardar'
    /*Se comento esta asignacion ya que pone el boton con fondo blanco*/
   // document.getElementById('send_button').style.backgroundColor = 'var(--blue-darken-2)'
    window.scrollTo(0, 50);

    switch (catalogo) {
        case 1:
            var t_row = document.getElementById('tr' + id_reg)
            document.getElementById('id_alumno').value = id_reg
            document.getElementById('nombre').value = t_row.getElementsByTagName('td')[2].innerHTML.toUpperCase()
            document.getElementById('apellido_p').value = t_row.getElementsByTagName('td')[3].innerHTML.toUpperCase()
            document.getElementById('apellido_m').value = t_row.getElementsByTagName('td')[4].innerHTML.toUpperCase()
            document.getElementById('correo').value = t_row.getElementsByTagName('td')[5].innerHTML
            document.getElementById('activo').value = t_row.getElementsByTagName('td')[6].innerHTML
            document.getElementById('edad').value = t_row.getElementsByTagName('td')[7].innerHTML
            document.getElementById('ciudad').value = t_row.getElementsByTagName('td')[8].innerHTML.toUpperCase()
            document.getElementById('telefono').value = t_row.getElementsByTagName('td')[9].innerHTML
            document.getElementById('tipo_pago').value = t_row.getElementsByTagName('td')[10].innerHTML.toUpperCase()
            document.getElementById('nivel').value = t_row.getElementsByTagName('td')[11].innerHTML.toUpperCase()
            break
        case 2:
            var t_row = document.getElementById('tr' + id_reg)
            document.getElementById('id_maestro').value = id_reg
            document.getElementById('nombre_m').value = t_row.getElementsByTagName('td')[1].innerHTML.toUpperCase()
            document.getElementById('apellido_pm').value = t_row.getElementsByTagName('td')[2].innerHTML.toUpperCase()
            document.getElementById('apellido_mm').value = t_row.getElementsByTagName('td')[3].innerHTML.toUpperCase()
            document.getElementById('correo_m').value = t_row.getElementsByTagName('td')[4].innerHTML
            document.getElementById('activo_m').value = t_row.getElementsByTagName('td')[5].innerHTML
            document.getElementById('nivel_m').value = t_row.getElementsByTagName('td')[6].innerHTML.toUpperCase()
            document.getElementById('telefono_m').value = t_row.getElementsByTagName('td')[7].innerHTML
            document.getElementById('dia_disponible').value =""
            document.getElementById('hora_disponible').value =""
            document.getElementById('alertEditHorario').classList.add("mi_hide");
            console.log(document.getElementById('TableHorarios'))
            document.getElementById('tbody_horarios').innerHTML = "";
            insertTablaHorario(t_row.getElementsByTagName('td')[8].innerHTML,t_row.getElementsByTagName('td')[9].innerHTML)
            
            break
        case 3:
            var t_row = document.getElementById('tr' + id_reg)
            document.getElementById('id_tipo_clase').value = id_reg
            document.getElementById('id_descripcion').value = t_row.getElementsByTagName('td')[1].innerHTML.toUpperCase()
            break
        case 4:
            var t_row = document.getElementById('tr' + id_reg)
            document.getElementById('id_tipo_pago').value = id_reg
            document.getElementById('id_descripcion').value = t_row.getElementsByTagName('td')[1].innerHTML.toUpperCase()
            break
        case 5:
            var t_row = document.getElementById('tr' + id_reg)
            document.getElementById('id_metodo_pago').value = id_reg
            document.getElementById('id_descripcion').value = t_row.getElementsByTagName('td')[1].innerHTML.toUpperCase()
            break
        case 6:
            var t_row = document.getElementById('tr' + id_reg)
            document.getElementById('id_pago_hora').value = id_reg
            document.getElementById('id_descripcion').value = t_row.getElementsByTagName('td')[1].innerHTML.toUpperCase()
            document.getElementById('cantidad_pago').value = t_row.getElementsByTagName('td')[2].innerHTML.toUpperCase()
            break
    }

}

//funcion para ocultar formulario de edición o creación
function hideForm() {
    document.getElementById('id_form_catalogo').style.display = 'none'
}

//funcion para vaciar los campos correspondientes (si no lo estan) y mostrar el form de cada catálogo
function addAction(catalogo) {
    console.log("catalogo: " + catalogo)
    document.getElementById('id_form_catalogo').style.display = 'block'
    document.getElementById('send_button').innerHTML = 'Crear'
    /*Se comento esta asignacion ya que pone el boton con fondo blanco*/
   // document.getElementById('send_button').style.backgroundColor = 'var(--green-darken-2)'
    window.scrollTo(0, 50);

    switch (catalogo) {
        case 1:
            document.getElementById('id_alumno').value = ''
            document.getElementById('nombre').value = ''
            document.getElementById('apellido_p').value = ''
            document.getElementById('apellido_m').value = ''
            document.getElementById('activo').value = 1
            document.getElementById('correo').value = ''
            document.getElementById('edad').value = ''
            document.getElementById('ciudad').value = ''
            document.getElementById('telefono').value = ''
            document.getElementById('tipo_pago').value = ''
            document.getElementById('nivel').value = ''
            document.getElementById('nombre').focus()
            break
        case 2:
            document.getElementById('id_maestro').value = ''
            document.getElementById('nombre_m').value = ''
            document.getElementById('apellido_pm').value = ''
            document.getElementById('apellido_mm').value = ''
            document.getElementById('activo_m').value = 1
            document.getElementById('correo_m').value = ''
            document.getElementById('telefono_m').value = ''
            document.getElementById('tbody_horarios').innerHTML = "";
            document.getElementById('nivel_m').value = ''
            document.getElementById('nombre_m').focus()
            break
        case 3:
            document.getElementById('id_tipo_clase').value = ''
            document.getElementById('id_descripcion').value = ''
            document.getElementById('id_tipo_clase').focus()
            break
        case 4:
            document.getElementById('id_tipo_pago').value = ''
            document.getElementById('id_descripcion').value = ''
            document.getElementById('id_tipo_pago').focus()
            break
        case 5:
            document.getElementById('id_metodo_pago').value = ''
            document.getElementById('id_descripcion').value = ''
            document.getElementById('id_metodo_pago').focus()
            break
        case 6:
            document.getElementById('id_pago_hora').value = ''
            document.getElementById('id_descripcion').value = ''
            document.getElementById('cantidad_pago').focus()
            break
      
    }
}

function deleteAction(catalogo, id_reg) {
    console.log("catalogo: " + catalogo + "\n Id: " + id_reg)
    const confirmaDelete = confirm("¿Estás seguro de borrar este registro permanéntemente?")

    if (confirmaDelete) {
        var myForm = new FormData()
            //catálogo que será afectado por medio del form
        myForm.append('catalogo', catalogo)
            //acción del fetch (insertar o actualizar)
        myForm.append('Id_Reg', id_reg)
            //catálogo que será afectado por medio del form
        myForm.append('deletePostForm', 1)

        fetch(base_url_js + 'Catalogos/deleteFormFetch', {
                method: 'POST',
                body: myForm
            })
            .then(function(response) {
                if (response.ok) {
                    return response.json()
                } else {
                    throw "Error en la llamada Ajax";
                }
            })
            .then(function(myJson) {
                console.log(myJson)
                if (myJson == 'Success') {
                    alert("El registro ha sido borrado!")
                    document.location.reload()
                } else {
                    alert(myJson)
                }
            })
            .catch(function(error) {
                console.log("Error desde Catch _  " + error)
            })
    }
}

//función para enviar el formulario, comprobar si se trata de insert or update y comprobar todo el llenado correcto del mismo
async function sendFormAction(catalogo) {
    switch (catalogo) {
        case 1:
            //form inputs charge
            var id_alumno = document.getElementById('id_alumno')
            var nombre= document.getElementById('nombre')
            var apellido_p=document.getElementById('apellido_p')
            var apellido_m=document.getElementById('apellido_m')
            var activo=document.getElementById('activo')
            var correo=document.getElementById('correo')
            var edad=document.getElementById('edad')
            var ciudad=document.getElementById('ciudad')
            var telefono=document.getElementById('telefono')
            var tipo_pago=document.getElementById('tipo_pago')
            var nivel=document.getElementById('nivel')
            
            if (id_alumno.value == '') { // se trata de insert
                //validaciones
                if (nombre.value.trim() != '' && apellido_p.value.trim() != '' && apellido_m.value.trim() != '' && activo.value.trim() != '' && correo.value.trim() != '' && edad.value.trim() != '' && ciudad.value.trim() != '' && telefono.value.trim() != '' && tipo_pago.value.trim() != '' && nivel.value.trim() != '' ) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '1')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            } else { // se trata de update
                if (nombre.value.trim() != '' && apellido_p.value.trim() != '' && apellido_m.value.trim() != '' && activo.value.trim() != '' && correo.value.trim() != '' && edad.value.trim() != '' && ciudad.value.trim() != '' && telefono.value.trim() != '' && tipo_pago.value.trim() != '' && nivel.value.trim() != '' ) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '2')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            }
            break
        case 2:
            //form inputs charge
            var id_maestro = document.getElementById('id_maestro')
            var nombre= document.getElementById('nombre_m')
            var apellido_p=document.getElementById('apellido_pm')
            var apellido_m=document.getElementById('apellido_mm')
            var activo=document.getElementById('activo_m')
            var correo=document.getElementById('correo_m')
            var vari_hor=leerDias()
            var horario_disponible=vari_hor[0]
            var dias_disponible=vari_hor[1]
            //var =leerDias()
            console.log(dias_disponible)
            var telefono=document.getElementById('telefono_m')
            var nivel=document.getElementById('nivel_m')
            
            if (id_maestro.value == '') { // se trata de insert
                //validaciones
                if (nombre.value.trim() != '' && apellido_p.value.trim() != '' && apellido_m.value.trim() != '' && activo.value.trim() != '' && correo.value.trim() != '' && horario_disponible.trim() != '' && dias_disponible.trim() != '' && telefono.value.trim() != '' && nivel.value.trim() != '' ) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '1')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            } else { // se trata de update
                if (nombre.value.trim() != '' && apellido_p.value.trim() != '' && apellido_m.value.trim() != '' && activo.value.trim() != '' && correo.value.trim() != ''  && horario_disponible.trim() != '' && dias_disponible.trim() != '' && telefono.value.trim() != '' && nivel.value.trim() != '' ) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '2')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            }
            break
        case 3:
            //form inputs charge
            var id_tipo_clase = document.getElementById('id_tipo_clase')
            var descripcion = document.getElementById('id_descripcion')

            if (id_tipo_clase.value == '') { // se trata de insert
                //validaciones
                if (descripcion.value.trim() != '' && descripcion.value.length <= MAXLENGTH) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '1')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            } else { // se trata de update
                if (id_tipo_clase.value.trim() != '' && descripcion.value.trim() != '' && descripcion.value.length <= MAXLENGTH) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '2')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            }
            break
        case 4:
            //form inputs charge
            var id_tipo_pago = document.getElementById('id_tipo_pago')
            var descripcion = document.getElementById('id_descripcion')

            if (id_tipo_pago.value == '') { // se trata de insert
                //validaciones
                if (descripcion.value.trim() != '' && descripcion.value.length <= MAXLENGTH) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '1')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            } else { // se trata de update
                if (id_tipo_pago.value.trim() != '' && descripcion.value.trim() != '' && descripcion.value.length <= MAXLENGTH) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '2')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            }
            break
        case 5:
            //form inputs charge
            var id_metodo_pago = document.getElementById('id_metodo_pago')
            var descripcion = document.getElementById('id_descripcion')

            if (id_metodo_pago.value == '') { // se trata de insert
                //validaciones
                if (descripcion.value.trim() != '' && descripcion.value.length <= MAXLENGTH) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '1')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            } else { // se trata de update
                if (id_metodo_pago.value.trim() != '' && descripcion.value.trim() != '' && descripcion.value.length <= MAXLENGTH) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '2')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            }
            break
        case 6:
            //form inputs charge
            var id_pago_hora = document.getElementById('id_pago_hora')
            var descripcion = document.getElementById('id_descripcion')
            var cantidad = document.getElementById('cantidad_pago')

            if (id_pago_hora.value == '') { // se trata de insert
                //validaciones
                if (descripcion.value.trim() != '' && descripcion.value.length <= MAXLENGTH) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '1')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            } else { // se trata de update
                if (id_pago_hora.value.trim() != '' && descripcion.value.trim() != '' && descripcion.value.length <= MAXLENGTH) {
                    console.log("form valido, se envia form por fetch")
                    fetchFormCatalogo(catalogo, '2')
                }
                else{
                    alert("Agregue toda la información necesaria para continuar")
                }
            }
            break
    }
}
function leerDias(){
    dias=""
    horas=""
    const table = document.getElementById('TableHorarios');
    for(let i = 1; i<table.rows.length; i++){
        dias=dias+table.rows[i].cells[0].innerHTML+",";
        horas=horas+ table.rows[i].cells[1].innerHTML+",";
    }
    console.log(dias)
    console.log(horas)
    return [dias,horas];
}
const readTableDetenidos = ()=>{
    const table = document.getElementById('tableDetenidos');

    let detenidos = [];

    for(let i = 1; i<table.rows.length; i++){
        detenidos.push({
            ['row']:{
                nombre: table.rows[i].cells[0].innerHTML,
                primerApellido: table.rows[i].cells[1].innerHTML,
                segundoApellido: table.rows[i].cells[2].innerHTML,
                fecha: table.rows[i].cells[3].innerHTML,
                edad: table.rows[i].cells[4].innerHTML,
                sexo: table.rows[i].cells[5].innerHTML,
            }
        });
    }

    return detenidos;
}
function leerHorario(){
    horas=""
    return horas;
}
function fetchFormCatalogo(catalogo, action) { //action: 1 - insertar,  2 - actualizar
    var myForm = new FormData()
        //acción del fetch (insertar o actualizar)
    myForm.append('action', action)
        //catálogo que será afectado por medio del form
    myForm.append('catalogo', catalogo)
        //catálogo que será afectado por medio del form
    myForm.append('postForm', 1)

    switch (catalogo) { //apendizar todos los campos correspondientes conforme al cátolo afectado
        case 1:
            myForm.append('id_alumno', document.getElementById('id_alumno').value)
            myForm.append('nombre', document.getElementById('nombre').value.toUpperCase())
            myForm.append('apellido_p', document.getElementById('apellido_p').value.toUpperCase())
            myForm.append('apellido_m', document.getElementById('apellido_m').value.toUpperCase())
            myForm.append('activo', document.getElementById('activo').value)
            myForm.append('correo', document.getElementById('correo').value)
            myForm.append('edad', document.getElementById('edad').value)
            myForm.append('ciudad', document.getElementById('ciudad').value.toUpperCase())
            myForm.append('telefono', document.getElementById('telefono').value)
            myForm.append('tipo_pago', document.getElementById('tipo_pago').value.toUpperCase())
            myForm.append('nivel', document.getElementById('nivel').value.toUpperCase())
            myForm.append('id_clase',0)
            myForm.append('contador',0)
            break
        case 2:
            myForm.append('id_maestro', document.getElementById('id_maestro').value)
            myForm.append('nombre', document.getElementById('nombre_m').value.toUpperCase())
            myForm.append('apellido_p', document.getElementById('apellido_pm').value.toUpperCase())
            myForm.append('apellido_m', document.getElementById('apellido_mm').value.toUpperCase())
            myForm.append('activo', document.getElementById('activo_m').value)
            myForm.append('correo', document.getElementById('correo_m').value)
            var vari_hor=leerDias()
            myForm.append('horario_disponible', vari_hor[1])
            myForm.append('dias_disponible',vari_hor[0].toUpperCase())
            myForm.append('telefono', document.getElementById('telefono_m').value)
            myForm.append('nivel', document.getElementById('nivel_m').value.toUpperCase())
            break
        case 3:
            myForm.append('id_tipo_clase', document.getElementById('id_tipo_clase').value)
            myForm.append('Descripcion', document.getElementById('id_descripcion').value.toUpperCase())
            break
        case 4:
            myForm.append('id_tipo_pago', document.getElementById('id_tipo_pago').value)
            myForm.append('Descripcion', document.getElementById('id_descripcion').value.toUpperCase())
            break
        case 5:
            myForm.append('id_metodo_pago', document.getElementById('id_metodo_pago').value)
            myForm.append('Descripcion', document.getElementById('id_descripcion').value.toUpperCase())
            break
        case 6:
            myForm.append('id_pago_hora', document.getElementById('id_pago_hora').value)
            myForm.append('Descripcion', document.getElementById('id_descripcion').value.toUpperCase())
            myForm.append('Cantidad', document.getElementById('cantidad_pago').value.toUpperCase())
            break

    }

    fetch(base_url_js + 'Catalogos/sendFormFetch', {
            method: 'POST',
            body: myForm
        })
        .then(function(response) {
            if (response.ok) {
                return response.json()
            } else {
                throw "Error en la llamada Ajax";
            }
        })
        .then(function(myJson) {
            console.log(myJson)
            if (myJson == 'Success') {
                alert("Consulta realizada correctamente")
                document.location.reload()
            } else {
                alert(myJson)
            }
        })
        .catch(function(error) {
            console.log("Error desde Catch _  " + error)
        })
}