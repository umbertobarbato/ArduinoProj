<?php
	function RipielogoGiornaliero($data)				//Funzione che restituisce una stringa di riepilogo di una giornata da inserire nella lista eventi a destra
	{
		$host = "localhost";
		$user = "root";
		$password = "";
		$db = "gsr";
		
		error_reporting(E_ERROR | E_PARSE);
		$table = "gsr_"+$data;
		
		$connessione = new mysqli($host, $user, $password,$db);
		
		if ($connessione->connect_errno) 
		{
			echo "Connessione fallita: ". $connessione->connect_error . ".";
			exit();
		}
		$sql = "SHOW TABLES LIKE '".$table."'"; 
		$result = $connessione->query($sql);			//Controllo se la tabella del giorno corrente è stata già creata.
		
		if ($result->num_rows > 0)						//Se la tabella della data in ingresso esiste
		{
			$sql = "SELECT COUNT(*) FROM `".$table."`";	//Ottengo il numero di campioni della giornata
			$result = $connessione->query($sql);
			$n_letture = mysqli_fetch_row($result);							//Il primo attributo dell'oggetto result è il numero di campioni della giornata
			$durata_sessione_ms = (int)($n_letture[0]*100);					//Calcolo il tempo di guida dell'intera giornata in ms
			$durata_sessione_s = (int)($durata_sessione_ms/1000)-3600;		//Passo il tempo in secondi
			
			$durata_sessione = date("H:i:s",$durata_sessione_s);			//Ottengo una stringa formattata in Ora:Minuti:Secondi
			
			$str = "Nella data selezionata hai guidato per ".$durata_sessione;						
			
			$sql = "SELECT `diff_event` FROM `".$table."` WHERE `diff_event` IS NOT NULL";	//Ottengo il diff_event degli eventi di trigger per farne la media
			
			$result = $connessione->query($sql);
			
			if ($result->num_rows > 0) 
			{
				$media = 0;
				while($row = $result->fetch_assoc()) 						//Per ogni riga in cui diff_event è presente tale riga è inserita in row
				{
					$media = $media + $row["diff_event"];					//Fai la sommatoria di ogni diff_event
				}
				$media = $media/$result->num_rows;							//Fai la media sul numero di diff_event
				if($media>60 || $media<-60)									//In base al valore assunto dalla media si stabilisce l'eventuale livello di stress
				{
					$str = $str." e sei stato generalmente stressato.";
					return $str;
				}
				else
				{
					$str = $str." e sei stato generalmente rilassato.";
					return $str;
				}
			} 
			else
			{
				$str = $str." e sei stato generalmente rilassato.";			//Se non ci sono diff_event
				return $str;
			}
		}
		else
		{
			$str = "Per la data selezionata non è stata registrata nessuna sessione.";	//Se non è presente la tabella con la data selezionata
			return $str;
		}
		$connessione->close();
	}
?>