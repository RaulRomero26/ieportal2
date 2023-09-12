<?php


class PDF extends FPDF
{
    protected $col = 0; // Current column
    protected $y0;      // Ordinate of column start

    function MultiCellRow($cells, $width, $heightizq, $heightder, $data, $pdf,$espacios_blancos,$numllamado)
    {
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $maxheight = 0;
        for ($i = 0; $i < $cells; $i++) {

            if($i == 1){
                $this->SetFillColor(242,242,242); //242
                $x = $pdf->GetX()+$width;
                $width = $width+25;
            } else {
                $this->SetFillColor(200,200,200); //200
                $width = $width -10;
                if($espacios_blancos > 0)
                    $data[$i]= $data[$i].str_repeat(" ", $espacios_blancos);
            }
            $valortamaño=12;
            $this->SetFont('helvetica','',$valortamaño);
            // numero de llamado indica el renglon de la "tabla" 1 alias, 2 curp ,3 udc, 4 utc, 5 facebook, siendo el 2 y el 4 inamobibles de un renglon
            $longituddatoderecha=0;
            $longituddatoderecha = strlen($data[1]);
            switch ($numllamado) {
                case '1': //ALIAS
                    if($i == 0){
                        if( ($longituddatoderecha>=18) && ($longituddatoderecha<=30) ){
                            $heightizq = 14;
                        }
                        $pdf->MultiCell($width, $heightizq, $data[$i],0,1,'L');
                    }else{
                        $pdf->MultiCell($width, $heightder, $data[$i],0,1,'L');
                    }   
                    break;

                case '2': //CURP
                    if($i == 0){
                        $pdf->MultiCell($width, $heightizq, $data[$i],0,1,'L');
                    }else{
                        $pdf->MultiCell($width, $heightder, $data[$i],0,1,'L');
                    }
                    break;

                case '3': //DOMICILIO
                    if($i == 0){
                        if( ($longituddatoderecha>=16) && ($longituddatoderecha<=35) ){
                            $heightizq = 14;
                        }else {
                            if( ($longituddatoderecha>=36) && ($longituddatoderecha<=49) ){
                                $heightizq = 21;
                            }else {
                                if( ($longituddatoderecha>=50) ){
                                    $heightizq = 28;
                                }
                            }
                        }
                        $pdf->MultiCell($width, $heightizq, $data[$i],0,1,'L');
                    }else{
                        $pdf->MultiCell($width, $heightder, $data[$i],0,1,'L');
                    }   
                    break;

                case '4': //TELEFONO
                    if($i == 0){
                        $pdf->MultiCell($width, $heightizq, $data[$i],0,1,'L');
                    }else{
                        $pdf->MultiCell($width, $heightder, $data[$i],0,1,'L');
                    }
                    break;

                case '5': //FACEBOOK
                    if($i == 0){
                        if( ($longituddatoderecha>=24) && ($longituddatoderecha<=35) ){
                            $heightizq = 14;
                        }else {
                            if( ($longituddatoderecha>=36) && ($longituddatoderecha<=60) ){
                                $heightizq = 21;
                            }
                        }
                        $pdf->MultiCell($width, $heightizq, $data[$i],0,1,'L');
                    }else{
                        $pdf->MultiCell($width, $heightder, $data[$i],0,1,'L');
                    }   
                    break;
                
                default:
                    # code...
                    break;
            }

            if ($pdf->GetY() - $y > $maxheight) $maxheight = $pdf->GetY() - $y;
            $pdf->SetXY($x + ($width * ($i + 1)), $y);
            $this->SetFont('helvetica','',12);

        }
        return $maxheight;
    }

    function Header()
    {
        // Page header
        global $title;
        /* ----- ----- ----- Variables ----- ----- ----- */
        $banner             = 'http://localhost/atlas/public/media/images/logo22.png';
        $this->Image($banner,80,0,50);
        $this->SetX(50);
    /*    $this->SetFont('Arial','B',15);
        $w = $this->GetStringWidth($title)+6;
        $this->SetX((210-$w)/2);
        $this->SetDrawColor(0,80,180);
        $this->SetFillColor(230,230,0);
        $this->SetTextColor(220,50,50);
        $this->SetLineWidth(1);
        $this->Cell($w,9,$title,1,1,'C',true);*/
        $this->Ln(10);
        // Save ordinate
        $this->y0 = $this->GetY()+10;
    }

    function Footer()
    {
        // Page footer
        $banner= 'http://localhost/atlas/public/media/images/footeratlas2.png';
        $this->Image($banner,10,277,180);
     /*   $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(128);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');*/
    }

