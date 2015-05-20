<?PHP
/*
 * Script que recive peticiones post
 * para realizar la descarga de plantillas de websites
 * @author SebsLakes twitter.com/sebslakes
 *
 */
  
 // Incluyo clase webtozip 
 include('webtozip.php');
 
 // Obtengo información traida por post
 $url=file_get_contents('php://input');

 // Instancio clase webtozip
 $web = new Webtozip();
 
 // Valido que realmente sea una url
 $validacion = $web->validate($url);
 
 // Si es una url entonces se descarga el sitio
 if($validacion==1){
		$descarga = $web->download($url);
		echo "Descarga el sitio desde el siguiente link: <a href='".$descarga['file']."'>".$descarga['url']."</a><br>";
 		echo "El link de descarga es valido por solo por 10 minutos.";
 }else{
 		echo $validacion; // Mensaje: url no valida
 }
 
?>