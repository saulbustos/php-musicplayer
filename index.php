<?PHP

ERROR_REPORTING(0);
session_start();

?>

<html>
<head>
<script type="text/javascript">
function seleccion(name)
{
	window.open("http://starblank.com/mp3player/index.php?user="+name, '_self');
}
function salir()
{
	window.open("http://starblank.com", '_top');
}
</script>

<?PHP

if ($_GET['user']!="") {$_POST['user']=$_GET['user'];}

if (isset($_POST['user'])) {$_SESSION['user']=$_POST['user'];}

if ($_SESSION['user']=="")
{
	echo "Bienvenido! Tu nombre es:<br>";
	if ($handle = opendir('.')) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." && $file != ".." && $file != "playlist.xml" && $file != "lista.xml") {
	            if (strtolower(substr($file,-4))==".xml")
			{echo '<INPUT TYPE="button" VALUE="'.substr($file,0,strlen($file)-4).'" onclick="javascript:seleccion(\''.substr($file,0,strlen($file)-4).'\');"><br>';
		    }
		    
	        }
	    }
	    closedir($handle);
	}
	echo "<br>¿Eres nuevo? Escribe tu nombre:"; 
    	echo "<form action='".$_SERVER['PHP_SELF']."' method='POST' name='formulario'>";
    	echo "<input type='text' name='user' /><br>";
	echo "<input type='submit' value='Entrar' name='usuario'</form>";
	echo '<INPUT TYPE="button" VALUE="Salir" onClick="javascript:salir();"><br>';
	exit;	
}

$milista=$_SESSION['user'].".xml";
$usuario=$_SESSION['user'];
if ($milista=="") {$milista="lista.xml";}
?>


<script type="text/javascript">

function volverinicio()
{
	window.open("http://starblank.com/mp3player/index.php", '_top');
}

function cargaframelista()
{
<?PHP
echo "document.getElementById('mp3').innerHTML='Lista de Reproduccion Personalizada<br><object data=\"mp3player.swf?playlist=".$milista."&autoplay=true\" type=\"application/x-shockwave-flash\" width=\"300\" height=\"200\" ><param name=\"movie\" value=\"mp3player.swf?playlist=".$milista."&autoplay=true\" /></object>';";
?>
}

function cargaframe()
{

document.getElementById('mp3').innerHTML='<object data="mp3player.swf?playlist=playlist.xml&autoplay=true" type="application/x-shockwave-flash" width="300" height="200" ><param name="movie" value="mp3player.swf?playlist=playlist.xml&autoplay=true" /></object>';
}

function nocarguesframe()
{
document.getElementById('mp3').innerHTML='<strong>Este programa no funciona en algunas versiones de internet explorer.<br> Prueba por si acaso, o si no con Firefox o Chrome.<br><br></strong><object data="mp3player.swf?playmedia=playlist.xml&autoplay=true" type="application/x-shockwave-flash" width="300" height="200" ><param name="movie" value="mp3player.swf?playmedia=playlist.xml&autoplay=true" /></object>';
}

</script>
</head>

<?PHP
$navegador = getenv("HTTP_USER_AGENT"); 
if (preg_match("/MSIE/i", "$navegador")) 
{ 
	echo '<body onLoad="nocarguesframe();">';
}
else
{
	if (isset($_POST['lista']))
	{	
		echo '<body onLoad="cargaframelista();">';
	}
	else
	{
		echo '<body onLoad="cargaframe();">';	
	}
}
?>




<div id="mp3"></div>

<?PHP


$nombrebase="index.php?ruta=";

$servidorbase="http://starblank.com/mp3player/";

if (isset($_POST['borrar']))
	{
		$myfile=$milista;
		$fh = fopen($myfile, 'w') or die('No se puede abrir el archivo '.$myfile);
		$listainicial='<?xml version="1.0" encoding="ISO-8859-1"?><playlist version="1" xmlns="http://xspf.org/ns/0/"><trackList>';
		$listainicial=$listainicial."\r\n</trackList></playlist>";		
		fwrite($fh, $listainicial);
		fclose($fh);

	}

if (isset($_POST['enviar']))
	{
	//Cargamos el xml que hay en /lista.xml
	//Mas adelante implementaremos varias listas de reproduccion, de momento solo una

	$res=file($milista);
	if (count($res)<=1)
	{
		//significa que la lista de favoritos todavia esta vacia. Le ponemos cabecera.
		
		
		$res[0]='<?xml version="1.0" encoding="ISO-8859-1"?><playlist version="1" xmlns="http://xspf.org/ns/0/"><trackList>';

	}
	if (count($res)<=1) {$inicio=count($res);} else {$inicio=count($res)-1;}

		for ($i=0;$i<100;$i++)
		{	
			if ($_POST['check'.$i]!="") 
			{
				$nombre=explode("/",$_POST['check'.$i]);
				$nombre=$nombre[count($nombre)-1];
				$res[$inicio]="<track><location>".$_POST['check'.$i]."</location><title>".$nombre."</title><album></album><creator></creator></track>\r\n";
				//$res[$inicio]=str_replace("./","http://starblank.com/mp3player/",$res[$inicio]);
				$res[$inicio]=str_replace("----","'",$res[$inicio]);
				$inicio++;
		
			}


		}
		
	//escribimos el XML en la ruta /playlist/playlist.xml
	$res[count($res)+1]="</trackList></playlist>";
	$res=implode($res);
	$myfile=$milista;
	$fh = fopen($myfile, 'w') or die('No se puede abrir el archivo '.$myfile);
	fwrite($fh, $res);
	fclose($fh);
	}


