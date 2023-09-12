//condiciones para permisos
var passButton  =   document.getElementById('id_pass_button')
var inputPass   =   document.getElementById('id_input_pass') 
var mySubmit    =   document.getElementById('mySubmit')
var myForm      =   document.getElementById('id_form')
document.getElementById('error_img1').style.display = "none"
function viewPassword(){
    if (inputPass.type == 'text') {
        inputPass.type = 'password'
    }
    else{
        inputPass.type = 'text'
    }
    
}

passButton.addEventListener('click',viewPassword)



function disablePermisos(){
    var permisos = document.getElementsByClassName('checkPermisos');
        permisos = Array.prototype.slice.call( permisos, 0 );
    
    if (document.getElementById('Modo_Admin').checked) 
        permisos.forEach(element => {element.disabled = true});
    
    else
        permisos.forEach(element => {element.disabled = false});    
}

disablePermisos()

//procesamiento de la imagen a subir
var img_1 = document.getElementById("id_foto_file")

var p_1 = document.getElementById('preview_1')

p_1.style.display = "none"

img_1.onchange = function(e) {
    let formatosImg = 'image/png image/jpeg image/jpg'

    //console.log(img_1.files[0])
    //console.log(img_1.files[0].type)
    let reader = new FileReader();
    if (typeof img_1.files[0] !== 'undefined') {
        if (img_1.files[0].size <= 8000000) { //size max 8MB
            if(formatosImg.includes(img_1.files[0].type+"")){
                document.getElementById('error_img1').style.display = "none"
                reader.onload = function() {
                    let image = document.createElement('img');

                    document.getElementById('label_foto_file').textContent = e.target.files[0].name

                    image.src = reader.result;
                    p_1.style.display = "block"
                    p_1.innerHTML = '';
                    p_1.append(image);
                    //alert('Tamaño: ' + img_1.files[0].size)
                };

                reader.readAsDataURL(e.target.files[0]);
            }
            else{
                delete img_1.files[0];
                p_1.style.display = "none"
                document.getElementById('error_img1').style.display = "block"
                img_1.value = ""
                document.getElementById('label_foto_file').textContent = "Subir imagen"
            }

        }
    } 
    else {
        delete img_1.files[0];
        p_1.style.display = "none"
        document.getElementById('error_img1').style.display = "block"
        img_1.value = ""
        document.getElementById('label_foto_file').textContent = "Subir imagen"
    }

}

/*JS para activar todos o ninguno de los permisos marcados*/

var all_juridico = document.getElementById('all_juridico')

all_juridico.addEventListener('change',change_all)

function change_all(e){
    switch(e.target.id){
        case 'all_juridico':
            if (all_juridico.value === '1') {
                document.getElementById('Ju_Create').checked = true
                document.getElementById('Ju_Read').checked = true
                document.getElementById('Ju_Update').checked = true
                document.getElementById('Ju_Delete').checked = true
                all_juridico.value = '0'
            }
            else{
                document.getElementById('Ju_Create').checked = false
                document.getElementById('Ju_Read').checked = false
                document.getElementById('Ju_Update').checked = false
                document.getElementById('Ju_Delete').checked = false
                all_juridico.value = '1'
            }
        break
    }
}