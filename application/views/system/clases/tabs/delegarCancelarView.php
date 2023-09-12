<div class="container">    
    
    <?php ?>
    <div class="row col-12 text-center my- mt-5">
		
        <h5>Delegar/Cancelar una clase</h5>
	</div>  
    <div class="col-12" id="msg_delegar_cancelar"></div>
    <div class="form-check form-switch">
        <select class="custom-select custom-select-sm" id="delegar_cancelar_select" name="delegar_cancelar_select">
            <option value="SD"> SELECCIONA UNA OPCION </option>
            <option value="D"> Delegar </option>
            <option value="C"> Cancelar </option>
        </select>
    </div>

    <div class="form-check form-switch" id="delegar_clase_div" style="display: none">
            <div class="col-12" id="msg_delegarClase"></div>
            <label for="delegar_profesor" class="label-form">Delegar al profesor:</label>
            <select class="custom-select custom-select-sm" id="delegar_profesor" name="delegar_profesor">
                <option value="SD"> SELECCIONA UNA OPCION </option>
            <?php foreach ($data['catalogos']['profesores'] as $item) : ?>
                <option value="<?php echo $item->Id_maestro ?>"><?php echo $item->Nombre.' '.$item->Apellido_paterno.' '.$item->Apellido_materno?></option>
            <?php endforeach ?>
            </select>
            <span class="span_error" id="delegar_profesor_error"></span>

            <label for="dia_clase_delegar" class="label-form">Delegar la clase del día:</label>
            <select class="custom-select custom-select-sm" id="dia_clase_delegar" name="dia_clase_delegar">
            </select>
            <span class="span_error" id="dia_clase_delegar_error"></span>
            <label for="comentario_delegar">Comentario acerca de la delegación de la clase:</label>
            <input type="text" class="form-control" id="comentario_delegar" value="">
            <div class="row mt-5 mb-5">
                <div class="form-group col-lg-12">
                    <button type="button" class="btn btn-primary button-movil-plus" onclick="delegar_clase(event)" id="delegar_clase_button">GUARDAR</button>
                </div>
            </div>
    </div>

    <div class="form-check form-switch" id="cancelar_clase_div" style="display: none">
        <div class="col-12" id="msg_cancelarClase"></div>
        <label for="dia_clase_cancelar" class="label-form">Cancelar la clase del día:</label>
        <select class="custom-select custom-select-sm" id="dia_clase_cancelar" name="dia_clase_cancelar">
        </select>
        <span class="span_error" id="dia_clase_cancelar_error"></span>
        <label for="comentario_cancelar">Comentario acerca de la cancelación de la clase:</label>
            <input type="text" class="form-control" id="comentario_cancelar" value="">
        <div class="row mt-5 mb-5">
            <div class="form-group col-lg-12">
                <button type="button" class="btn btn-primary button-movil-plus" onclick="cancelar_clase(event)" id="cancelar_clase_button">GUARDAR</button>
            </div>
        </div>
    </div>

</div>  