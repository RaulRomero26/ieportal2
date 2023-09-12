var id_actualizar = 0
var listaAlumnos;
var dias_clase=[]
var horas_clase=[]
window.onload = () => {
    console.log('actualizar clase');
    const urlParams = new URLSearchParams(window.location.search);
    const id_clase = urlParams.get('id_clase');
    id_actualizar = id_clase;
    console.log(id_clase);

    var myFormData = new FormData()
    myFormData.append('id_clase', id_clase)
    //console.log(myFormData.get('no_grupo'))
    fetch(base_url_js + 'Clases/getClase', {
        method: 'POST',
        body: myFormData
    })

    .then(res => res.json())

    .then(data => {
        //console.log(data);

        /* --------- seleccion de los input -------------*/
        let input_profesor = document.querySelector('#profesor_asignado');
        let input_nivel = document.querySelector('#nivel_clase');
        let input_status = document.querySelector('#estatus_clase');

        input_profesor.value = data.clase[0].id_profesor;
        input_nivel.value = data.clase[0].nivel;
        input_status.value = data.clase[0].estatus;

        const rowsTableHorarios = data.horarios;
        //console.log(rowsTableHorarios)
        for (let i = 0; i < rowsTableHorarios.length; i++) {
            //console.log(rowsTableHorarios[i].dia,rowsTableHorarios[i].hora);
            let formData = {
                dia_disponible: rowsTableHorarios[i].dia,
                hora_disponible: rowsTableHorarios[i].hora,
            }
            dias_clase.push(rowsTableHorarios[i].dia)
            horas_clase.push(rowsTableHorarios[i].hora)
            //console.log(formData);
            insertNewRowOtro(formData);
        }
        //------para saber dia y hora
        //console.log("dias")
        //console.log(dias_clase)

        //console.log("horas")
        //console.log(horas_clase)
         //----
         today = new Date();
         //Nday=(today.getDay()==0)?7:today.getDay();
         //sumDay=7-Nday;
         //today.setDate(today.getDate()+sumDay+1);
         //console.log(today)
         //----------
        paseListaValido=0
        dias = ["DOM","LUN","MAR","MIE","JUE","VIE","SAB"];
        dia_actual=dias[(today.getDay()==0)?7:today.getDay()];
        hora_actual =today.toLocaleTimeString('en-US');  
        hora_actual=new Intl.DateTimeFormat(undefined,{timeStyle:"short"}).format(new Date());
        //hora_actual=hora_actual.split(":")
        //console.log("dia actual",dia_actual, hora_actual)
        for (let i=0;i<dias_clase.length;i++){
            if (dias_clase[i]==dia_actual){
                aux=horas_clase[i].split(":")
                //console.log(aux)
                //console.log(Number(aux[0])-1,Number(hora_actual[0]),Number(aux[0])+1,Number(hora_actual[0]))
                if (aux[1]=='00'){
                    inferior=50
                    superior=10
                }
                else{
                    inferior=20
                    superior=40
                }
                inferior=Number(aux[0])-1+':'+inferior
                superior=Number(aux[0])+1+':'+superior
                //console.log(inferior,superior)
                if(hora_actual>=inferior && hora_actual<=superior){
                    paseListaValido=1
                    pase_lista_dia=dias_clase[i] + " "+horas_clase[i]
                }
                //if(Number(aux[0])-1<=Number(hora_actual[0]) && Number(aux[0])+1>=Number(hora_actual[0]) ){
                //    paseListaValido=1
                //    pase_lista_dia=dias_clase[i] + " "+horas_clase[i]
                //}
            }
        }
        
       if (paseListaValido==0){
            document.getElementById("pase_lista_dia").value="Solo es posible el pase de lista en días y horarios de clases así como 10 minutos antes o despues de la clase"
            document.getElementById('div_pase_lista').style.display = 'none'
        }
       else{
            document.getElementById("pase_lista_dia").value="Pase de lista del día: "+pase_lista_dia
            const rowsTableAlumnos = data.alumnos;
            listaAlumnos = data.alumnos;
            //console.log('lista de alumnos fetch',listaAlumnos)
            //console.log(rowsTableHorarios)
            for (let i = 0; i < rowsTableAlumnos.length; i++) {
            
                let formData = {
                    alumno_asignar: rowsTableAlumnos[i].nombre_alumno,
                }
                //console.log(formData);
                insertNewRowAlumno(formData);
                insertNewRowAlumnoPaseLista(formData)
            }

            
            document.getElementById('div_pase_lista').style.display = 'block';
    }

    });

    document.getElementById("delegar_cancelar_select").addEventListener('change', fdelegar_cancelar)
    //document.getElementById("cancelar_clase").addEventListener('change', fdelegar_cancelar)
}

async function actualizar_clase(e) {
    var msg_principalesError = document.getElementById('msg_principales')
    e.preventDefault();

    const button = document.getElementById(e.target.id);
    button.innerHTML = `
        Guardando
        <div class="spinner-grow spinner-grow-sm" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `;
    var urlFetch = base_url_js + "Clases/actualizarClase";
   
    //console.log(urlFetch)
    var formClase = document.getElementById("editar_clase");
    var myFormData = new FormData(formClase);
    myFormData.append('id_clase', id_actualizar);
    myFormData.append('horarios_table', JSON.stringify(await readTableHorarios2()));
    myFormData.append('alumnos_table', JSON.stringify(await readTableAlumnos2()));

    for (var pair of myFormData.entries()) {
        console.log(pair[0]+ ', ' + pair[1]); 
    }

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
            msg_principalesError.innerHTML = '<div class="alert alert-success text-center" role="alert">Hubo un error en la actualizacion de la clase</div>';
        }
        else {
            window.scroll({
                top: 0,
                left: 100,
                behavior: 'smooth'
            });

            msg_principalesError.innerHTML = '<div class="alert alert-success text-center" role="alert">Clase actualizada con exito</div>';
            // setInterval(() => {
            //     window.location = base_url_js + "Clases/index";
            // }, 1000);
            // result.tab === '1' ? '' : document.getElementById('save-tab-0').style.display = 'block';
        }
    })

}
/* ------ Funcioones  para leer las tablas y enviarlas en el post -------- */
const readTableAlumnos2 = () => {
    const table = document.getElementById('alumnos_table');
    let alumnos = [];
    for (let i = 1; i < table.rows.length; i++) 
        alumnos.push({
            ['row']: {
                nombre_alumno: table.rows[i].cells[0].innerHTML,
        }
    });
    return alumnos;
}
const readTableHorarios2 = () => {
    const table = document.getElementById('TableHorarios');
    let horarios = [];
    for (let i = 1; i < table.rows.length; i++) 
        horarios.push({
            ['row']: {
                dia: table.rows[i].cells[0].innerHTML,
                hora: table.rows[i].cells[1].innerHTML,
        }
    });
    return horarios;
}