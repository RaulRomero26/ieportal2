//ocultar mensaje de error inicial
document.getElementById('error_img1').style.display = "none"
//procesamiento de la imagen a subir
var img_1 = document.getElementById("id_foto_file")

var p_1 = document.getElementById('preview_1')



img_1.onchange = function(e) {
    let formatosImg = 'image/png image/jpeg image/jpg'

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

/*JS del password del usuario*/

var passButton  =   document.getElementById('id_pass_button')
var inputPass   =   document.getElementById('id_input_pass') 

function viewPassword(){
    if (inputPass.type == 'text') {
        inputPass.type = 'password'
    }
    else{
        inputPass.type = 'text'
    }
    
}

passButton.addEventListener('click',viewPassword)