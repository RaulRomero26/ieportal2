$(function() {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
})

const search = document.getElementById('id_search'),
    filtroActual = document.getElementById('filtroActual'),
    search_button = document.getElementById('search_button');

search_button.addEventListener('click',buscarHistorialesCad);

function buscarHistorialesCad(e){
    let  myform = new FormData();
    myform.append('cadena', search.value);
    myform.append('filtroActual', filtroActual.value);

    fetch(base_url_js+'Historiales/buscarPorCadena',{
        method: 'POST',
        body: myform
    })
    .then(res=>res.json())
    .then(data=>{
        if(!(typeof(data) == 'string')){
            document.getElementById('id_tbody').innerHTML = data.infoTable.body;
            document.getElementById('id_thead').innerHTML = data.infoTable.header;
            document.getElementById('id_pagination').innerHTML = data.links;
            document.getElementById('id_link_excel').href = data.export_links.excel;
            document.getElementById('id_link_pdf').href = data.export_links.pdf;
            document.getElementById('id_total_rows').innerHTML = data.total_rows;
            document.getElementById('id_dropdownColumns').innerHTML = data.dropdownColumns;
            const columnsNames3 = document.querySelectorAll('th');
            columnsNames3.forEach((element,index,array)=>{
                if(element.className.match(/column.*/)){
                    hideShowColumn(element.className);
                }
            })
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
        }else{
            console.log(data);
        }
    })
}

const aplicarRangos = ()=>{
    const rango_inicio = document.getElementById('id_date_1').value,
        rango_fin = document.getElementById('id_date_2').value;
    
    if (rango_inicio != '' && rango_fin != '') {
        let fecha1 = new Date(rango_inicio);
        let fecha2 = new Date(rango_fin)

        let resta = fecha2.getTime() - fecha1.getTime()
        if(resta >= 0){
            document.getElementById('form_rangos').submit()
        }
        else{
            alert("Elige intervalos correctos")
        } 
    }
    else {
        alert("Selecciona primero los rangos")
    }
}

const hideShowColumn = (col_name)=>{


    let myform = new FormData();
    myform.append('columnName',col_name);

    const checkbox_val = document.getElementById(col_name).value;
    if(checkbox_val == 'hide'){
        const all_col = document.getElementsByClassName(col_name);
        for(let i=0;i<all_col.length;i++){
            all_col[i].style.display="none";
        }

        document.getElementById(col_name).value = 'show';
        myform.append('valueColumn','hide');
    }else{
        const all_col = document.getElementsByClassName(col_name);
        for(let i=0;i<all_col.length;i++){
            all_col[i].style.display='table-cell';
        }

        document.getElementById(col_name).value="hide";
        myform.append('valueColumn','show');
    }

    fetch(base_url_js+'Historiales/setColumnFetch',{
        method: 'POST',
        body: myform
    })
    .then((res)=>{
        if(res.ok){
            return res.json();
        }else{
            throw 'Erro Fetch'
        }
    })
    
}

const hideShowAll = ()=>{
    const valueCheckAll = document.getElementById('checkAll').value;
    const checkBoxes = document.querySelectorAll('.checkColumns');

    console.log(valueCheckAll);
    if (valueCheckAll === 'hide') {
        checkBoxes.forEach(function(element,index,array){
            if (element.value = 'show') {
                element.value = 'hide'
                element.checked = false
            }
        })
        document.getElementById('checkAll').value = 'show'
    }
    else{
        checkBoxes.forEach(function(element,index,array){
            if (element.value === 'hide') {
                element.value = 'show'
                element.checked = true
            }
        })
        document.getElementById('checkAll').value = 'hide'
    }
    
    const columnsNames = document.querySelectorAll('th')
    columnsNames.forEach(function(element, index, array){
        if (element.className.match(/column.*/)){
            hideShowColumn(element.className)
        }
    });
}

const checarCadena = (e)=>{
    if (search.value == "") {
        buscarHistorialesCad()
    }
}