<?php
	error_reporting(E_ERROR | E_PARSE);
	header("Content-Type: image/png");									//L'immagine viene creata in seguito ad una HTTP GET in cui vengono forniti la sessione 
	$sessione = $_GET['sess'];										//(attraverso un intero che parte da 0) che si vuole visualizzare e la relativa data
	$data = $_GET['data'];
	$host = "localhost";
	$user = "root";
	$password = "";
	$db = "gsr";
	$table = "gsr_"+$data;
	$connessione = new mysqli($host, $user, $password,$db);

	if ($connessione->connect_errno) 
	{
		echo "Connessione fallita: ". $connessione->connect_error . ".";
		exit();
	}
			
	$sql = "SELECT `ID` FROM `".$table."` WHERE `time` IS NOT NULL";	//Per ottenere la sessione desiderata si selezionano dalla tabella gli ID in cui il timestamp non è NULL (Ossia gli inizi delle singole sessioni)
	
	$result = $connessione->query($sql);
			
	if ($result->num_rows > 0) 
	{
		$row = NULL;
		for($i=0;$i<=$sessione;$i++)									//Si avvia un ciclo su gli ID che rappresentano l'inizio di ogni sessione finché non raggiungiamo sessione-esimo
																		//che rappresenta la sessione che si vuole visualizzare
		{
			$row = $result->fetch_array();						
		}
		$ID_iniziosessione = $row['ID'];								//Una volta usciti dal for il fetch_array restituisce la riga corrispondente al sessione-esimo
		
		if($sessione < ($result->num_rows-1))							//Se la sessione che si vuole visualizzare non è l'ultima tra le sessioni disponibili 
		{
			$row = $result->fetch_array();								//Considero la riga della sessione successiva e il suo ID
			$ID_finesessione = $row['ID'];
			$ID_numcampioni = $ID_finesessione-$ID_iniziosessione;		//Per valutare il numero di campioni
			$sql = "SELECT MAX(`GSR`) AS MaxH FROM `".$table."` WHERE `ID` > ".$ID_iniziosessione." AND `ID` < ".$ID_finesessione;
			$result = $connessione->query($sql);
			$row = $result->fetch_array();
			$MaxH = $row['MaxH']+50;
			$im = @imagecreate($ID_numcampioni*3, $MaxH)					//Si crea un'immagine la cui larghezza corrisponde al numero di campioni
				or die("Cannot Initialize new GD image stream");
			$color_fondo = imagecolorallocate($im, 255, 255, 255);
			$azzurro = imagecolorallocate($im, 0, 102, 204);	
			$sql = "SELECT `GSR` FROM `".$table."` WHERE `ID` > ".$ID_iniziosessione." AND `ID` < ".$ID_finesessione;
																		//Nella tabella seleziono i valori GSR dall'inizio alla fine della sessione
			$result = $connessione->query($sql);
			for($i=0;$row = $result->fetch_array();$i++)				//Avvio un ciclo finché la fetch_array restituisce una riga (lettura)
			{
				imagesetpixel($im,$i*3,$MaxH-$row['GSR'],$azzurro);			//Per ogni lettura (GSR) si stampa un pixel all'altezza relativa, ciò viene fatto per ogni campione di tempo
			}
		}
		else															//Se la sessione che si vuole visualizzare è l'ultima sessione disponibile
		{
			$sql = "SELECT MAX(`GSR`) AS MaxH FROM `".$table."` WHERE `ID` > ".$ID_iniziosessione;
			$result = $connessione->query($sql);
			$row = $result->fetch_array();
			$MaxH = $row['MaxH']+50;
			$sql = "SELECT `GSR` FROM `".$table."` WHERE `ID` > ".$ID_iniziosessione; //Nella tabella seleziono i valori GSR dall'inizio alla fine della sessione
			$result = $connessione->query($sql);
			$ID_numcampioni = $result->num_rows;						//Si calcola diversamente il numero di campioni ossia il numero di righe della query sopra
			$im = @imagecreate($ID_numcampioni*3, $MaxH)
				or die("Cannot Initialize new GD image stream");
			$color_fondo = imagecolorallocate($im, 255, 255, 255);
			$azzurro = imagecolorallocate($im, 0, 102, 204);
			for($i=0;$row = $result->fetch_array();$i++)
			{
				imagesetpixel($im,$i*3,$MaxH-$row['GSR'],$azzurro);
			}			
		}
	}
	$connessione->close();
	imagepng($im);
	imagedestroy($im);
?>
