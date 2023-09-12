<div class="container-fluid">
    
    <form id='pagos_maestro' enctype="multipart/form-data">
        <div class="container mt-20">
            <?php
            $id_maestro    = (isset($_GET['id_maestro'])) ? $_GET['id_maestro'] : '0';
                ?>
            <label>Editar pagos maestros</label>
            <input type="hidden" name="id_maestro" id="id_maestro" value=<?= $id_maestro ?>>
                <div class="form-row mt-5">
                    <div class="col-12" id="msg_principales"></div>
                    <div class="col-12 col-md-1 form-group">
								<label for="id_maestro">Id:</label>
								<input type="text" class="form-control" id="id_maestro" value="1" readonly>
                    </div>
                    <div class="col-12 col-md-4 form-group">
                        <label for="nombre_m">Nombre:</label>
                        <input type="text" class="form-control" id="nombre_m" readonly>
                    </div>
                    <div class="col-12 col-md-4 form-group">
                        <label for="apellido_pm">Apellido paterno:</label>
                        <input type="text" class="form-control" id="apellido_pm" readonly>
                    </div>
                    <div class="col-12 col-md-3 form-group">
                        <label for="apellido_mm">Apellido materno:</label>
                        <input type="text" class="form-control" id="apellido_mm" readonly>
                    </div>
                    <div class="col-12 col-md-2 form-group">
                        <label for="activo_m">Activo:</label>
                        <input type="text" class="form-control" id="activo_m" readonly>
                       
                    </div>
                    <div class="col-12 col-md-4 form-group">
                        <label for="correo_m">Correo:</label>
                        <input type="email" class="form-control" id="correo_m" readonly>
                    </div>
                    <div class="col-12 col-md-4 form-group">
                        <label for="telefono_m">Telefono:</label>
                        <input type="text" class="form-control" id="telefono_m" readonly>
                    </div>
                    <div class="col-12 col-md-3 form-group">
                        <label for="nivel_m">Nivel:</label>
                        <input type="text" class="form-control" id="nivel_m" readonly>
                        
                    </div> 

                    <!--
                        <div class="form-group col-lg-3">
                        <button type="button" class="btn btn-primary button-movil-plus mt-4" id="button_agregar_pago" onclick="agregarPago()">Agregar Pago</button>
                        </div>
                    -->
                </div>
        
        </div>
        <div class="container" id="container_PagosM"  style="display: none">
            <div class="form-row mt-5">
                        <div class="form-group col-lg-12">
                            <div class="alert alert-warning" role="alert" id="alertEditPago" style="display: none">
                                Está realizando edición a un elemento.
                            </div>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="id_pagom" class="label-form">ID:</label>
                            <input type="text" class="form-control form-control-sm " id="id_pagom" name="id_pagom"  readonly>
                            <span class="span_error" id="id_pagom_error" ></span>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="fecha_iniciom" class="label-form">Periodo de inicio:</label>
                            <input type="date" class="form-control form-control-sm " id="fecha_iniciom" name="fecha_iniciom" >
                            <span class="span_error" id="fecha_iniciom_error" style="display: none">Agregue periodo de inicio</span>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="fecha_finm" class="label-form">Periodo de fin:</label>
                            <input type="date" class="form-control form-control-sm " id="fecha_finm" name="fecha_finm" >
                            <span class="span_error" id="fecha_finm_error" style="display: none">Agregue periodo fin</span>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="fecha_pagom" class="label-form">Fecha de pago:</label>
                            <input type="date" class="form-control form-control-sm " id="fecha_pagom" name="fecha_pagom" >
                            <span class="span_error" id="fecha_pagom_error" style="display: none">Agregue fecha de pago</span>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="monto_pagom" class="label-form">Monto:</label>
                            <input type="number" class="form-control form-control-sm " id="monto_pagom" name="monto_pagom"  >
                            <span class="span_error" id="monto_pagom_error" style="display: none">Agregue monto</span>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="desc_pagom" class="label-form">Descripción:</label>
                            <select class="custom-select custom-select-sm" id="desc_pagom" name="desc_pagom">
                                <option value="PAGADO">PAGADO</option>
                                <option value="PENDIENTE">PENDIENTE</option>
							</select>
                            <span class="span_error" id="desc_pagom_error"></span>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="comentarios" class="label-form">Comentarios:</label>
                            <input type="text" class="form-control form-control-sm " id="comentarios" name="comentarios" >
                        </div>
                        <div class="form-group col-lg-3">
                            <button type="button" class="btn btn-primary button-movil-plus" id="button_query2" onclick="onFormPagoSubmit()">Guardar</button>
                        </div>
            </div> 
        </div>
        <div class="container">
            <div class="form-row row"> 
                <div class="form-group col-lg-12">
                    <label class="label-form">Pagos:</label>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="table_pagos_maestro">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">ID </th>
                                    <th scope="col">Periodo de inicio </th>
                                    <th scope="col">Periodo de fin</th>
                                    <th scope="col">Fecha de pago</th>
                                    <th scope="col">Monto</th>
                                    <th scope="col">Descripción</th>
                                    <th scope="col">Comentarios</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead >
                            <tbody class="smallfont" id="pagosm_table_body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        

        
        <div class="container">
            <div class="row mt-5 mb-5">
                <div class="form-group col-lg-12">
                    <button type="button" class="btn btn-primary button-movil-plus" onclick="crear_guardarPM(event)" id="button_grupos_editar">GUARDAR</button>
                </div>
            </div>
        
        </div>
    </form>
</div>