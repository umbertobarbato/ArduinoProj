<?php
		$data = "8052018";
		$sessione = 0;
		$host = "localhost";
		$user = "root";
		$password = "";
		$db = "gsr";
		
		error_reporting(E_ERROR | E_PARSE);
		$table = /*"gsr_"*/+$data;
		
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
					$durata_sessione_ms = (int)($ID_numcampioni*100);					//Calcolo il tempo di guida dell'intera giornata in ms
					$durata_sessione_s = (int)($durata_sessione_ms/1000)-3600;		//Passo il tempo in secondi
					
					$durata_sessione = date("H:i:s",$durata_sessione_s);			//Ottengo una stringa formattata in Ora:Minuti:Secondi
					
					$str = "Nella sessione selezionata hai guidato per ".$durata_sessione;
					$sql = "SELECT AVG(`diff_event`) AS diff_media FROM `".$table."` WHERE `diff_event` IS NOT NULL AND (`ID` > ".$ID_iniziosessione." AND `ID` < ".$ID_finesessione.")";
					$result = $connessione->query($sql);
					if ($result->num_rows > 0) 
					{
						$row = $result->fetch_array();
						$media = $row['diff_media'];
						if($media>60 || $media<-60)									//In base al valore assunto dalla media si stabilisce l'eventuale livello di stress
						{
							$str = $str." e sei stato stressato.";
							return $str;
						}
						else
						{
							$str = $str." e sei stato rilassato.";
							return $str;
						}
					} 
					else
					{
						$str = $str." e sei stato rilassato.";			//Se non ci sono diff_event
						return $str;
					}
				}
				else
				{
					$sql = "SELECT COUNT(*) FROM `".$table."` WHERE `ID` > ".$ID_iniziosessione;	//Ottengo il numero di campioni della sessione
					$result = $connessione->query($sql);
					$ID_numcampioni = mysqli_fetch_row($result);							//Il primo attributo dell'oggetto result è il numero di campioni della sessione
					$durata_sessione_ms = (int)($ID_numcampioni[0]*100);					//Calcolo il tempo di guida dell'intera sessione in ms
					$durata_sessione_s = (int)($durata_sessione_ms/1000)-3600;		//Passo il tempo in secondi
					
					$durata_sessione = date("H:i:s",$durata_sessione_s);			//Ottengo una stringa formattata in Ora:Minuti:Secondi
					
					$str = "Nella sessione selezionata hai guidato per ".$durata_sessione;
					$sql = "SELECT AVG(`diff_event`) AS diff_media FROM `".$table."` WHERE `diff_event` IS NOT NULL AND `ID` > ".$ID_iniziosessione;
					$result = $connessione->query($sql);
					if ($result->num_rows > 0) 
					{
						$row = $result->fetch_array();
						$media = $row['diff_media'];
						if($media>60 || $media<-60)									//In base al valore assunto dalla media si stabilisce l'eventuale livello di stress
						{
							$str = $str." e sei stato stressato.";
							return $str;
						}
						else
						{
							$str = $str." e sei stato rilassato.";
							return $str;
						}
					} 
					else
					{
						$str = $str." e sei stato rilassato.";			//Se non ci sono diff_event
						return $str;
					}
				}
			}
			
		}
		else
		{
			$str = "Per la sessione selezionata non è stato registrato alcun dato.";	//Se non è presente la tabella con la data selezionata
			return $str;
		}
		$connessione->close();
		?>