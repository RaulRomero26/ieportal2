<?php //echo var_dump($data['columns_ED']);?>
<div class="container mt-5 mb-3">
	<div class="row">
		<div class="col-auto mr-auto my-auto">
			<h5 class="title-width my-auto">Usuarios del sistema</h5>
		</div>
		<div class="col-auto my-auto">
			<a class="btn btn-opacity" href="<?= base_url;?>UsersAdmin/crearUser" >Nuevo usuario</a>
		</div>
	</div>
	
	<div class="row mt-5">
		<div class="col-6 col-lg-auto mr-lg-auto my-2 my-lg-auto  d-flex justify-content-center">
			
			<!-- Button trigger modal -->
			<button type="button" class="btn btn-filtro" data-toggle="dropdown" id="id_filtros">
				<i class="material-icons md-30 v-a-middle" >filter_alt</i>
				<span class="v-a-middle" >Filtros</span>
			</button>
			<!--Dropdown filter content-->
			<div class="dropdown-menu" aria-labelledby="id_filtros">
			    
				<a class="dropdown-item <?= ($data['filtroActual']==1)?'active':'';?>" href="<?= base_url;?>UsersAdmin/index/?filtro=1">Todos los usuarios</a>
				<a class="dropdown-item <?= ($data['filtroActual']==2)?'active':'';?>" href="<?= base_url;?>UsersAdmin/index/?filtro=2">Administradores</a>
				<a class="dropdown-item <?= ($data['filtroActual']==3)?'active':'';?>" href="<?= base_url;?>UsersAdmin/index/?filtro=3">Otros</a>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item <?= ($data['filtroActual']==11)?'active':'';?>" href="#" class="btn btn-filtro" data-toggle="modal" data-target="#filtro_rangos">
					<span class="v-a-middle" >Por rango de fechas</span>
				</a>
			</div>
		</div>
		<div class="col-6 col-lg-auto mr-lg-auto my-2 my-lg-auto  d-flex justify-content-center">
			<!-- Button trigger modal -->
			<button type="button" class="btn btn-filtro" data-toggle="dropdown" id="columnas_filtro">
				<i class="material-icons md-30 v-a-middle" >table_chart</i>
				<span class="v-a-middle" >Columnas</span>
			</button>
			<!--Dropdown filter content-->
			<div id="id_dropdownColumns" class="dropdown-menu" aria-labelledby="columnas_filtro">
				<?= $data['dropdownColumns'];?>
			</div>
		</div>
		<div class="col-12 col-lg-auto mr-lg-auto my-2 my-lg-auto  d-flex justify-content-center">
			<?php $cadena = (isset($data['cadena'])) ? $data['cadena'] : ""; ?>
			<div class="input-group">
				<input id="id_search" type="search" name="busqueda" value="<?= $cadena; ?>" id="busqueda" class="form-control py-2 border-right-0 border" placeholder="Buscar" required="required" aria-describedby="button-addon2" onkeyup="return checarCadena(event)" onchange="return checarCadena(event)">
				<span class="input-group-append">
					<div id="search_button" class="input-group-text bg-transparent"><i class="material-icons md-18 ssc search" id="filtro">search</i></div>
				</span>
			</div>
		</div>
		<div class="col-6 col-lg-auto mr-lg-auto my-auto  d-flex justify-content-center" id="id_total_rows">
			
			Total registros: <?= (isset($data['total_rows']))?$data['total_rows']:"350";?>
		</div>
		<div class="col-6 col-lg-auto mr-lg-auto my-2 my-lg-auto d-flex justify-content-center">
			<div class="row">
				<div id="buttonsExport" class="col-12">
					<?php 
						$cadenaExport = (isset($data['cadena'])) ? ("&cadena=" . $data['cadena']) : "";
						$filtroActual = "&filtroActual=".$data['filtroActual'];
					?>

					<a id="id_link_excel" href="<?= base_url ?>UsersAdmin/exportarInfo/?tipo_export=<?= "EXCEL".$cadenaExport.$filtroActual; ?>" class="btn" data-toggle="tooltip" data-placement="bottom" title="Exportar a Excel">
						<i class="material-icons ssc md-36">description</i>
						<!--img src="<?= base_url ?>public/media/icons/excelIcon.png" width="40px"--!-->
					</a>
					<a id="id_link_pdf" href="<?= base_url ?>UsersAdmin/exportarInfo/?tipo_export=<?= "PDF".$cadenaExport.$filtroActual; ?>" target="_blank" class="btn" data-toggle="tooltip" data-placement="bottom" title="Exportar a PDF">
						<i class="material-icons ssc md-36">picture_as_pdf</i>
						<!--img src="<?= base_url ?>public/media/icons/pdfIcon.png" width="40px"--!-->
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="row d-flex justify-content-center mt-3">
		<div class="col-auto">
			<span>Filtro: </span>
			<div class="chip">
				<span class="v-a-middle" >
					<?php
						if (isset($_SESSION['userdata']->rango_inicio_user)) {
							$r_inicio = $_SESSION['userdata']->rango_inicio_user;
							$r_fin = $_SESSION['userdata']->rango_fin_user;
							echo (isset($data['filtroNombre']))?$data['filtroNombre']." | Rangos de (".$r_inicio.") a (".$r_fin.")":"Vista general";
							 
						}
						else{
							echo (isset($data['filtroNombre']))?$data['filtroNombre']:"Vista general";
						}
						
					?>
				</span>
			</div>
			
		</div>
		<?php
			if (isset($_SESSION['userdata']->rango_inicio_user)) {
				?>
					<a class="btn btn-opacity" href="<?= base_url;?>UsersAdmin/removeRangosFechasSesion/?filtroActual=<?= $data['filtroActual'];?>">mostrar todo</a>
				<?php
			}
		?>
	</div>	
