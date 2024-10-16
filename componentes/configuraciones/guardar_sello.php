<?php
    session_start();
    require("../conexion.php");
    date_default_timezone_set('America/Mexico_City');

    if(empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario']))
    {
        session_destroy();
        echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
    }
    else
    {
        $pass = $_POST['pass'];
		$vigencia = $_POST['fecha'];

        $nombreCer = $_FILES['cer']['name'];
	    $rutaCer = $_FILES['cer']['tmp_name'];
	    $tipoCer = $_FILES['cer']['type'];

        $nombreKey = $_FILES['key']['name'];
	    $rutaKey = $_FILES['key']['tmp_name'];
	    $tipoKey = $_FILES['key']['type'];

        $info_cer = new SplFileInfo($nombreCer);
	    $extension_cer = pathinfo($info_cer->getFilename(), PATHINFO_EXTENSION);
	    //echo $extension_cer;

	    $info_key = new SplFileInfo($nombreKey);
	    $extension_key = pathinfo($info_key->getFilename(), PATHINFO_EXTENSION);
	    //echo $extension_key;

        $cer_subido = move_uploaded_file($rutaCer, "../../emisores/".$_SESSION['id_emisor']."/archivos/generales/certificado.cer");
        if($cer_subido)
        {
            $key_subido = move_uploaded_file($rutaKey, "../../emisores/".$_SESSION['id_emisor']."/archivos/generales/llave.key");
            if($key_subido)
            {
				/*
                system('openssl x509 -in ../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/certificado.cer -inform DER -out ../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/certificado.cer.pem -outform PEM');
	            system('openssl pkcs8 -in ../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/llave.key -inform DER -passin pass:'.$password.' -out ../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/llave.key.pem -outform PEM');
	            system('openssl enc -in ../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/certificado.cer -a -A -out ../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/certificado.txt');                

                $serieCertificado = system('openssl x509 -in ../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/certificado.cer.pem -serial -noout');

                $urlarchivo = '../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/serieCertificado.txt';
	            $archivo = fopen($urlarchivo, "w");
	            fwrite($archivo, $serieCertificado);
	            fclose($archivo);

                $NoCertificado = file_get_contents('../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/serieCertificado.txt');
                $NoCertificado2 = str_replace("serial=","",$NoCertificado);

                $urlarchivo = '../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/serieCertificado.txt';
				$archivo = fopen($urlarchivo, "w");
				fwrite($archivo, $NoCertificado2);
				fclose($archivo);
	
				$serieCertificado = file_get_contents('../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/serieCertificado.txt');
				//$serieCertificado = "3330303031303030303030343030303032343334";
				$cer1 = $serieCertificado[1];
				$cer3 = $serieCertificado[3];
				$cer5 = $serieCertificado[5];
				$cer7 = $serieCertificado[7];
				$cer9 = $serieCertificado[9];
				$cer11 = $serieCertificado[11];
				$cer13 = $serieCertificado[13];
				$cer15 = $serieCertificado[15];
				$cer17 = $serieCertificado[17];
				$cer19 = $serieCertificado[19];
				$cer21 = $serieCertificado[21];
				$cer23 = $serieCertificado[23];
				$cer25 = $serieCertificado[25];
				$cer27 = $serieCertificado[27];
				$cer29 = $serieCertificado[29];
				$cer31 = $serieCertificado[31];
				$cer33 = $serieCertificado[33];
				$cer35 = $serieCertificado[35];
				$cer37 = $serieCertificado[37];
				$cer39 = $serieCertificado[39];
	
				$NoCertificado3 = $cer1.$cer3.$cer5.$cer7.$cer9.$cer11.$cer13.$cer15.$cer17.$cer19.$cer21.$cer23.$cer25.$cer27.$cer29.$cer31.$cer33.$cer35.$cer37.$cer39;
				//$serieCER = file_get_contents($destinoCer."certificado.txt");
				//$serieCER = base64_decode($serieCER);

				$urlarchivo = '../../emisores/'.$_SESSION['id_emisor'].'/archivos/generales/serieCertificado.txt';
				$archivo = fopen($urlarchivo, "w");
				fwrite($archivo, $NoCertificado3);
				fclose($archivo);
				*/

				$sqlCSD = "UPDATE emisores_configuraciones SET sello_cer = 1, sello_key = 1, password = '".$pass."', sello_vigencia = '".$vigencia."' WHERE id_emisor = ".$_SESSION['id_emisor'];
				$guardar = mysqli_query($conexion, $sqlCSD);
	
				if($guardar){
					echo '4';
				}else{
					echo '3';
				}
            }
            else
            {
                echo "2";
            }
        }
        else
        {
            echo "1";
        }
    }
?>