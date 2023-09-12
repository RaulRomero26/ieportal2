<div class="container-fluid">    

   <?php
        //print_r($data)
   ?>

    <div class="container mt-20">
        <form id='editar_clase'>

    <div class="row col-12 text-center my- mt-5">
		<h5>Edición de una clase</h5>
	</div>  


            <div class="form-group col-lg-6">
                <label for="estatus_clase" class="label-form">Estatus de la clase:</label>
                <select class="custom-select custom-select-sm" id="estatus_clase" name="estatus_clase">
                    <option value=1> ACTIVA</option>
                    <option value=0> INACTIVA </option>
                </select>
                <span class="span_error" id="estatus_clase_error"></span>
            </div>
      
            <div class="form-row mt-5">
                <div class="col-12" id="msg_principales"></div>
                <div class="form-group col-lg-6">
                    <label for="profesor_asignado" class="label-form">Profesor Asignado:</label>
                    <select class="custom-select custom-select-sm" id="profesor_asignado" name="profesor_asignado">
                        <option value="SD"> SELECCIONA UNA OPCION </option>
                    <?php foreach ($data['catalogos']['profesores'] as $item) : ?>
                        <option value="<?php echo $item->Id_maestro ?>"><?php echo $item->Nombre.' '.$item->Apellido_paterno.' '.$item->Apellido_materno?></option>
                    <?php endforeach ?>
                    </select>
                    <span class="span_error" id="profesor_asignado_error"></span>
                </div>

                <div class="form-group col-lg-6">
                    <label for="nivel_clase" class="label-form">Nivel de la clase:</label>
                    <select class="custom-select custom-select-sm" id="nivel_clase" name="nivel_clase">
                        <option value="SD"> SELECCIONA UNA OPCION </option>
                    <?php foreach ($data['catalogos']['niveles'] as $item) : ?>
                        <option value="<?php echo $item->Descripcion ?>"><?php echo $item->Descripcion ?></option>
                    <?php endforeach ?>
                    </select>
                    <span class="span_error" id="nivel_clase_error"></span>
                </div>
                
                <div class="col-5 col-md-5 form-group">
                    <div class="alert alert-warning mi_hide" role="alert" id="alertEditHorario">
                        Está realizando edición a un elemento.
                    </div>
                    <span class="span_error" id="Horario_error"></span>
                    <label for="dia_disponible">Dia disponible:</label>
                    <select class="custom-select custom-select-sm" id="dia_disponible" name="nivel_m">
                        <option value="LUN">Lunes</option>
                        <option value="MAR">Martes</option>
                        <option value="MIE">Miercoles</option>
                        <option value="JUE">Jueves</option>
                        <option value="VIE">Viernes</option>
                        <option value="SAB">Sabado</option>
                    </select>
                    <div class="invalid-feedback" id="dia_disponible-invalid">
                            El día es requerido.
                    </div>
								
				</div>
                <div class="col-5 col-md-5 form-group">
                    <label for="hora_disponible">Hora disponible:</label>
                    <input type="time" class="form-control" id="hora_disponible">
                    <div class="invalid-feedback" id="hora_disponible-invalid">
                            La hora es requerida.
                    </div>
                </div>
                <div class="col-12 col-md-2 form-group">
                    <button type="button" class="btn btn-primary button-movil-plus" onclick="onFormOtroSubmit()">+</button>
                </div>
                <div class="col-12 col-md-6 form-group">
                    <label for="TableHorarios">Horario disponible:</label>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="TableHorarios">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Día</th>
                                    <th scope="col">Hora</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody id="tbody_horarios">
                            </tbody>
                        </table>
                    </div>
				</div>        
            </div>
        
            <div class="form-row row">
                <div class="form-group col-lg-12">
                    <div class="alert alert-warning" role="alert" id="alertEditAlumno" style="display: none">
                        Está realizando edición a un elemento.
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="alumno_asignar" class="label-form">Seleccione el alumno a asignar:</label>
                    <select class="custom-select custom-select-sm" id="alumno_asignar" name="alumno_asignar">
                        <option value="SD"> SELECCIONA UNA OPCION </option>
                    <?php foreach ($data['catalogos']['alumnos'] as $item) : ?>
                        <option value="<?php echo $item->Id_alumno.'-'.$item->Nombre.' '.$item->Apellido_paterno.' '.$item->Apellido_materno ?>"><?php echo $item->Nombre.' '.$item->Apellido_paterno.' '.$item->Apellido_materno?></option>
                    <?php endforeach ?>
                    </select>
                    <span class="span_error" id="alumno_asignar_error"></span>
                </div>
                <button type="button" class="btn btn-primary button-movil-plus" id="button_query" onclick="onFormAlumnoSubmit()">+</button>
                <div class="form-group col-lg-12">
                    <p class="label-form ml-2"> Alumnos asignados a la clase</p>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="alumnos_table">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Alumno</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-5 mb-5">
                <div class="form-group col-lg-12">
                    <button type="button" class="btn btn-primary button-movil-plus" onclick="actualizar_clase(event)" id="button_actualizar_clase">GUARDAR</button>
                </div>
            </div>

        </form>   
    </div>
</div>