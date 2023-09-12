/*Se comento el resto del codigo debido que se utilizo un archivo que ya existia
con la función de mostrar contraseña habilitada pero que no era llamado en la pantalla 
de login, debido a esto no se sabe si el archivo fue deshabilitado porque causaba
conflicto con otro o porque era parte de una versión temprana del proyecto*/

var checkbox = document.getElementById('check_pass');
var contrasena = document.getElementById('contrasena')
checkbox.addEventListener('change', function() {
    if (this.checked) {
        contrasena.type = 'text'
    } else {
        contrasena.type = 'password'
    }

});
/*

(function() {
    'use strict';
    window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

/* ----- ----- ----- Login fetch ----- ----- ----- 
const dataLogin = document.getElementById('data_login_modal');

let error_usuario_login = document.getElementById('error_usuario_login'),
    error_contrasena_login = document.getElementById('error_contrasena_login');

document.getElementById('btn_login_fetch').addEventListener('click', (e) => {
    e.preventDefault();
    let myFormData = new FormData(dataLogin),
        band = [],
        FV = new FormValidator(),
        i = 0;

    band[i++] = error_usuario_login.innerText = FV.validate(myFormData.get('User_Name'), 'required');
    band[i++] = error_contrasena_login.innerText = FV.validate(myFormData.get('Password'), 'required');

    let success = true
    band.forEach(element => {
        success &= (element == '') ? true : false
    })

    if (success) {
        fetch(base_url_js + 'Login/loginFetch', {
                method: 'POST',
                body: myFormData
            })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                if (!data.status) {
                    document.getElementById('error_login_feedback').innerText = data.error_message;
                } else {
                    if (document.getElementsByClassName('alert-session-create')) {
                        error_usuario_login.innerText = '';
                        error_contrasena_login.innerText = '';
                        $('#modalLogin').modal('hide')
                        errorsMsg = document.getElementsByClassName('alert-session-create');
                        for (let i = 0; i < errorsMsg.length; i++) {
                            errorsMsg[i].parentNode.innerHTML = `
                            <div class="alert alert-success text-center" id="id_alert_result" role="alert">
                                <p>Inicio de sesión exitoso. Vuelva a guardar el registro por favor.</p>
                            </div>
                        `;
                        }
                    }
                }
            });
    }
})

*/