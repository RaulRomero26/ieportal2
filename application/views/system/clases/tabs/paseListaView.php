<div class="container">    
    
    <?php ?>
    <div class="col-12" id="msg_pasar_lista"></div>
    <div class="row col-12 text-center my- mt-5">
        <h5>Pase de lista </h5>
        <input type="text" class="form-control" id="pase_lista_dia" readonly>
	</div>  
    

    <div class="form-row row">
        <div class="form-group col-lg-12">
            <p class="label-form ml-2"> Alumnos asignados a la clase</p>
            <div class="table-responsive">
                <table class="table table-bordered" id="alumnos_lista_table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Alumno</th>
                            <th scope="col">Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-5 mb-5" id="div_pase_lista" style="display: none">
        <div class="form-group col-lg-12">
            <button type="button" class="btn btn-primary button-movil-plus" onclick="pasar_lista(event)" id="button_pasar_lista">PASAR LISTA</button>
        </div>
    </div>

</div>  