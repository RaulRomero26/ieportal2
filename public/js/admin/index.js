$(function() {
    $('[data-toggle="tooltip"]').tooltip({
        delay: { "show": 200, "hide": 100 }
    })
})

$(function() {
    $('[data-toggle="popover"]').popover()
})

//document.addEventListener("DOMContentLoaded", function(event) {
var search = document.getElementById('id_search')
var filtroActual = document.getElementById('filtroActual')
var search_button = document.getElementById('search_button')

search_button.addEventListener('click', buscarUsuarioCad)

function buscarUsuarioCad(e) {
    //if (search.value != "") {
    var myform = new FormData()
    myform.append("cadena", search.value)
    myform.append("filtroActual", filtroActual.value)

    fetch(base_url_js + 'UsersAdmin/buscarPorCadena', {
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
            //console.log("Respuesta 2\n"+JSON.stringify(myJson))
            //console.log("Respuesta 2\n"+JSON.stringify(myJson.response))
            if (!(typeof(myJson) == 'string')) {
                document.getElementById('id_tbody').innerHTML = myJson.infoTable.body
                document.getElementById('id_thead').innerHTML = myJson.infoTable.header
                document.getElementById('id_pagination').innerHTML = myJson.links
                    //document.getElementById('id_link_csv').href = myJson.export_links.csv
                document.getElementById('id_link_excel').href = myJson.export_links.excel
                document.getElementById('id_link_pdf').href = myJson.export_links.pdf
                document.getElementById('id_total_rows').innerHTML = myJson.total_rows
                document.getElementById('id_dropdownColumns').innerHTML = myJson.dropdownColumns
                var columnsNames3 = document.querySelectorAll('th')
                columnsNames3.forEach(function(element, index, array) {
                    if (element.className.match(/column.*/))
                        hideShowColumn(element.className)
                });
                //console.log(myJson)
            } else {
                //console.log("myJson: " + myJson)
            }
            $('[data-toggle="popover"]').popover()

        })
        .catch(function(error) {
            //console.log("Error desde Catch _  " + error)
        })
        //}

}

function checarCadena(e) {
    if (search.value == "") {
        buscarUsuarioCad()
    }
}
//});

function aplicarRangos() {
    //obtener cada valor de la fecha
    var rango_inicio = document.getElementById('id_date_1').value
    var rango_fin = document.getElementById('id_date_2').value
        //comprobar si ya seleccionó una fecha
    if (rango_inicio != '' && rango_fin != '') {
        let fecha1 = new Date(rango_inicio);
        let fecha2 = new Date(rango_fin)

        let resta = fecha2.getTime() - fecha1.getTime()
            //console.log("resta = "+resta)
        if (resta >= 0) { //comprobar si los rangos de fechas son correctos
            document.getElementById('form_rangos').submit()
        } else {
            //caso de elegir rangos erroneos
            //console.log("Elige intervalos correctos")
            alert("Elige intervalos correctos")
        }
    } else {
        //caso de no ingresar aún nada
        //console.log("Te falta knalito")
        alert("Selecciona primero los rangos")
    }
}

function hideShowColumn(col_name) {
    var myform = new FormData() //form para actualizar la session variable
    myform.append('columName', col_name) //se asigna el nombre de la columna a cambiar

    var checkbox_val = document.getElementById(col_name).value;
    if (checkbox_val == "hide") {
        var all_col = document.getElementsByClassName(col_name);
        for (var i = 0; i < all_col.length; i++) {
            all_col[i].style.display = "none";
        }
        //document.getElementById(col_name+"_head").style.display="none";
        document.getElementById(col_name).value = "show";
        myform.append('valueColumn', 'hide') //se asigna la acción (hide or show)
    } else {
        var all_col = document.getElementsByClassName(col_name);
        for (var i = 0; i < all_col.length; i++) {
            all_col[i].style.display = "table-cell";
        }
        //document.getElementById(col_name+"_head").style.display="table-cell";
        document.getElementById(col_name).value = "hide";
        myform.append('valueColumn', 'show') //se asigna la acción (hide or show)
    }
    //se actualiza la session var para las columnas cambiadas
    fetch(base_url_js + 'UsersAdmin/setColumnFetch', {
            method: 'POST',
            body: myform
        })
        .then(function(response) {
            if (response.ok) {
                return response.json()
            } else {
                throw "Error en la llamada fetch"
            }
        })
        .then(function(myJson) {
            //console.log(myJson)
        })
        .catch(function(error) {
            console.log("catch: " + error)
        })
}

function hideShowAll() {
    const valueCheckAll = document.getElementById('checkAll').value //valor actual del check todos
    var checkBoxes = document.querySelectorAll('.checkColumns') //se obtiene los checks de las columnas del filtro actual
        //se convierte todo a hide o todo a show ademas de desmarcar o marcar todos los checked
    if (valueCheckAll === 'hide') {
        checkBoxes.forEach(function(element, index, array) {
            if (element.value = 'show') {
                element.value = 'hide'
                element.checked = false
            }
        })
        document.getElementById('checkAll').value = 'show'
    } else {
        checkBoxes.forEach(function(element, index, array) {
            if (element.value = 'hide') {
                element.value = 'show'
                element.checked = true
            }
        })
        document.getElementById('checkAll').value = 'hide'
    }

    //se procede a mostrar u ocultar todo
    var columnsNames = document.querySelectorAll('th')
    columnsNames.forEach(function(element, index, array) {
        if (element.className.match(/column.*/))
            hideShowColumn(element.className)
    });
}

var columnsNames2 = document.querySelectorAll('th')
columnsNames2.forEach(function(element, index, array) {
    if (element.className.match(/column.*/))
        hideShowColumn(element.className)
});