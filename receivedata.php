<?php
	$host = "localhost";
	$user = "root";
	$password = "";
	$db = "gsr";
	$connessione = new mysqli($host, $user, $password,$db);

	if ($connessione->connect_errno) 
	{
		echo "Connessione fallita: ". $connessione->connect_error . ".";
		exit();
	}
	//Ogni tabella è identificata da Giorno-Mese-Anno. All'interno della tabella ogni sessione di guida è identificata Anno-Mese-Giorno-Ora-Minuto-Secondo
	//Quando Arduino inizia una sessione tramite HTTP GET invia il primo valore GSR e il timestamp(Anno-Mese-Giorno-Ora-Minuto-Secondo) della sessione
	
	$timestamp = strtotime($_GET['time']);			//Dalla stringa ottengo l'oggetto Data
	
	$timedata = date('dmY',$timestamp); 			//Ottengo la data(Giorno-Mese-Anno) per la creazione della tabella
	$timestamp  = date('Y-m-d H:i:s',$timestamp);	//Ottengo il timestamp per identificare la sessione e per soddisfare il formato richiesto da Mysql
	$table = "gsr_" + $timedata;					//Definisco il nome della tabella
	
	$GSR = $_GET['GSR'];							//Variabile GSR
	if(isset($_GET['ok']))							//Se identifico inizio sessione, controllando il campo OK, ottengo il timestamp per creare la sessione e inserisco la prima lettura.
	{
					
		$sql = "SHOW TABLES LIKE '".$table."'"; 
		$result = $connessione->query($sql);		//Controllo se la tabella del giorno corrente è stata già creata.

		if ($result->num_rows > 0) {} 				//Se la tabella già esiste non faccio nulla
		else										//Altrimenti creo la tabella
		{	
			$sql = "CREATE TABLE `".$table."` ( `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
														`GSR` INT UNSIGNED NOT NULL , 
														`time` TIMESTAMP NULL ,
														`diff_event` INT NULL ,
														PRIMARY KEY (`ID`)) ENGINE = InnoDB";
														//diff_event rappresenta la differenza in caso di variazione umore rilevata. E' null quando non è rilevata tale variazione
										
			$connessione->query($sql);
													
		}
		
		
		$sql = "INSERT INTO `".$table."` ( `GSR` , `time` ) VALUES ( ".$GSR." , '".$timestamp."' )";		//Inserimento prima lettura della sessione nella rispettiva tabella. 
		
		$connessione->query($sql);
	}
	else											//Se il campo OK non è impostato sto ricevando letture successive alla prima e quindi non ho bisogno di ottenere il timestamp in quanto la sessione è già stata creata						
	{					
		//Il campo diff_event rappresenta la variazione tra il valore GSR corrente e quello precedente. Se tale variazione è maggiore di una certa costante,viene segnalato il trigger con un valore non NULL.
		//Se invece la variazione non supera tale costante allora diff_event è NULL. Ricercando i diff_event diversi da NULL identifichiamo velocemente la rilevazione della variazione dell'umore
		//Diff_event non è calcolato per la prima lettura in modo che non venga calcolato rispetto all'ultima lettura della sessione precedente se presente nello stesso giorno
		
		$sql = "SELECT GSR FROM `".$table."` ORDER BY ID DESC LIMIT 1 ";//Restituisce il valore GSR della lettura precedente
		$result = $connessione->query($sql);
		$previous_gsr = mysqli_fetch_row($result);
		$diff = $GSR-$previous_gsr[0];									//Calcolo diff_event
		if($diff>60 || $diff<-60)										//Valuto se la variazione in valore assoluto è maggiore di una certa costante
		{
			$sql = "INSERT INTO `".$table."` ( `GSR` , `diff_event` ) VALUES (".$GSR." , ".$diff." )";	//Se ciò accade imposto diff_event
			$connessione->query($sql);
		}
		else{
			$sql = "INSERT INTO `".$table."` ( `GSR` ) VALUES (".$GSR." )";								//Altrimenti diff_event non viene impostato
			$connessione->query($sql);
		}
		mysqli_free_result($result);
	}
	// chiusura della connessione
	$connessione->close();
?> 