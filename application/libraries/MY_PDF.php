<?php
/**
 //$array = json_decode(json_encode($booking), true);
 */
class MY_PDF
{
	
	public function getPlantilla($data=null){
		$plantilla = '';

		if ($data == null) {
			return "";
		}

		$subtitulo 	= $data['subtitulo'];
		$mensaje 	= $data['msg'];
		$theads 	= '';
		$field_names= $data['field_names'];
		$body		= ''; 

		//obteniendo y generando los heads de la tabla
		foreach ($data['columns'] as $column) {
			$theads.= '<th>'.$column.'</th>';
		}

		//obteniendo y enerando cada registro con sus respectivos valores
		foreach ($data['rows'] as $row) {
			$body.= '<tr>';
			foreach ($field_names as $f_name) {
				$body.= '<td>'.mb_strtoupper($row->$f_name).'</td>';
			}
			$body.= '</tr>';
		}

		$FontStyleTable = (count($data['columns']) >= 8)?'style="font-size: 11px;"':'';
		$plantilla.= '
			<body>
			    <div>
			    	<div class="row mb-5 no_border">
						<table class="table">
						  <tbody style="">
						    <tr style="">
						      <td style="">
						      	<img src="'.base_url.'media/images/logo_secretaria.png" height="80px" >
						      </td>
						      <td style="vertical-align: middle; text-align: center;">
						      	<h3>Planeación</h3>
						      	<hr>
						      	<span>Exportación de '.$subtitulo.'</span>
						      </td>
						    </tr>
						  </tbody>
						</table>
					</div>
					<div class="row mb-4" style="text-align: center;">
						<h5 style="color: #616161;">La siguiente tabla muestra '.$mensaje.'</h5>
					</div>
					<div class="row">
						<div class="col-auto mi_table">
							<table class="table table-sm" '.$FontStyleTable.'>
								<thead class="text-center">
									<tr id="id_thead" >
										'.$theads.'
									</tr>
								</thead>
								<tbody id="id_tbody" class="text-justify">
									'.$body.'
								</tbody>
							</table>
						</div>
							
					</div>
				</div>
			</body>
		';

		return $plantilla;
	}

	public function getPlantilla_IPH1($data=null){
		$plantilla = '';

		if ($data == null) {
			return "";
		}

		$plantilla.= '
			<body>
			    <div>
			    	<div class="row mb-1 no_border">
						<table class="table">
						  <tbody style="">
						    <tr style="">
						      <td style="">
						      	<img src="'.base_url.'public/media/images/banner.png" height="60px" >
						      </td>
						      <td style="vertical-align: middle; text-align: right; font-size: 12px;">
						      	<p>COORDINACIÓN GENERAL DE OPERATIVIDAD POLICIAL</p>
						      </td>
						    </tr>
						  </tbody>
						</table>
					</div>
					<div class="row mb-1 " style="text-align: center;">
						<h5 style="color: #616161; font-size: 14px;">REMISIÓN</h5>
					</div>
					<div class=" my-3">
						<div style="max-width: 50%; margin-right: 30px;"><span>jeje</span></div>
						<div style="max-width: 50%; margin-right: 30px;"><span>jeje2</span></div>
					</div>
					<div class="row mb-2 no_border " >
						<table class=" table mi_border1">
						  <tbody  style="">
						    <tr style="">
						      <td class="mi_border1" style="vertical-align: middle;">
						      	<span  style="font-size: 12px;">C: </span>
						      	<span class="underline">Ministerio público / juez calificador</span>
						      </td>
						      <td style="vertical-align: middle; text-align:center;">
						      	<p >
						      		<span>FOLIO: </span>
						      		<span class="mi_border1">1024515</span>
						      	</p>
						      	<p class="mt-3">
						      		<span>FECHA:  </span>
						      		<span>01-05-2020</span>
						      	</p>
						      	<!--table class="table ">
								  <tbody style="border: 1px solid #f00; border-radius: 30px;">
								    <tr style="border: 1px solid #0f0; border-radius: 30px;">
								      <td style="border: 1px solid #00f; border-radius: 30px;">
								      	<div class="mi_border1">FOLIO: 1024515</div>
								      </tr>
								    </tr>
								    <tr style="margin-top: -20px;">
								      <td style="">
								      	FECHA: 01-05-2020
								      </tr>
								    </tr>
								  </tbody>
								</table-->
						      	
						      </td>
						    </tr>
						  </tbody>
						</table>
					</div>
					
				</div>
			</body>
		';

		return $plantilla;
	}	
}

?>
				