    function SetCol($col)
    {
        // Set position at a given column
        $this->col = $col;
        $x = 10+$col*97.5;
        $this->SetLeftMargin($x);
      //  $this->SetY(20);
        $this->SetX($x);
      //  $this->SetMargins(10,20);
       
    }

    function AcceptPageBreak()
    {
     //   $this->SetY($this->GetY()+10);
        if($this->col<1)
        {
            // Go to next column
            $this->SetCol($this->col+1);
            // Set ordinate to top
            $this->SetY($this->y0-10);
            // Keep on page
            return false;
        }
        else
        {
            // Go back to first column
            $this->SetCol(0);
        //    $this->SetY(20);
      //      $this->y0 = $this->y0+10;
            // Page break
            return true;
        }
    }

    function ChapterTitle($data)
    {
        // Title
        $this->SetFont('Arial','',12);
        
        $this->SetFont('helvetica','',30);
        $this->SetTextColor(31, 56, 100);
        $this->SetY(45);
        $this->SetX(16);
        $this->Cell(5,4,utf8_decode("\"".$data[0]['grupo']->NOMBRE_BANDA."\""));
        $this->SetTextColor(156, 156, 156);
        $this->SetFont('helvetica','',11);
        $this->SetY(55);
        $this->SetX(77);
        $this->Cell(5,4,utf8_decode(strtoupper($data[0]['grupo']->PRINCIPALES_DELITOS)));
        $this->Ln(4);
        $this->y0 = $this->GetY();
    }

