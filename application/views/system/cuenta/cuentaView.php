<div id="id_container1" class="container mt-1">
	
	<?php $infoUser = $data['infoUser'];//informacion del usuario?>
	<div class="card row mb-3" id="" aria-describedby="id_card">
		<div class="card-header text-center">
		    <h3 class="display-4">Mi cuenta</h3>
		</div>
		<?php echo (isset($data['resultStatus']))?$data['resultStatus']:""; //status del post (con exito o sin exito)?>
		
	  	<!--Cuerpo del card para mostrar la info del usuario-->
		<form class="card-body col-12" method="post" action="<?= base_url;?>Cuenta/index" enctype="multipart/form-data" accept-charset="utf-8">
			<div class="row mx-2">
				<div class="col-12 col-lg-6 my-lg-auto d-flex justify-content-center" id="img_contenedor">
					<!--Edición de imagen de usuario-->
					<div class="row">
						<div class="col-auto mx-auto mt-4">
				            <div id="preview_1" class="preview">
				            	<img id="img_user" class="img-fluid" alt="Responsive image" src="<?= base_url;?>public/media/users_img/<?= $infoUser->Id_Usuario."/".$infoUser->Path_Imagen_User;?>">
				            </div>
							
						</div>
						<div class="col-12 mt-3">
							<div class="row">
								<div class="col-8 mx-auto">
									<div id="id_image" class="input-group">
										<div class="custom-file">
											<label id="label_foto_file" class="custom-file-label" for="id_foto_file" data-browse="Buscar">Subir imagen</label>
									    	<input type="file" class="custom-file-input" id="id_foto_file" name="foto_file">
									  	</div>
									</div>
									<small id="error_img1" class="form-text text-danger">Tamaño máximo 8MB, formatos: jpg/png</small>
								</div>
							</div>
								
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6" id="id_info_cuenta">
					<!--Vista de información y posible cambio en la contraseña-->
					<div class="row">
						<div class="col-12 text-center" id="id_title_info">
							<h5 class="card-title">Información de la cuenta</h5>
						</div>
						<div class="col-12 mt-4 text-center">
							<!--espacios orden:  5 15 8 7 11 14-->
							<p><i class="material-icons">person</i> <span>Nombre completo:  </span> <strong><?= mb_strtoupper($infoUser->Nombre)." ".mb_strtoupper($infoUser->Ap_Paterno)." ".mb_strtoupper($infoUser->Ap_Materno)?>	</strong></p>
							<p><i class="material-icons">email        </i> <span>Email:  </span> <strong><?= $infoUser->Email?>	</strong></p>
							<p><i class="material-icons">domain       </i> <span>Área laboral:  </span> <strong><?= mb_strtoupper($infoUser->Area)?>	</strong></p>
							<p><i class="material-icons">assignment_ind</i> <span>Nombre Usuario:  </span> <strong><?= $infoUser->User_Name?>	</strong></p>
							<div class="row d-flex justify-content-center">
								<div class="col-auto">
									<i class="material-icons pass-icon">lock</i>
									
								</div>
								<div class="col-auto">
									<div class="form-group">
										<div id="id_pass" class="input-group">
							                <input id="id_input_pass" type="password" name="Password" class="form-control py-2 border-right-0 border" placeholder="Contraseña" required value="<?php echo (isset($infoUser))?$infoUser->Pass_Decrypt:"";?>" aria-describedby="button-addon2" >
							                <span class="input-group-append">
							                    <div id="id_pass_button" class="input-group-text bg-transparent"><i class="material-icons md-18 ssc view-password">visibility</i></div>
							                </span>
							            </div>
							            <!--small class="form-text text-muted">Contraseña de usueario</small-->
									</div> 
								</div>
									
									
								
							</div>
							<p><i class="material-icons">verified_user</i> <span>Estatus:  </span> <strong><?= ($infoUser->Estatus)?"ACTIVO":"INACTIVO";?>	</strong></p>
							
						</div>
					</div>
				</div>
			</div>
			<div class="row mt-4 mb-5 d-flex justify-content-center">
					<a href="<?= base_url;?>Inicio" id="backButton" class="btn mr-3">
						<i class="material-icons v-a-middle">arrow_back_ios</i>
        				<span class="v-a-middle">Regresar</span>
					</a>
					<button type="submit" id="mySubmit" class="btn ml-3" name="editarInfo">Guardar cambios</button>
			</div>		    
		</form>
		<div class="card-footer text-center">
		    <div class="row">
		    	<div class="col-12 text-center">
		    		<h6><?= "Fecha registro: ".$infoUser->Fecha_Format;?></h6>
		    	</div>
		    </div>
		</div>
	</div>
	

</div>