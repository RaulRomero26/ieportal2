<?php

class FormValidator
{
	
	public function __construct(){}

	//valida el formato y si es real la fecha introducida
	public function validar_fecha($fecha){
		//formato yyyy-mm-dd
		$valores = explode('-', $fecha);
		if((count($valores) == 3) && checkdate(intval($valores[1]), intval($valores[2]), intval($valores[0]))){
			return true;
	    }
		return false;
	}

	//valida el formato y si es real la fecha introducida
	public function validar_hora($hora){
		//formato yyyy-mm-dd
		$valores = explode(':', $hora);
		if((count($valores) == 2) && intval($valores[0]) >= 0  && intval($valores[0]) <= 23 && intval($valores[1]) >= 0  && intval($valores[1]) <= 59){
			return true;
	    }
		return false;
	}

	//my starts with function
	public function startsWith ($string, $startString){ 
	    $len = strlen($startString); 
	    return (substr($string, 0, $len) === $startString); 
	}

	//obtiene el valor numerico entre los corchetes
	public function getSize($cad="[1]"){
		return substr($cad, strpos($cad,"[")+1, -1);
	}

	//función para validar un input
	public function validate($post="",$field="",$rules=""){
		//se inician los valores por defecto
		//si validation tiene un valor != '' entonces ocurrió un error en el form o la validación
		$validation = "definir bien las reglas...";
		//variables que involucran length de un input
		$max_length = -1;
		$min_length = 99999;
		$length 	= -1;
		if (!isset($post[$field])) {
			$validation = "Campo requerido";
		}
		elseif ($rules != "") {
			$field = $post[$field]; //se asigna el campo a validar
			$rules = str_replace(' ', '', $rules); //borrar todo espacio en blanco
			$rules = explode("|", $rules);	//separar cada regla

			//echo var_dump($rules);
			//variantes de length
			$ind = 0;
			foreach ($rules as $rule) {
				if ($this->startsWith($rule,"max_length")) {
					$max_length = $this->getSize($rule);
					$rules[$ind] = "max_length";
				}
				elseif ($this->startsWith($rule,"min_length")) {
					$min_length = $this->getSize($rule);
					$rules[$ind] = "min_length";
				}
				elseif ($this->startsWith($rule,"length")) {
					$length = $this->getSize($rule);
					$rules[$ind] = "length";
				}
				$ind++;
			}

			//proceso de validation dependiendo de la regla
			$seguir = true;
			for ($i=0; $i < count($rules); $i++) { 
				//echo $rules[$i]."____";
				switch ($rules[$i]) {

					case "required"://regla isset()
						if (is_array($field)) { //en caso de ser un checkbox
							if (count($field) > 0) 
								$validation = '';
							else{
								$validation = "Elige al menos una opción";
								$seguir = false;
							}
						}
						else if (isset($field) && trim($field) != "") 
								$validation = '';
						else{
								$validation = "Campo requerido";
								$seguir = false;
						}
					break;
					case "numeric"://regla is_numeric()
						if (is_numeric($field)) 
							$validation = '';
						else{
							$validation = "El campo debe ser un número";
							$seguir = false;
						}
					break;
					case "max_length"://regla count()
						if (strlen($field) <= $max_length) 
							$validation = '';
						else{
							$validation = "Tamaño máximo: $max_length";
							$seguir = false;
						}
					break;
					case "min_length"://regla count()
						if (strlen($field) >= $min_length) 
							$validation = '';
						else{
							$validation = "Tamaño mínimo: $min_length";
							$seguir = false;
						}
					break;
					case "length"://regla count()
						if (strlen($field) == $length) 
							$validation = '';
						else{
							$validation = "Tamaño exacto: $length";
							$seguir = false;
						}
					break;
					case "date"://regla validar_fecha()
						if ($this->validar_fecha($field)) 
							$validation = '';
						else{
							$validation = "Fecha incorrecta, intenta de nuevo";
							$seguir = false;
						}
					break;
					case "time"://regla validar_fecha()
						if ($this->validar_hora($field)) 
							$validation = '';
						else{
							$validation = "Hora incorrecta, intenta de nuevo";
							$seguir = false;
						}
					break;
					default: 
						$validation = "***"; //si algo salio mal en la sintaxis de las reglas
						$seguir = false;
				}
				//si se detecta un error se interrumpe el ciclo
				if (!$seguir) break;
			}
		}
		return $validation;
	}

}
?>