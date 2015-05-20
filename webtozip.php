<?PHP
/*
* Clase encargada de recibir peticiones para 
* realizar descarga de url solicitada
* La url es empaquetada en un .tar.gz
* @author sebslakes - twitter.com/sebslakes
*
* Ej. de uso:
* $webtozip = new webtozip();
* $validacion = $web->validate($url);
* $descarga = $web->download($url);
*
*/
include 'conexion.php';

class Webtozip {

	// Metodo encargado de generar la descarga de la url solicitada
	public function download($url){
		$this->write_log("Url a descargar: ".$url,"INFO");

		$web = shell_exec("echo $url | awk -F'/' '{print $3}'");
		shell_exec("wget -r $url");
		shell_exec("tar -zcvf ".trim($web).".tar.gz ".$web);
		shell_exec("rm -r ".trim($web));
		$fecha = date("YmdHis");
		$id = $fecha.$url;
		$this->url_downloaded($web);

		$response=array(
			'file' => trim($web).'.tar.gz',
			'url' => trim($web)
		);

		return $response;
	}
	
	// Funcion de encargada de eliminar los archivos del sistema que tengan más de 10 minutos en el servidor
	public function recycler(){
		$query="SELECT * FROM webtozip WHERE status=1 AND time_stamp <= DATE_ADD( NOW( ) , INTERVAL -10 MINUTE )";
		$result=mysql_query($query);

		while($row = mysql_fetch_array($result)){
			$archivo=trim($row['url']).".tar.gz";
			$this->write_log("Eliminacion de $archivo","INFO"); 
			shell_exec("rm -r $archivo");
		}

		$update="update webtozip set status=0 where status=1 and time_stamp <= DATE_ADD( NOW( ) , INTERVAL -10 MINUTE )";
		if(!mysql_query($update)){
			echo "error";
		}

		$this->write_log("Ejecucion OK","INFO"); 
	}
	
	// Metodo valida que url sea valida devuelve 1 si es true y string en caso contrario
	public function validate($url){
		if(!filter_var($url, FILTER_VALIDATE_URL)){
	        if($url=='Chile'){
	        	// Huevo de pascua :P
	          return "Puro Chile es tu cielo azulado puras brisas te cruzan tambien <3";
	        }else{
	          return "Ups makinola, al parecer no pusiste una url valida";
	        }
		}
		return true;
	}
	
	// Inserta registro en base de datos dejando status=1, esto indica que el archivo
	// ya fue descargado al servidor y luego de 10 minutos será eliminado.
	protected function url_downloaded($url){
		$web=trim($url);
		$query="insert into webtozip (url,status) values ('$url',1)";
		mysql_query($query);
	}

	// Metodo encargado de escribir en log
	protected function write_log($cadena,$tipo){
		$arch = fopen(realpath( '.' )."/log/webtozip_".date("Y-m-d").".log", "a+"); 
		fwrite($arch, "[".date("Y-m-d H:i:s.u")." ".$_SERVER['REMOTE_ADDR']." ".$_SERVER['HTTP_X_FORWARDED_FOR']." - $tipo ] ".$cadena."\n");
		fclose($arch);
	}
	
}
?>