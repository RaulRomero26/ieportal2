class FormValidator {
    //valida el formato y si es real la fecha introducida
    /*valida fecha*/
    validarFecha(fecha) {
            var fechaf = fecha.split("-")
            var d = fechaf[2]
            var m = fechaf[1]
            var y = fechaf[0]
            return m > 0 && m < 13 && y > 0 && y < 32768 && d > 0 && d <= (new Date(y, m, 0)).getDate()
        }
        /*valida fecha*/
    validarHora(hora) {
        var horaH = hora.split(":")
        var hrs = horaH[0]
        var mins = horaH[1]
        return hrs >= 0 && hrs <= 23 && mins >= 0 && mins <= 59
    }

    validarMail(correo) {
        var patron = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i
        if (patron.test(correo))
            return correo
    }

    //my starts with function
    startsWith(cad, starts) {
        return cad.startsWith(starts)
    }

    //obtiene el valor numerico entre los corchetes
    getSize(cad = '[1]') {
        return cad.substring(cad.indexOf('[') + 1, cad.indexOf(']'))
    }

    validate(field, rules = "") {

        //se inician los valores por defecto
        //si validation tiene un valor != '' entonces ocurrió un error en el form o la validación
        var validacion = ""
            //variables que involucran length de un input
        var max_length = -1
        var min_length = 99999
        var length = -1

        if (rules != "") {

            rules = rules.replace(/ /g, '') //borrar todo espacio en blanco
            rules = rules.split("|") //separar cada regla

            //variantes de min-max-length
            var ind = 0
            rules.forEach((rule, index, array) => {
                if (rule.startsWith("max_length")) {
                    max_length = this.getSize(rule)
                    rules[ind] = "max_length"
                } else if (rule.startsWith("min_length")) {
                    min_length = this.getSize(rule)
                    rules[ind] = "min_length"
                } else if (rule.startsWith("length")) {
                    length = this.getSize(rule)
                    rules[ind] = "length"
                }
                ind++
            })

            //proceso de validation dependiendo de la regla
            var seguir = true
            for (var i = 0; i < rules.length; i++) {
                switch (rules[i]) {

                    case 'required': //regla isset()
                        if (field == null || field.trim() == '' || field == 'NA') {
                            validacion = "Campo requerido"
                            seguir = false
                        }
                        break
                    case 'numeric': //regla is_numeric()
                        if (!Number.isInteger(parseInt(field))) {
                            validacion = "El campo debe ser un número"
                            seguir = false
                        }
                        break
                    case 'max_length': //regla count()
                        if (!(field.length <= max_length)) {
                            validacion = "Tamaño máximo: " + max_length
                            seguir = false
                        }
                        break
                    case 'min_length': //regla count()
                        if (!(field.length >= min_length)) {
                            validacion = "Tamaño mínimo: " + min_length
                            seguir = false
                        }
                        break
                    case 'length': //regla count()
                        if (!(field.length == length)) {
                            validacion = "Tamaño exacto: " + length
                            seguir = false
                        }
                        break
                    case 'date': //regla validar_fecha()
                        if (!this.validarFecha(field)) {
                            validacion = "Fecha incorrecta, intenta de nuevo"
                            seguir = false
                        }
                        break
                    case 'time': //regla validar_fecha()
                        if (!this.validarHora(field)) {
                            validacion = "Hora incorrecta, intenta de nuevo"
                            seguir = false
                        }
                        break

                    case 'mail':
                        if (!this.validarMail(field)) {
                            validacion = "Ingresa un correo valido"
                            seguir = false
                        }
                        break
                    default: //si algo salio mal en la sintaxis de las reglas
                        validacion = "***"
                        seguir = false
                }
                //si se detecta un error se interrumpe el ciclo
                if (!seguir) break
            }
        }
        return validacion
    }
}