async function crear_guardar(e) {
    var msg_principalesError = document.getElementById('msg_principales')
    //console.log(e.target.id)
    //console.log("para guardar")
    e.preventDefault();

    const button = document.getElementById(e.target.id);
    button.innerHTML = `
        Guardando
        <div class="spinner-grow spinner-grow-sm" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `;
    var urlFetch = base_url_js + "GestorCasos";
    if (e.target.id == "button_grupos") {
        urlFetch += "/insertGrupoFetch";//insert
    } else if (e.target.id == "button_grupos_editar") {
        urlFetch += "/editGrupoFetch";//editar
    }
    //console.log(urlFetch)
    var formInsp = document.getElementById("grupo_delictivo");
    var myFormData = new FormData(formInsp);
    myFormData.append('integrantes_table', JSON.stringify(await readTableSenas()));
    myFormData.append('zonas_final', readTableZonas());
    myFormData.append('colonias_final', readTableColonias());
    myFormData.append('foto_grupo', JSON.stringify(await enviarImagenGrupo()));
    if(document.getElementById("images_row_grupo")!=null)
        myFormData.append('imagen_anterior', document.getElementById("images_row_grupo").src);
    for (var pair of myFormData.entries()) {
         //console.log(pair[0] + ', ' + pair[1]);
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
            msg_principalesError.innerHTML = '<div class="alert alert-success text-center" role="alert">Hubo un error en la actualización</div>';
        }
        else {
            window.scroll({
                top: 0,
                left: 100,
                behavior: 'smooth'
            });

            msg_principalesError.innerHTML = '<div class="alert alert-success text-center" role="alert">Información actualizada con éxito</div>';
            setInterval(() => {
                window.location = base_url_js + "GestorCasos/index";
            }, 900);
            // result.tab === '1' ? '' : document.getElementById('save-tab-0').style.display = 'block';
        }
    })

}
const readTableIntegrantes = () => {
    const table = document.getElementById('integrantes_banda');
    let integrantes = [];

    for (let i = 1; i < table.rows.length; i++) {
        integrantes.push({
            ['row']: {
                nombre_int: table.rows[i].cells[0].innerHTML,
                apep_int: table.rows[i].cells[1].innerHTML,
                apem_int: table.rows[i].cells[2].innerHTML,
                sexo_int: table.rows[i].cells[3].innerHTML,
                estado_int: table.rows[i].cells[4].innerHTML,
                alias_int: table.rows[i].cells[5].innerHTML,
                curp_int: table.rows[i].cells[6].innerHTML,
                udc_int: table.rows[i].cells[7].innerHTML,
                utc_int: table.rows[i].cells[8].innerHTML,
                face_int: table.rows[i].cells[9].innerHTML,
                asociado_int: table.rows[i].cells[10].innerHTML,
                antece_int: table.rows[i].cells[11].innerHTML,
                categoria_int: table.rows[i].cells[12].innerHTML
            }
        });
    }

    return integrantes;
}
const readTableZonas = () => {
    const table = document.getElementById('zonas_table');
    let zonas ="";
    for (let i = 1; i < table.rows.length; i++) 
        zonas += table.rows[i].cells[0].innerHTML+","
    return zonas;
}
const readTableColonias = () => {
    const table = document.getElementById('zonas_table');
    let colonias = "";
    for (let i = 1; i < table.rows.length; i++) 
        colonias += table.rows[i].cells[1].innerHTML+"$"
    return colonias;
}
const enviarImagenGrupo = async() => {
    //console.log()
   // const input =document.getElementById('imageContent_grupo').children[1].children[0];
  //  //console.log(input);
    
 //   if (input != undefined) {
    if (document.getElementById('images_row_grupo') != null) {
        const type = document.getElementById('images_row_grupo').classList[1],
        base64 = document.getElementById('images_row_grupo');
        //console.log(type);
        nameImage = 'fileFoto_grupo';
        let integrantes = [];
        if (type != 'File') {
            isPNG = base64.src.split('.');
            if (isPNG[1] != undefined) {
                await toDataURL(base64.src)
                    .then(myBase64 => {
                        integrantes.push(dataImageGrupo(type, nameImage, myBase64));
                    })
            } else {
                integrantes.push(dataImageGrupo(type, nameImage, base64.src));
            }
        } else {
            integrantes.push(dataImageGrupo(type, nameImage, null));
        }
        return integrantes;
    } else {
        return [];
    }
 //   }
    
    
}
const readTableSenas = async() => {
    const table = document.getElementById('integrantes_banda');
    let integrantes = [];
    if (table.rows.length > 1) {


        for (let i = 1; i < table.rows.length; i++) {
           
            const input = table.rows[i].cells[13].children[1].children[0];
            //console.log(table.rows[i].cells[13].children[1])
            //console.log(table.rows[i].cells[13].children[1].children[0])
            //console.log(input);
            
            if (input != undefined) {
                const type = input.children[2].classList[1],
                    base64 = document.getElementById('images_row_' + i);
                    //console.log(type);
                nameImage = 'foto_row' + i;
                if (type != 'File') {
                    isPNG = base64.src.split('.');
                    if (isPNG[1] != undefined) {
                        await toDataURL(base64.src)
                            .then(myBase64 => {
                                integrantes.push(dataImageSenas(table.rows[i].cells[0].innerHTML,table.rows[i].cells[1].innerHTML,table.rows[i].cells[2].innerHTML,
                                    table.rows[i].cells[3].innerHTML,table.rows[i].cells[4].innerHTML,table.rows[i].cells[5].innerHTML,table.rows[i].cells[6].innerHTML,
                                    table.rows[i].cells[7].innerHTML,table.rows[i].cells[8].innerHTML,table.rows[i].cells[9].innerHTML,table.rows[i].cells[10].innerHTML,
                                    table.rows[i].cells[11].innerHTML, table.rows[i].cells[12].innerHTML,type, nameImage, myBase64));
                            })
                    } else {
                        integrantes.push(dataImageSenas(table.rows[i].cells[0].innerHTML,table.rows[i].cells[1].innerHTML,table.rows[i].cells[2].innerHTML,
                            table.rows[i].cells[3].innerHTML,table.rows[i].cells[4].innerHTML,table.rows[i].cells[5].innerHTML,table.rows[i].cells[6].innerHTML,
                            table.rows[i].cells[7].innerHTML,table.rows[i].cells[8].innerHTML,table.rows[i].cells[9].innerHTML,table.rows[i].cells[10].innerHTML,
                            table.rows[i].cells[11].innerHTML, table.rows[i].cells[12].innerHTML, type, nameImage, base64.src));
                    }
                } else {
                    integrantes.push(dataImageSenas(table.rows[i].cells[0].innerHTML,table.rows[i].cells[1].innerHTML,table.rows[i].cells[2].innerHTML,
                        table.rows[i].cells[3].innerHTML,table.rows[i].cells[4].innerHTML,table.rows[i].cells[5].innerHTML,table.rows[i].cells[6].innerHTML,
                        table.rows[i].cells[7].innerHTML,table.rows[i].cells[8].innerHTML,table.rows[i].cells[9].innerHTML,table.rows[i].cells[10].innerHTML,
                        table.rows[i].cells[11].innerHTML,table.rows[i].cells[12].innerHTML, type, nameImage, null));
                }
            } else {
                integrantes.push(dataImageSenas(table.rows[i].cells[0].innerHTML,table.rows[i].cells[1].innerHTML,table.rows[i].cells[2].innerHTML,
                    table.rows[i].cells[3].innerHTML,table.rows[i].cells[4].innerHTML,table.rows[i].cells[5].innerHTML,table.rows[i].cells[6].innerHTML,
                    table.rows[i].cells[7].innerHTML,table.rows[i].cells[8].innerHTML,table.rows[i].cells[9].innerHTML,table.rows[i].cells[10].innerHTML,
                    table.rows[i].cells[11].innerHTML,table.rows[i].cells[12].innerHTML, null, null, null));
            }
        }
    }
   
    return integrantes;
}


const dataImageSenas = (nombre_int, apep_int, apem_int, sexo_int, estado_int, alias_int, curp_int, 
                        udc_int, utc_int, face_int, asociado_int, antece_int,categoria_int, typeImage, nameImage, dataImage) => {
    return {
        ['row']: {
            nombre_int: nombre_int,
            apep_int: apep_int,
            apem_int: apem_int,
            sexo_int: sexo_int,
            estado_int: estado_int,
            alias_int: alias_int,
            curp_int: curp_int,
            udc_int: udc_int,
            utc_int: utc_int,
            face_int: face_int,
            asociado_int: asociado_int,
            antece_int:  antece_int,
            categoria_int:  categoria_int,
            typeImage: typeImage,
            nameImage: nameImage,
            image: dataImage
        }
    }
    
}
const validateImages = async({integrantes}) => {
    let band = true;

    await senas.forEach(element => {
        if (element.row.typeImage === null) {
            band = false;
        }
    });

    return band;
}

const dataImageGrupo = (typeImage, nameImage, dataImage) => {
    return {
        ['row']: {
        typeImage: typeImage,
        nameImage: nameImage,
        image: dataImage
        }
    }
}
