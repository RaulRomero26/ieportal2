window.onload = function() {
    var no_grupo = document.getElementById('no_grupo')
    var myFormData = new FormData()
    myFormData.append('no_grupo', no_grupo.value)
    //console.log(myFormData.get('no_grupo'))
    fetch(base_url_js + 'GestorCasos/getGrupo', {
        method: 'POST',
        body: myFormData
    })

    .then(res => res.json())

    .then(data => {
        //console.log(data)
        //document.getElementById('no_grupo').value=
        //console.log("banda",data['grupo']['ID_BANDA'])
        document.getElementById('principal_actividad').value=data['grupo']['PRINCIPALES_DELITOS']
        document.getElementById('nombre_grupo').value=data['grupo']['NOMBRE_BANDA']
        document.getElementById('delitos_asociados').value=data['grupo']['ACTIVIDADES_ILEGALES']
        document.getElementById('peligrosidad').value=data['grupo']['PELIGROSIDAD']
        document.getElementById('antecedentes').value=data['grupo']['ANTECEDENTES']

        pathImagesFotos = `${base_url_js}public/files/GestorCasos/${data['grupo']['ID_BANDA']}/Grupo/`
        pathPlaceholder = `${base_url_js}public/files/GestorCasos/placeholderprofile.jpg`

        pathImagesFotosG = `${base_url_js}public/files/GestorCasos/${data['grupo']['ID_BANDA']}/Grupo/`
        pathPlaceholderG = `${base_url_js}public/files/GestorCasos/placeholdergrupo.jpg`
        
        srcImageG = data['grupo']['FOTOGRAFIA']
        //console.log(srcImageG);
        srcImageG = srcImageG.split('?')
        //console.log(srcImageG);
        if (data['grupo']['FOTOGRAFIA'].length != 0 && srcImageG[0] != '.png') {
            createElementFotoGrupo(pathImagesFotosG + srcImageG[0], 'Photo');
        }else{
            createElementFotoGrupo(pathPlaceholderG, 'Photo')
        }
        const rowsTableIntegrantes = data.integrantes;
        //console.log(rowsTableIntegrantes)
        for (let i = 0; i < rowsTableIntegrantes.length; i++) {
    
            let formData = {
                nombre_int: rowsTableIntegrantes[i].NOMBRE,
                apep_int: rowsTableIntegrantes[i].APELLIDO_PATERNO,
                apem_int: rowsTableIntegrantes[i].APELLIDO_MATERNO,
                sexo_int: rowsTableIntegrantes[i].SEXO,
                estado_int: rowsTableIntegrantes[i].ESTATUS,
                alias_int: rowsTableIntegrantes[i].ALIAS,
                curp_int: rowsTableIntegrantes[i].CURP,
                udc_int: rowsTableIntegrantes[i].UDC,
                utc_int: rowsTableIntegrantes[i].UTC,
                face_int: rowsTableIntegrantes[i].PERFIL_FACEBOOK,
                asociado_int: rowsTableIntegrantes[i].DESCRIPCION,
                antece_int: rowsTableIntegrantes[i].ANTECEDENTES_PERSONA,
                categoria_int: rowsTableIntegrantes[i].TIPO,
            }
            insertNewRowIntegrante(formData);

            srcImage = rowsTableIntegrantes[i].PATH_IMAGEN
            //console.log(srcImage);
            srcImage = srcImage.split('?')
            //console.log(srcImage);

            if (rowsTableIntegrantes[i].PATH_IMAGEN.length != 0 && srcImage[0] != '.png') {
                createElementFoto(pathImagesFotos + srcImage[0], i + 1, 'Photo');
                //console.log("if png",pathImagesFotos + srcImage[0])
            }else{
                createElementFoto(pathPlaceholder, i + 1, 'Photo')
                //console.log("else png")
            }

            /*srcImage = rowsTableIntegrantes[i].PATH_IMAGEN
            //console.log(srcImage);
            srcImage = srcImage.split('?')
            //console.log(srcImage);
            imagen = document.getElementById(`imageContent_row${i+1}`);
            //console.log(imagen);
            imagen.innerHTML += `
                <div>
                    <div class="d-flex justify-content-end">
                        <span onclick="deleteImageFoto(${i+1}) class="deleteFile">x</span>
                    </div>

                    <img class="img-fluid File images_row_${i+1}" id="images_row_${i+1}" width="100" src="${ base_url_js}public/files/GestorCasos/${no_grupo.value}/Grupo/${srcImage[0]}">
                    <input type="hidden" class="${i+1} File"/>
                </div>
            `*/
        }
        zonas_s=data['grupo']['ZONAS'].split(",")
        colonias_s=data['grupo']['COLONIAS'].split("$")
        //console.log(zonas_s,colonias_s)
        for(let i=0;i<zonas_s.length;i++){
            if(zonas_s[i].trim()!="" && colonias_s[i].trim()!=""){
                let formData = {
                    zona_int: zonas_s[i],
                    colonias_int: colonias_s[i]
                }
                insertNewRowZona(formData);
            }
            
        }

    })
}