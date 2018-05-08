#include <SPI.h>
#include <Ethernet.h>

byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };    //Crea un array di byte hex che identifica l'indirizzo MAC dell'Arduino

IPAddress ip(169,254,190,12);                           //Definisco l'IP dell'Arduino nella rete locale
IPAddress server(169,254,190,51);                       //Nota: si deve fare in modo che questo indirizzo corrisponde all'indirizzo IP del server nella rete locale
EthernetClient client;                                  //Oggetto EthernetClient usato per aprire e chiudere connessioni HTTP

const int GSR=A2;                                       
int threshold=0;
int sensorValue;
String Data = "";

void setup()
{                          
  Serial.begin(9600);                                   //Inizializzo la porta seriale
  Ethernet.begin(mac, ip);                              //Inizializzo la connessione Ethernet fornendo il MAC dell'arduino e l'indirizzo IP che voglio associare a tale MAC (creando una rete locale)
  delay(1000);
  if (client.connect(server, 80))                       //Se la connessione al server sulla porta 80 avviene correttamente
  {                                                     //Tramite una HTTP GET chiamo la funzione PHP che restituisce il timestamp per identificare la sessione
    client.print("GET /getdata.php");                   //Continuo dell'intestazione HTTP...
    client.println(" HTTP/1.1");
    client.print("Host: ");
    client.println(server);
    client.println("Connection: close");
    client.println();
    client.println();
  }
  
  while(client.connected())                             //Attendo di ricevere il pacchetto in risposta alla HTTP GET
  {
    while (client.available())                          //Attendo il termine del pacchetto
    {
      if(client.read()=='\r' && (client.available() && client.read()=='\n') && (client.available() && client.read()=='\r') && (client.available() && client.read()=='\n'))    //Per evitare di leggere anche l'header del pacchetto in risposta attendiamo la sequenza \r\n\r\n dopo il quale inizia il campo dati
      {
        for(int i=0;i<14;i++)                           //Essendo il timestamp composto da 14 caratteri scorro la sequenza di interi 14 volte
        {
          char c = client.read();                       
          Data += c;                                    //Salvando i caratteri in una stringa in maniera consecutiva
        }
      }
   }
  }
  client.stop();                                        //Chiusura connessione richiesta data
  
  long sum=0;                                           //Per normalizzare la prima lettura in caso di misurazioni sfalsate eseguo una media sui primi 500 campioni
  for(int i=0;i<500;i++)
  {
    sensorValue=analogRead(GSR);
    sum += sensorValue;
    delay(5);
  }
  threshold = sum/500;
  if (client.connect(server, 80))                       //Intestazione della richiesta HTTP GET per inviare il primo valore GSR al server
  {                                                     //NOTA BENE: Il valore ok=1 è utilizzato per far capire al server l'inizio di una sessione e dunque il salvataggio del timestamp relativo
    client.print("GET /receivedata.php?time=");
    client.print(Data);
    client.print("&GSR=");
    client.print(sensorValue);
    client.print("&ok=1");
    client.println(" HTTP/1.1");
    client.print("Host: ");
    client.println(server);
    client.println("Connection: close");
    client.println();
    client.println();
    client.stop();
  }   
}

void loop()
{  
  delay(100);                                            //In seguito all'invio del primo dato, i successivi verranno inviati ogni 100ms
  sensorValue=analogRead(GSR);                          
  if (client.connect(server, 80))                        //Intestazione della richiesta HTTP GET per inviare il primo valore GSR al server
  {                                                      //NOTA BENE: Il valore ok=1 non è impostato in quanto stiamo inviando le letture successive all'inizio della sessione
    client.print("GET /receivedata.php?time=");
    client.print(Data);
    client.print("&GSR=");
    client.print(sensorValue);
    client.println(" HTTP/1.1");
    client.print("Host: ");
    client.println(server);
    client.println("Connection: close");
    client.println();
    client.println();
    client.stop();
  }
  else
  {
    Serial.println("connection refused");
  }
}

