
const readTableAlumnosPaseLista = () => {
    const table = document.getElementById('alumnos_lista_table');
    let alumnos = [];
    for (let i = 1; i < table.rows.length; i++) {
        console.log(table.rows[i].cells[1].childNodes[0].childNodes[1].checked)
        alumnos.push({
            ['row']: {
                nombre_alumno: table.rows[i].cells[0].innerHTML.split('-')[1],
                id_alumno: table.rows[i].cells[0].innerHTML.split('-')[0],
                asistencia: table.rows[i].cells[1].childNodes[0].childNodes[1].checked
            }
        });
    }
    console.log(alumnos);
    return alumnos;
}


const insertNewRowAlumnoPaseLista = (data, type) => {

    console.log('data que llego',data)
    const table = document.getElementById('alumnos_lista_table').getElementsByTagName('tbody')[0];
    let newRow = table.insertRow(table.length);

    newRow.insertCell(0).innerHTML = data.alumno_asignar;

    if (type === undefined) {
        newRow.insertCell(1).innerHTML = `<div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="asistencia">
        <label class="form-check-label" for="asistencia">ASISTIO</label>
      </div>`
    }

}

const pasar_lista = async(e) => {
    e.preventDefault();
    //const pase_lista = readTableAlumnosPaseLista()

    const button = document.getElementById(e.target.id);
    button.innerHTML = `
        Guardando
        <div class="spinner-grow spinner-grow-sm" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `;
    var urlFetch = base_url_js + "Clases/pasarLista";
    const urlParams = new URLSearchParams(window.location.search);
    const id_clase = urlParams.get('id_clase');
    var myFormData = new FormData();
    myFormData.append('asistencias_table', JSON.stringify(await readTableAlumnosPaseLista()));
    myFormData.append('id_clase', id_clase)

    var today = new Date();

    // `getDate()` devuelve el día del mes (del 1 al 31)
    var day = today.getDate();

    // `getMonth()` devuelve el mes (de 0 a 11)
    var month = (today.getMonth() + 1).toString().padStart(2, "0");

    // `getFullYear()` devuelve el año completo
    var year = today.getFullYear();

    // muestra la fecha de hoy en formato `MM/DD/YYYY`
    console.log('fecha')
    console.log(`${month}/${day}/${year}`);
    myFormData.append('fecha_pase', `${year}-${month}-${day}`)

    myFormData.append('id_profesor',document.getElementById('id_maestro'))


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
            (document.getElementById("msg_pasar_lista")).innerHTML = '<div class="alert alert-warning text-center" role="alert">Hubo un error en el pase de lista</div>';
        }
        else {
            window.scroll({
                top: 0,
                left: 100,
                behavior: 'smooth'
            });
           (document.getElementById("msg_pasar_lista")).innerHTML = '<div class="alert alert-success text-center" role="alert">Pase de lista realizado correctamente</div>';
        }
    })
}
