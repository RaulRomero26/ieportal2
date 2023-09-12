<!-- <div class="container">

    <?php if(!isset($data['titulo_1'])){ ?>
        <div class="paragraph-title d-flex justify-content-between mt-5 mb-4">
            <h5> <a href="<?= base_url; ?>Remisiones">Remisiones </a> <span>/ Nueva</span></h5>
        </div>
    <?php }else{ ?>
        <div class="paragraph-title d-flex justify-content-between mt-5 mb-4">
            <h5> <a href="<?= base_url; ?>Remisiones">Remisiones </a> <span>/ <?=$data['titulo_1']?></span></h5>
        </div>
    <?php } ?>

</div>

 -->




<div class="container-fluid" >
    <ul class="nav nav-tabs d-flex justify-content-center" id="tab_clases" role="tablist">

            <li class="nav-item" role="presentation">
                <a class="nav-link active d-flex align-items-center" id="datos_clase" data-toggle="tab" href="#datos_clase0" role="tab" aria-controls="Datos_principales" aria-selected="true">
                    Datos clase
                </a>
            </li>
      
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center" id="pase_lista" data-toggle="tab" href="#pase_lista0" role="tab" aria-controls="Inf_ad" aria-selected="false">
                    Pase de lista
                </a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center" id="delegar_cancelar" data-toggle="tab" href="#delegar_cancelar0" role="tab" aria-controls="Inf_ad" aria-selected="false">
                    Delegar/Cancelar
                </a>
            </li>

    </ul>


    <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade show active" id="datos_clase0" role="tabpanel" aria-labelledby="datos_p">
            <?php include 'tabs/editarClaseView.php'; ?>
        </div>

        <div class="tab-pane fade" id="pase_lista0" role="tabpanel" aria-labelledby="datos_s">
            <?php include 'tabs/paseListaView.php'; ?>
        </div>

        <div class="tab-pane fade" id="delegar_cancelar0" role="tabpanel" aria-labelledby="datos_s">
            <?php include 'tabs/delegarCancelarView.php'; ?>
        </div>

    </div>
</div>