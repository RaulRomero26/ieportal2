async function crear_clase(e) {
    var msg_principalesError = document.getElementById('msg_principales')
    e.preventDefault();

    const button = document.getElementById(e.target.id);
    button.innerHTML = `
        Guardando
        <div class="spinner-grow spinner-grow-sm" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `;
    var urlFetch = base_url_js + "Clases/insertarClase";
   
    //console.log(urlFetch)
    var formClase = document.getElementById("nueva_clase");
    var myFormData = new FormData(formClase);

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
            msg_principalesError.innerHTML = '<div class="alert alert-success text-center" role="alert">Hubo un error en la creacion de la clase</div>';
        }
        else {
            window.scroll({
                top: 0,
                left: 100,
                behavior: 'smooth'
            });

            msg_principalesError.innerHTML = '<div class="alert alert-success text-center" role="alert">Clase creada con exito</div>';
            setInterval(() => {
                window.location = base_url_js + "Clases/index";
            }, 1000);
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