if (isset($_POST['lista']))
{
	//Cargamos una lista con las canciones, y les ponemos checkbox y un boton de borrar
	//al pulsar en el boton, se borran las seleccionadas y se vuelve a la playlist.
	//ponemos un boton de salir que vuelve al principio del programa.
	echo "Estamos en la playlist de ".$usuario."<br>";
	echo '<INPUT TYPE="button" VALUE="Volver al Inicio" onClick="volverinicio();"><br>';
	echo '<FORM METHOD="POST" ACTION="index.php"><INPUT TYPE="submit" value="Borrar Playlist" NAME="borrar">';
	
}
else
{

	$dir=$_GET['ruta'];
	$anterior=$dir;
	
	if ($dir!="")
		{
			echo "Estamos en ".$dir."<br><br>";
		}
		else{
			echo "Bienvenido, ".$usuario."! Elige la musica que quieres escuchar<br>";
		}
	echo '<INPUT TYPE="button" VALUE="Salir" onClick="javascript:salir();"><br>';

	if ($dir=="")
		{
			$dir="./";
		}
		else
		{
		echo '<INPUT TYPE="button" VALUE="<-Atrás" onClick="window.history.back();"><br>';
		}
		//Ponemos boton de leer playlist
		echo ' <FORM METHOD="POST" ACTION="index.php"><INPUT TYPE="submit" value="Ir a PlayList" NAME="lista"><br><br></form>';
		//Estamos en el principio

		if (is_dir($dir)) {
			if ($dh = opendir($dir)) 
			{
				while (($file = readdir($dh)) !== false) 
				{
					
				  if ($file!="." && $file!=".." )
                   		   {				  
					$archivos[]=$file;   
						}
				}	
			  }
				closedir($dh);
			}
		

	sort($archivos);
	$i=0;

	echo "<FORM METHOD='post' ACTION='index.php'>";
	$entrado=0;
	foreach($archivos as $cosa)
	{
	
					if (filetype($dir."/".$cosa)=='file')
						{
							
						        if (strtoupper(substr($cosa,-3))=='MP3') {
							    $cosa=str_replace("'","----",$cosa); 
							    echo "<input type='checkbox' name='check$i' value='$dir/$cosa'>$cosa<br>";
							     $i++;
							     $entrado=1;	
							}
						}
						else
						{
							if ($anterior=="")
							{
								echo "Ir a-><a href='$nombrebase$dir$cosa'> $cosa </a><br>";
							}
							else 
							{
								if (filetype($anterior."/".$cosa)!="file")
								 {echo "Ir a-><a href='$nombrebase$anterior/$cosa'> $cosa </a><br>";}
							}
						}	


	}
	if ($entrado==1) {echo "<input type='submit' value='Guardar en Playlist' name='enviar'>";}

	echo "</form>";

	$_SESSION['ruta']=$dir;
	
	echo "<br><br>";
	
	$locale = 'es_ES.UTF-8';
	setlocale(LC_ALL, $locale);
	putenv('LC_ALL='.$locale);
	
	$header='<?xml version="1.0" encoding="ISO-8859-1"?><playlist version="1" xmlns="http://xspf.org/ns/0/"><trackList>';
	
	$resultado=scandir($dir);
	
	
	
	$data=$resultado;
	
	for ($i=0;$i<count($data);$i++)
		{
		   if (strtoupper(substr($data[$i],-3))=="MP3")
		      {
			$longitud=$data[$i];
			$cadena=$data[$i];
			$nombre=explode("/",$cadena);
			$nombre=$nombre[count($nombre)-1];
			//$cadena=strtolower($cadena);
			//$salida=$salida."<track><location>".$dir."/".$cadena."</location><title>".$nombre."</title><album></album><creator></creator></track>\r\n";
			$salida=$salida."<track><location>".$dir."/".$cadena."</location><title>$nombre</title><creator>-</creator></track>";
			$salida=str_replace('location>./','location>',$salida);
	              }
		}

	$salida=$salida.'</trackList></playlist>';
	$salida=$header.$salida;
	$myFile = "playlist.xml";
	$fh = fopen($myFile, 'w') or die("can't open file");
	fwrite($fh, $salida);
	fclose($fh);
}







function directoryToArray($directory, $recursive) {
	$array_items = array();
	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($directory. "/" . $file)) {
					if($recursive) {
						$array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
					}
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				} else {
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				}
			}
		}
		closedir($handle);
	}
	return $array_items;
}



?>


</body>
</html>
