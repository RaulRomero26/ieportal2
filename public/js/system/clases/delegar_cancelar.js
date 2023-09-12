

function fdelegar_cancelar(e){
    console.log(document.getElementById("delegar_cancelar_select").value)
    if (document.getElementById("delegar_cancelar_select").value=='D') {
        // do something if checked
        console.log("delegar")
        document.getElementById("delegar_clase_div").style.display="block"
        document.getElementById("cancelar_clase_div").style.display="none"
        //document.getElementById("dia_clase_delegar").style="block"
        selector_dias=document.getElementById("dia_clase_delegar")
        for (let i = 0; i < dias_clase.length; i++) {
            option=document.createElement('option')
            option.value=dias_clase[i]
            option.text=dias_clase[i]
            selector_dias.appendChild(option)
        }
    } else {
        console.log("cancelar")
        document.getElementById("cancelar_clase_div").style.display="block"
        document.getElementById("delegar_clase_div").style.display="none"
        //document.getElementById("dia_clase_cancelar").style="block"
        selector_dias=document.getElementById("dia_clase_cancelar")
        for (let i = 0; i < dias_clase.length; i++) {
            option=document.createElement('option')
            option.value=dias_clase[i]
            option.text=dias_clase[i]
            selector_dias.appendChild(option)
        }
        // do something else otherwise
    }
}
function delegar_clase(e){
    console.log("delegar f")
    if ( document.getElementById("delegar_profesor").value!='SD'){

        //enviar    
        var myform = new FormData()
        const urlParams = new URLSearchParams(window.location.search);
        myform.append("delegar_profesor", document.getElementById("delegar_profesor").value)
        myform.append("id_clase", urlParams.get('id_clase'))
        myform.append("dia", document.getElementById("dia_clase_delegar").value)
        myform.append("comentarios", document.getElementById("comentario_delegar").value)
        urlFetch  = base_url_js + "/Clases/delegarClase";//editar
        fetch(urlFetch, {
            method: 'POST',
            body: myform
        })
    
        .then(res => res.json())
    
        .then(data => {
            
            //console.log(data.status)
            if (!data.status) {
                (document.getElementById("msg_delegarClase")).innerHTML = '<div class="alert alert-warning text-center" role="alert">Hubo un error en la actualización</div>';
            }
            else {
                window.scroll({
                    top: 0,
                    left: 100,
                    behavior: 'smooth'
                });
    
                (document.getElementById("msg_delegarClase")).innerHTML = '<div class="alert alert-success text-center" role="alert">Clase delegada</div>';
               
            }
        })

    }
    else{
        (document.getElementById("msg_delegarClase")).innerHTML = '<div class="alert alert-warning text-center" role="alert">Eliga un profesor para delegar la clase</div>';
    }
}

function cancelar_clase(e){
    console.log("cancelar f")
      
    var myform = new FormData()
    const urlParams = new URLSearchParams(window.location.search);
    myform.append("id_clase", urlParams.get('id_clase'))
    myform.append("dia", document.getElementById("dia_clase_cancelar").value)
    myform.append("comentarios", document.getElementById("comentario_cancelar").value)
    urlFetch  = base_url_js + "/Clases/cancelarClase";//editar
    fetch(urlFetch, {
        method: 'POST',
        body: myform
    })

    .then(res => res.json())

    .then(data => {
        
        //console.log(data.status)
        if (!data.status) {
            (document.getElementById("msg_cancelarClase")).innerHTML = '<div class="alert alert-warning text-center" role="alert">Hubo un error en la actualización</div>';
        }
        else {
            window.scroll({
                top: 0,
                left: 100,
                behavior: 'smooth'
            });

            (document.getElementById("msg_cancelarClase")).innerHTML = '<div class="alert alert-success text-center" role="alert">Clase cancelada</div>';
            
        }
    })
}