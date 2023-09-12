/*  FUNCIONALIDADES DE LA TABLA DE FOTOS */

function uploadPhoto(apartado) {

    const canvas = document.getElementById('canvas' + apartado),
        src = canvas.toDataURL();

    if (apartado === 'fyh') {
        const tipo = document.getElementById('tipoFoto'),
            perfil = document.getElementById('perfilFoto');
        createElementFyH(src, tipo.value, perfil.value, 'Photo');
    } else {
        if (apartado === 'senas') {
            const index = canvas.classList.value;
            createElementSena(src, index, 'Photo');
            document.getElementById('fileSena_row' + index).value = "";
        } else {
            if (apartado === 'Inspecciones') {
                createElementInspecciones(src);
            } else {
                if (apartado === 'ObjRecuperados') {
                    createElementObjRecuperados(src, true);
                    document.getElementById('fileObjRecuperados').value = "";
                }
            }
        }
    }

    closeCamera(apartado);
}

function createElementFoto(src, index, type, view) {
    const div = document.getElementById('imageContent_row' + index);

    if (view === undefined) {
        div.innerHTML = `
            <div>
                <div class="d-flex justify-content-end">
                    <span onclick="deleteImageFoto(${index})" class="deleteFile">x</span>
                </div>
                <img class="img-fluid ${type}" id="images_row_${index}" width="100" src="${src}">
                <input type="hidden" class="${index} ${type}"/>
            </div>
        `;
    } else {
        div.innerHTML = `
            <div>
                <img class="img-fluid ${type}" id="images_row_${index}" width="100" src="${src}">
                <input type="hidden" class="${index} ${type}"/>
            </div>
        `;
    }
}

function createElementFotoGrupo(src, type, view) {
    const div = document.getElementById('imageContent_grupo');

    if (view === undefined) {
        div.innerHTML = `
            <div>
                <div class="d-flex justify-content-end">
                    <span onclick="deleteImageFoto('grupo')" class="deleteFile">x</span>
                </div>
                <img class="img-fluid ${type}" id="images_row_grupo" width="100" src="${src}">
                <input type="hidden" class="grupo ${type}"/>
            </div>
        `;
    } else {
        div.innerHTML = `
            <div>
                <img class="img-fluid ${type}" id="images_row_${index}" width="100" src="${src}">
                <input type="hidden" class="${index} ${type}"/>
            </div>
        `;
    }
}


function uploadFile(event, type) {
    console.log('file fotos');
    let file;
    if (type) {
        file = 'Photo';
    } else {
        file = 'File';
    }

    if (event.currentTarget.classList.contains('uploadFileFotos')) {
        if (validateImage(event.target)) {
            const src = URL.createObjectURL(event.target.files[0]);
            const row = event.currentTarget;
            const index = row.parentNode.parentNode.parentNode.parentNode.rowIndex;
            createElementFoto(src, index, 'File');
        } else {
            document.getElementById('msg_fotosParticulares').innerHTML = '<div class="alert alert-warning text-center" role="alert">Verificar el archivo cargado.<br>Posibles errores: <br> - Archivo muy pesado (Máximo 8 megas). <br> -Extensión no aceptada (Extensiones aceptadas: jpeg, png, jpg, PNG).</div>';
            window.scroll({
                top: 0,
                left: 100,
                behavior: 'smooth'
            });
        }
    }

    if (event.currentTarget.classList.contains('uploadFileVideos')) {
        if (validateImage(event.target)) {
            const src = URL.createObjectURL(event.target.files[0]);
            const row = event.currentTarget;
            const index = row.parentNode.parentNode.parentNode.parentNode.rowIndex;
            createElementVideo(src, index, 'File');
        } else {
            document.getElementById('msg_fotosParticulares').innerHTML = '<div class="alert alert-warning text-center" role="alert">Verificar el archivo cargado.<br>Posibles errores: <br> - Archivo muy pesado (Máximo 8 megas). <br> -Extensión no aceptada (Extensiones aceptadas: jpeg, png, jpg, PNG).</div>';
            window.scroll({
                top: 0,
                left: 100,
                behavior: 'smooth'
            });
        }
    }

    if (event.currentTarget.classList.contains('uploadFileFotosGrupo')) {
        if (validateImage(event.target)) {
            const src = URL.createObjectURL(event.target.files[0]);
            const row = event.currentTarget;
            const index = row.parentNode.parentNode.parentNode.parentNode.rowIndex;
            createElementFotoGrupo(src, 'File');
        } else {
            document.getElementById('msg_fotosParticulares').innerHTML = '<div class="alert alert-warning text-center" role="alert">Verificar el archivo cargado.<br>Posibles errores: <br> - Archivo muy pesado (Máximo 8 megas). <br> -Extensión no aceptada (Extensiones aceptadas: jpeg, png, jpg, PNG).</div>';
            window.scroll({
                top: 0,
                left: 100,
                behavior: 'smooth'
            });
        }
    }

};

const validateImage = (image) => {
    const size = image.files[0].size,
        allowedExtensions = /(.jpg|.jpeg|.png|.PNG)$/i;
    if (!allowedExtensions.exec(image.value)) {
        return false;
    }
    /* if(size > 8000000){
        return false;
    } */
    return true;
}

function deleteImageFoto(index) {
    const div = document.getElementById('imageContent_row' + index);
    document.getElementById('fileFoto_row' + index).value = '';

    div.innerHTML = '';
}

/*  FUNCIONALIDADES DE LA TABLA DE VIDEOS */

function createElementVideo(src, index, type, view) {
    const div = document.getElementById('imageContentV_row' + index);

    if (view === undefined) {
        div.innerHTML = `
            <div>
                <div class="d-flex justify-content-end">
                    <span onclick="deleteImageVideo(${index})" class="deleteFile">x</span>
                </div>
                <img class="img-fluid ${type}" id="imagesV_row_${index}" width="100" src="${src}">
                <input type="hidden" class="${index} ${type}"/>
            </div>
        `;
    } else {
        div.innerHTML = `
            <div>
                <img class="img-fluid ${type}" id="imagesV_row_${index}" width="100" src="${src}">
                <input type="hidden" class="${index} ${type}"/>
            </div>
        `;
    }
}

function deleteImageVideo(index) {
    const div = document.getElementById('imageContentV_row' + index);
    document.getElementById('fileVideo_row' + index).value = '';

    div.innerHTML = '';
}


const toDataURL = url => fetch(url)
    .then(res => res.blob())
    .then(blob => new Promise((resolve, reject) => {
        const reader = new FileReader()
        reader.onloadend = () => resolve(reader.result)
        reader.onerror = reject
        reader.readAsDataURL(blob)
    }))