</div>

<!--Tabla con la información-->
	<div class="row d-flex justify-content-center mx-5">
		<div class="col-auto table-responsive">
			<table class="table table-striped">
				<thead class="thead-myTable text-center">
					<tr id="id_thead" >
						<?php
							//se imprimen los encabezados conforme al catálogo seleccionado 
							echo (isset($data['infoTable']['header']))?$data['infoTable']['header']:"";
						?>
					</tr>
				</thead>
				<tbody id="id_tbody" class="text-justify">
					<?php
						//se imprime todos los registros tabulados de la consulta
						echo (isset($data['infoTable']['body']))?$data['infoTable']['body']:"";
					?>
				</tbody>
			</table>
		</div>
			
	</div>
	
<!--Despliegue de Links de Pagination-->
<div class="container mt-3 mb-5">
	<div class="row d-flex justify-content-center">
		<div class="col-auto">
			<nav aria-label="Page navigation example ">
				<ul id="id_pagination" class="pagination">
					<?php
						echo (isset($data['links']))?$data['links']:"";
					?>
				</ul>
			</nav>
		</div>
	</div>
</div>

<!-- Modals content-->
<div class="modal fade" id="filtro_rangos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
		    <div class="modal-header">
		        <h5 class="title-width" id="exampleModalLabel">Filtrar</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <i class="material-icons">close</i>
		        </button>
		    </div>
		    <div class="modal-body">
		        <div class="container">
		        	<form id="form_rangos" class="row filter-content mb-3" method="post" action="<?= base_url;?>UsersAdmin/index/?filtro=<?= $data['filtroActual']?>">
		        		<div class="col-3">
		        			<h6>Rango de folios</h6>
		        		</div>
		        		<div class="col-9">
		        			<div class="row">
		        				<div class="col-12 col-md-6">
		        					<div class="form-group input-group-sm">
	                                    <input type="date" class="form-control" id="id_date_1" name="rango_inicio" aria-describedby="fecha_filtro_1" required>
	                                    <small id="fecha_filtro_1" class="form-text text-muted">Fecha inicio</small>
	                                </div>
		        				</div>
		        				<div class="col-12 col-md-6">
		        					<div class="form-group input-group-sm">
	                                    <input type="date" class="form-control" id="id_date_2" name="rango_fin" aria-describedby="fecha_filtro_2" required>
	                                    <small id="fecha_filtro_2" class="form-text text-muted">Fecha fin</small>
	                                </div>
		        				</div>
		        			</div>       
		        		</div>
		        	</form>
		        </div>
		    </div>
		    <div class="modal-footer d-flex justify-content-center">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">cancelar</button>
		        <button type="button" class="btn btn-primary" onclick="return aplicarRangos()">Aplicar</button>
		    </div>
	    </div>
	</div>
</div>

<!--Input de filtro para Fetch busqueda por cadena-->
<input id="filtroActual" type="hidden" value="<?= $data['filtroActual']?>">