    function ChapterBody($data)
    {
        // Read text file
    //  $txt = file_get_contents($file);
        // Font
     //   $this->SetFont('Times','',12);
        // Output text in a 6 cm width column
        $this->SetFont('helvetica','B',12);
        $this->SetTextColor(31, 56, 100);
        $this->SetDrawColor(31,56,100);
        $this->SetY(63);
        $this->SetX(20);
        $this->Multicell(75,4,strtoupper("Antecedentes"),'C');
        $this->Line(20, $this->GetY()+2, 95, $this->GetY()+2);
        $this->SetFont('helvetica','',10);
        $this->SetY(75);
        $this->SetX(20);
        $this->Multicell(75,4,utf8_decode(strtoupper($data[0]['grupo']->ANTECEDENTES)));
       // $this->MultiCell(95,5,$data[0]['integrantes'][1]->DESCRIPCION);
        $this->Ln();
        // Mention
        $this->SetFont('','I');
       // $this->Cell(0,5,'(end of excerpt)');
        // Go back to first column
        $this->SetCol(1);
        $imagen = explode("?", $data[0]['grupo']->FOTOGRAFIA);
        $pathImagesFH = "http://localhost/atlas/public/files/GestorCasos/".$data[0]['grupo']->ID_BANDA."/Grupo/".$imagen[0];
        if(isset($pathImagesFH) && getimagesize($pathImagesFH)){
            $type = exif_imagetype($pathImagesFH);
            $extension = '';
            switch($type){
                case 1:
                    $extension = 'gif';
                break;
                case 2:
                    $extension = 'jpeg';
                break;
                case 3:
                    $extension = 'png';
                break;
            }
            $this->SetLineWidth(0.5);
            $this->Rect(101,68,91,72,"D");
            $this->Image($pathImagesFH,102,69,89,70,$extension);
        }
        else{
            $pathImagesFH = "http://localhost/atlas/public/files/GestorCasos/placeholdergrupo.jpg";
            $type = exif_imagetype($pathImagesFH);
            $extension = '';
            switch($type){
                case 1:
                    $extension = 'gif';
                break;
                case 2:
                    $extension = 'jpeg';
                break;
                case 3:
                    $extension = 'png';
                break;
            }
            $this->Image($pathImagesFH,102,69,90,68,$extension);
        }
        $this->SetY(148);
        $this->SetX(100);
        $this->SetFont('helvetica','B',10);
        $this->SetTextColor(31, 56, 100);
        $this->SetDrawColor(31,56,100);
        $this->Multicell(75,4,strtoupper("Peligrosidad"),'C');
        $this->Line(100, $this->GetY()+2, 190, $this->GetY()+2);
        $this->SetFont('helvetica','',10);
        $this->SetY(155);
        $this->SetX(102);
        $this->Multicell(75,4,utf8_decode(strtoupper($data[0]['grupo']->PELIGROSIDAD)));

        $this->SetFont('helvetica','B',10);
        $this->SetTextColor(31, 56, 100);
        $this->SetDrawColor(31,56,100);
        $this->SetY($this->GetY()+4);
        $this->SetX(100);
        $this->Multicell(75,4,utf8_decode(strtoupper("Zonas de Operación")),'C');
        $this->Line(100, $this->GetY()+2, 190, $this->GetY()+2);
        $this->SetFont('helvetica','',10);
        $this->SetY($this->GetY()+4);
        $this->SetX(102);
        
        $zonas=explode(",",$data[0]['grupo']->ZONAS);
        $colonias=explode("$",$data[0]['grupo']->COLONIAS);
        $zonas_final="";
        for($i=0;$i<count($zonas);$i++)
            if($zonas[$i]!="")
                $zonas_final=$zonas_final.$zonas[$i].": ".$colonias[$i];
        $this->Multicell(90,4,utf8_decode(strtoupper($zonas_final)));

        $this->SetFont('helvetica','B',10);
        $this->SetTextColor(31, 56, 100);
        $this->SetDrawColor(31,56,100);
        $this->SetY($this->GetY()+4);
        $this->SetX(100);
        $this->Multicell(75,4,utf8_decode(strtoupper("Actividades ilegales")),'C');
        $this->Line(100, $this->GetY()+2, 190, $this->GetY()+2);
        $this->SetFont('helvetica','',10);
        $this->SetY($this->GetY()+8);
        $this->SetX(102);
        $this->Multicell(90,4,utf8_decode(strtoupper($data[0]['grupo']->ACTIVIDADES_ILEGALES)));

        $this->SetFont('helvetica','B',10);
        $this->SetTextColor(31, 56, 100);
        $this->SetDrawColor(31,56,100);
        $this->SetY($this->GetY()+4);
        $this->SetX(100);
        $this->Multicell(75,4,utf8_decode(strtoupper("Líderes")),'C');
        $this->Line(100, $this->GetY()+2, 190, $this->GetY()+2);
        $this->SetFont('helvetica','',10);
        $lideres=[];
        $this->SetY($this->GetY()+4);
        $bandera=0;
        foreach($data[0]['integrantes'] as $integrante){
            if($integrante->TIPO=="LIDER"){
                $cadena="";
                $cadena=$cadena.$integrante->NOMBRE." ".$integrante->APELLIDO_PATERNO." ".$integrante->APELLIDO_MATERNO." ";
                if($integrante->ALIAS!="")  
                    $cadena=$cadena."(a) \"".$integrante->ALIAS."\"";
                array_push($lideres,$cadena);
            } 
        }
        for($i=0;$i<count($lideres);$i++){
            $this->SetX(106);
            $this->Multicell(80,4,utf8_decode(strtoupper("-".$lideres[$i])));
        }
        $this->AddPage();
        $this->SetCol(0);
        $repla=['"','\'','\"','“','”'];
        for($contador_integrantes=0;$contador_integrantes<count($data[0]['integrantes']);$contador_integrantes++){
            
            $this->SetTextColor(31, 56, 100);
            $this->SetY($this->GetY()+5);
            $this->SetFont('helvetica','B',10);
            $this->Cell(5,4,utf8_decode(mb_strtoupper($data[0]['integrantes'][$contador_integrantes]->NOMBRE." ".$data[0]['integrantes'][$contador_integrantes]->APELLIDO_PATERNO." ".$data[0]['integrantes'][$contador_integrantes]->APELLIDO_MATERNO)));
            $this->SetFont('helvetica','',12);    
            $this->Line($this->GetX()-5, $this->GetY()+5, $this->GetX()+80, $this->GetY()+5);
                //fotografia
                $imagen = explode("?", $data[0]['integrantes'][$contador_integrantes]->PATH_IMAGEN);
                $pathImagesFH = "http://localhost/atlas/public/files/GestorCasos/".$data[0]['grupo']->ID_BANDA."/Grupo/".$imagen[0];
                if(isset($pathImagesFH) && getimagesize($pathImagesFH)){
                    $type = exif_imagetype($pathImagesFH);
                    $extension = '';
                    switch($type){
                        case 1:
                            $extension = 'gif';
                        break;
                        case 2:
                            $extension = 'jpeg';
                        break;
                        case 3:
                            $extension = 'png';
                        break;
                    }
                    $this->SetLineWidth(0.5);
                    $this->Rect($this->GetX()-5,$this->GetY()+7,40,44,"D");
                    $this->Image($pathImagesFH,$this->GetX()-4,$this->GetY()+8,38,42,$extension);
                }
                else{
                    $pathImagesFH = "http://localhost/atlas/public/files/GestorCasos/placeholderprofile.jpg";
                    $type = exif_imagetype($pathImagesFH);
                    $extension = '';
                    switch($type){
                        case 1:
                            $extension = 'gif';
                        break;
                        case 2:
                            $extension = 'jpeg';
                        break;
                        case 3:
                            $extension = 'png';
                        break;
                    }
                    $this->SetLineWidth(0.5);
                    $this->Rect($this->GetX()-5,$this->GetY()+7,40,44,"D");
                    $this->Image($pathImagesFH,$this->GetX()-4,$this->GetY()+8,38,42,$extension);
                }
                $this->SetTextColor(255, 0, 0);
                $this->SetY($this->GetY()+10);
                $this->SetX($this->GetX()+45);
                $this->Cell(37,10,utf8_decode($data[0]['integrantes'][$contador_integrantes]->ESTATUS),1,0,'C');
                $this->SetFillColor(200,200,200);
                $this->SetTextColor(31, 56, 100);
                $this->Ln();
                $this->SetY($this->GetY()+33);
                $espacios_blancos = 1;
                $maxheight = $this->MultiCellRow(2, 40, 7, 7, ["ALIAS:",strtoupper($data[0]['integrantes'][$contador_integrantes]->ALIAS)],$this,$espacios_blancos,1);
                $espacios_blancos = 1;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 7, 7, ["CURP: ",strtoupper($data[0]['integrantes'][$contador_integrantes]->CURP)],$this,$espacios_blancos,2);
                $espacios_blancos = 1;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 7, 7, ["UDC: ",strtoupper($data[0]['integrantes'][$contador_integrantes]->UDC)],$this,$espacios_blancos,3);
                $espacios_blancos = 1;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 7, 7, ["UTC: ",strtoupper($data[0]['integrantes'][$contador_integrantes]->UTC)],$this,$espacios_blancos,4);
                $espacios_blancos = 1;
                $this->SetY($this->GetY()+$maxheight);
                $maxheight =$this->MultiCellRow(2, 40, 7, 7, ["FACEBOOK: ",$data[0]['integrantes'][$contador_integrantes]->PERFIL_FACEBOOK],$this,$espacios_blancos,5);
                /*$this->Multicell(80,8,utf8_decode("Alias:    ".$data[0]['integrantes'][$contador_integrantes]->ALIAS),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("CURP:   ".$data[0]['integrantes'][$contador_integrantes]->CURP),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("UDC:      ".$data[0]['integrantes'][$contador_integrantes]->UDC),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("UTC:      ".$data[0]['integrantes'][$contador_integrantes]->UTC),0,1,'L',1);
                $this->Multicell(80,8,utf8_decode("Facebook: " .$data[0]['integrantes'][$contador_integrantes]->PERFIL_FACEBOOK),0,1,'R',1);*/
                $this->SetY($this->GetY()+$maxheight+3);
                $this->Multicell(80,4,utf8_decode(strtoupper(str_replace($repla, '"',$data[0]['integrantes'][$contador_integrantes]->DESCRIPCION))),0,"J",false);
                
                if(trim($data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA)!=""){
                    $antecedentes=explode("$",$data[0]['integrantes'][$contador_integrantes]->ANTECEDENTES_PERSONA);
                    for($i=0;$i<count($antecedentes);$i++){
                        if($i==0 && $antecedentes[$i]!=""){
                            $this->SetY($this->GetY()+5);
                            $this->SetFont('helvetica','B',10);
                            $this->Cell(4,4,utf8_decode(strtoupper("Cuenta con antecedentes policiales por: ")));
                            $this->SetFont('helvetica','',10);
                            $this->SetY($this->GetY()+5);
                        }
                        $this->SetY($this->GetY()+3);
                        $this->Multicell(80,4,utf8_decode(($i+1).".-".strtoupper($antecedentes[$i])));
                    } 
                }
                if($this->GetY()>150){
                    if($this->col==0)
                        $this->SetCol(1);
                    else{
                        if($contador_integrantes!=count($data[0]['integrantes'])-1 )   
                            $this->AddPage();
                        $this->SetCol(0);
                    }
                    $this->SetY(20);
                }
                
        }
    }
    function PrintChapter($data)
    {
        // Add chapter
        for($cantidad_grupo=0;$cantidad_grupo<count($data);$cantidad_grupo++){
            $this->AddPage();
            $this->ChapterTitle($data[$cantidad_grupo]);
            $this->ChapterBody($data[$cantidad_grupo]);
        }
        
    }
}

$pdf = new PDF();
$pdf->AddFont('Avenir','','avenir.php');
$pdf->AliasNbPages();
$pdf->PrintChapter($data);
//$pdf->PrintChapter(2,'THE PROS AND CONS','20k_c2.txt');
$pdf->Output();
?>