# IPSymconIFTTT
[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-%3E%205.1-green.svg)](https://www.symcon.de/service/dokumentation/installation/)
![Code](https://img.shields.io/badge/Code-PHP-blue.svg)
[![StyleCI](https://github.styleci.io/repos/72363004/shield?branch=master)](https://github.styleci.io/repos/72363004)

Modul für IP-Symcon ab Version 4.1 ermöglicht die Kommunikation mit dem Dienst IFTTT.
Anbindung von IFTTT an IP-Symcon über den _Webhooks_ Service.

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Installation](#3-installation)  
4. [Funktionsreferenz](#4-funktionsreferenz)
5. [Konfiguration](#5-konfiguration)  
6. [Anhang](#6-anhang)  

## 1. Funktionsumfang

Der Internet Dienst IFTTT bietet die Möglichkeit verschiedene Internetdienste miteinander zu verknüpfen
und so jeweils aus einem Trigger und einer Aktion eine Arbeitsablauf zu erstellen, genannt Applet. Im Gegensatz zu andern
Diensten lassen sich bei IFTTT nur ein Trigger mit einer Aktion verknüpfen.
Der _Webhooks_ Service von IFTTT kann als Trigger (__*THIS*__) oder auch als Aktion (__*THAT*__) in IFTTT Applets eingebunden werden. Dabei
kann der _Webhooks_ Service mit beliebigen anderen Services zu einem IFTTT Applet verbunden werden.

####Trigger
Der Trigger des _Webhooks_ Service erlaubt die Definition eines Events und lässt 3 Variablen zu, die übertragen werden können. Mit
dem Modul können Variablen verlinkt werden oder im Modul selber Werte als Konstante eingetragen werden, die dann von IP-Symcon
an IFTTT geschickt werden um ein Trigger für ein Applet auszulösen. Die übergebenen Variablen können dann in einer
IFTTT Aktion weiterverwertet werden. Auf dieser Weise lassen sich Kanäle von IFTTT mit IP-Symcon triggern.

####Aktion
Bei Auslösen eines Triggers eines IFTTT Applets kann eine Aktion ausgelöst werden, die Informationen an IP-Symcon weiterleitet.
Hierbei hängt es vom gewählten Trigger ab welche Zutaten (Ingredients) zur Verfügung stehen. Bei einem Email Channel könnten zum
Beispiel From Subject und Body übertragen werden. Abhängig vom Trigger stehen dann unterschiedliche Auswahlen zur Verfügung.
Bei Aktionen kann die Anzahl der Variablen die übergeben werden frei definiert werden.  
In IP-Symcon werden die ankommenden Daten in eine Variable geschrieben. Da die Anzahl der Kombinationsmöglichkeiten an Services
und damit die zu übermittelnden Daten von Fall zu Fall unterschiedlich sind, müssen die Anzahl der Variablen im Modul eingestellt
werden. 

Weiterführende Information zu IFTTT

[IFTTT](https://ifttt.com "IFTTT")

## 2. Voraussetzungen

 - IPS 4.1
 - IP-Symcon Connect
 - Account bei [IFTTT](https://ifttt.com "IFTTT")
 - IFTTT Account mit eingerichteten _Webhooks_ Service

## 3. Installation

### a. Vorbereitungen in IFTTT
 Anmelden bei IFTTT bzw. Erstellen eines neuen Nutzers falls noch kein Account vorhanden.
 Nach dem Anmelden in der Menüleiste oben auf *My Applets* wechseln.
 
![Select Channel](img/ifttt-menu1.png?raw=true "IFTTT Menu")

 Auf der Reiter _Services_ wechseln und im Suchfeld *Webhooks* eingeben
 
![Select Maker Channel](img/ifttt-2.png?raw=true "Select Webhooks")
 
 Den _Webhooks_ Service auswählen und auf _Settings_ wechseln. Connect in den Settings herstellen.
 Wenn wir im _Webbhooks_ oben rechts auf _Dokumenation_ klicken erscheint ganz oben auf der neuen Seite der Eintrag
 _Your key is:_ Den Key schreiben wir uns auf, diesen benötigen wir um später Applets aus IP-Symcon zu triggern.
	  
### b. Laden des Moduls

Die Webconsole von IP-Symcon mit _http://<IP-Symcon IP>:3777/console/_ öffnen. 


Anschließend oben rechts auf das Symbol für den Modulstore (>5.1) klicken

![Store](img/store_icon.png?raw=true "open store")

Im Suchfeld nun

```
IFTTT
```  

eingeben

![Store](img/module_store_search.png?raw=true "module search")

und schließend das Modul auswählen und auf _Installieren_

![Store](img/install.png?raw=true "install")

drücken.


#### Alternatives Installieren über Modules Instanz

Den Objektbaum _Öffnen_.

![Objektbaum](img/objektbaum.png?raw=true "Objektbaum")	

Die Instanz _'Modules'_ unterhalb von Kerninstanzen im Objektbaum von IP-Symcon (>=Ver. 5.x) mit einem Doppelklick öffnen und das  _Plus_ Zeichen drücken.

![Modules](img/Modules.png?raw=true "Modules")	

![Plus](img/plus.png?raw=true "Plus")	

![ModulURL](img/add_module.png?raw=true "Add Module")
 
Im Feld die folgende URL eintragen und mit _OK_ bestätigen:

```
https://github.com/Wolbolar/IPSymconIFTTT.git
```  
	
Anschließend erscheint ein Eintrag für das Modul in der Liste der Instanz _Modules_    

Es wird im Standard der Zweig (Branch) _master_ geladen, dieser enthält aktuelle Änderungen und Anpassungen.
Nur der Zweig _master_ wird aktuell gehalten.

![Master](img/master.png?raw=true "master") 

Sollte eine ältere Version von IP-Symcon die kleiner ist als Version 5.1 (min 4.1) eingesetzt werden, ist auf das Zahnrad rechts in der Liste zu klicken.
Es öffnet sich ein weiteres Fenster,

![SelectBranch](img/select_branch.png?raw=true "select branch") 

hier kann man auf einen anderen Zweig wechseln, für ältere Versionen kleiner als 5.1 (min 4.1) ist hier
_Old-Version_ auszuwählen. 


### c. Einrichtung in IP-Symcon
	
In IP-Symcon wird von jedes Event das wir triggern wollen eine seperate Instanz angelegt. Der IFTTT IO und IFTTT Splitter wird
automatisch mit angelegt. Um die Instanz zu erstellen wechseln wir in die Kategorie, unter der wir die Instanz platzieren wollen
und erstellen mit _Instanz hinzufügen_ (_Rechtsklick -> Objekt hinzufügen -> Instanz_)  eine neue Instanz.

![Instanz erstellen](img/ifttt_instance.png?raw=true "Instanz erstellen")

Über IFTTT finden wir die Instanz und mit weiter und Ok wird diese angelegt.

##### Auswahl der Kommunikation und Anzahl der Variablen
Es gibt die Möglichkeit aus IP-Symcon herraus ein Applet zu triggern als auch die Möglichkeit Daten von IFTTT zu erhalten. Jede IP-Symcon IFTTT Instanz steht für ein Event das in IFTTT auftritt es können beliebig viele Instanzen angelegt werden.
Nach dem Öffen der Instanz erscheint zunächst eine Abfrage ob die aktuelle Instanz nur zum Senden, Empfangen oder zum Senden/Empfangen, Google Home dienen soll.

![Kommunikationabfrage](img/selection.png?raw=true "Auswahl")

Nachdem die passende Auswahl getroffen wurde bestätigen wir mit _Änderungen Übernehmen_.

![Apply_Changes](img/Accept_Changes.png?raw=true "Apply Changes")

Nun haben wir je nach vorheriger Auswahl weitere Optionen. Jetzt legen wir die Anzahl der Variablen fest die die Instanz benutzten soll.
Um ein Event in IFTTT zu triggern ist grundsätzlich gar keine Variable notwendig, es reicht der _Eventname_ zum Triggern eines Applets aus. Es gibt aber
die Möglichkeit bis zu _*drei Werte*_ an IFTTT mit dem Trigger zu übertragen. Wir wählen also bei _Senden bis zu 3 Werte_ bei Bedarf aus. In die umgekehrte
Richtung ist es abhängig vom Trigger wieviele Variablen gesendet werden.

![Varanzahl](img/ifttt_form_makerkey.png?raw=true "Variablenanzahl eintragen")

Da es unzählige Channels und damit unzählige Möglichkeiten gibt was von IFTTT an IP-Symcon geschickt werden kann, je nachdem welches Applet
man verwendet, muss pro angelegter Instanz vom Nutzer festgelegt werden vielviele Variablen von IFTTT erwartet werden. Dabei ist dabei darauf zu
achten das der gewählte Variablentyp dem zu erwartenden Wert aus IFTTT entspricht, ansonsten werden die Daten nicht abgelegt. Alternativ können auch
keine existierenden Variablen angegeben werden sondern ein Haken gesetzt werden im Modul, dann legt das Modul beim ersten Eintreffen von Daten in der
Instanz in IP-Symcon die Variablen passend zum Variablentyp der Daten an. Wichtig ist nur das die Anzahl der Variablen mit der Anzahl der von IFTTT
versendeten Daten übereinstimmt. Die maximale Anzahl an Variablen, die das Modul zur Zeit verwaltet liegt bei 15. Sollte es dennoch Recipes geben die
mehr Variablen benötigen kann dies zukünftig aber auch noch erhöht werden.

Nachdem die passende Anzahl der Variablen selektiert wurde bestätigen wir mit _Änderungen Übernehmen_ anschließend **schließen wir die Instanz** und öffnen diese dann erneut.

Nun füllen wir im Feld IFTTT Makerkey den Key vom IFTTT Maker Channel ein den wir notiert haben (s.o.)

![Makerkey eintragen](img/ifttt_form_that.png?raw=true "form that")

Der Eventname kann frei gewählt werden. Es ist nur darauf zu achten das keine Umlaute, Sonderzeichen und Leerzeichen im Eventnamen
verwendet werden. Dadurch das der Eventname frei bestimmt werden kann können also beliebig viele Instanzen in IP-Symcon angelegt werden
mit jeweils einem anderen Eventnamen. Diese Triggern dann unterschiedliche Events (Applets) in IFTTT. 
Unter Wert 1-3 kann eine Variable ausgewählt werden die an IFTTT geschickt werden soll.

Alternativ besteht auch die Möglichkeit keine Variablen auszuwählen sondern stattdessen einen konstanten Wert direkt in das Modul einzutragen
das bei einem Event an IFTTT übergeben werden soll. Wenn der Wert aus dem Modul geschickt werden soll ist zusätzlich zum Eintrag noch ein Haken
bei _Modul Wert nutzen_ zu setzten. Das Haken bei _IFTTT Return_ ist standardmäßig deaktiviert. Wenn hier ein Haken gesetzt wird erscheint eine kurze
Meldung von IFTTT im Webfront von IP-Symcon wenn die Nachricht verschickt worden ist. 
Wenn alle Einstellungen vorgenommen worden sind und auf Übernehmen geklickt wurde müssen wir zunächst noch IFTTT konfigurieren bevor wir dann später
mit Trigger Event eine Nachricht mit den zugeordneten Werten an IFTTT verschicken können.
	
### d. Konfiguration des Webhooks Applets in IFTTT

##### Webhooks als Trigger eines Applets

Jetzt müssen wir in IFTTT noch ein Applet erstellen das unsere Nachrichten von IP-Symcon an IFTTT entgegennimmt.
Dazu wechseln wir wieder zu IFTTT wechseln zu _My Applets_ und erstellen ein neues Applet mit _New Applet_.

![Create Recipe](img/newapplet.png?raw=true "New Applet")

Dann __*this*__ auswählen

![select this](img/this.png?raw=true "select this")

Im Suchfenster _Webhooks_ eingeben und den _Webhooks_ Service auswählen

![search maker](img/step1webhooks.png?raw=true "search webhooks")

Bei Choose a Trigger auf Receive a web request drücken

![choose trigger](img/step2webhooks.png?raw=true "choose trigger")

Hier geben wir jetzt den Eventnamen ein den wir im IP-Symcon Modul gewählt haben. Im Beispiel ips_test. Dann drücken wir auf Create Trigger.

![create trigger](img/step3webhooks.png?raw=true "create trigger")
	
Im nächsten Schritt __*That*__ auswählen

![select that](img/step4webhooks.png?raw=true "select that")

Hier wählen wir jetzt einen der verfügbaren Channel in IFTTT aus der in IFTTT getriggert werden soll und können die 3 Werte die aus IP-Symcon
übergeben worden sind in der Action	verwenden.
Im Beispiel nutze ich einfach mal gmail, ist vielleicht etwas sehr banal weil man ja direkt von IP-Symcon Emails bei einem Event verschicken kann,
dies soll ja aber nur ein Beispiel für eines der vielen IFTTT Channels sein.

![select gmail](img/step1gmail.png?raw=true "select gmail")
	
Hier gehen wir auf Send a email

![send gmail](img/step2gmail.png?raw=true "send gmail")

Hier finden wir jetzt wieder unseren Eventnamen und die Werte die wir von IP-Symcon an IFTTT geschickt haben. So können wir dann eben mit
unterschiedlichen Events und unterschiedlichen Werten Recipes basteln.

![values gmail](img/step3gmail.png?raw=true "values1 gmail")

	
Mit _*Create Action*_ speichern wir das Recipe ab.

Mit Finish wird nun das Applet komplett erstellt.

![finish](img/step4gmail.png?raw=true "finish")

##### Webhooks als Aktion (That) eines Applets	
Wenn wir Daten von IFTTT an IP-Symcon schicken wollen binden wir den _Webhooks_ Service als Aktion (That) in ein Applet ein. Das Applet schickt dann Daten von IFTTT an
IP-Symcon wenn eine Aktion durch ein Recipe getriggert wird.
	
Zunächst wird ein Trigger (This) erstellt mit dem Service der den _This_ (IF) Anteil des Applets stellen soll (s.o.).
	
Wie oben bereits beschrieben hier das gleiche Vorgehen
- _*New Applet*_
- _*This*_ auswählen
- beliebigen verfügbaren Service auswählen (zur Vereinfachung nehme ich wieder gmail als Beispiel)

z.B. _Any new email in inbox_ auswählen

![any new email](img/gmail1action.png?raw=true "any new email")

__*That*__ wählen wir diesmal als Aktion Maker Channel aus

![action gmail that](img/gmailthat.png?raw=true "action gmail that")

Choose action service

_Webhooks_

![make a web request](img/makeawebrequest.png?raw=true "make a webrequest")

_Make a web request_ auswählen



Jetzt müssen wir die passenden Werte eintragen

![form request](img/webhookrequestform.png?raw=true "form request")
	

Wir schauen die ObjektID der Instanz, an die die Daten geschickt werden sollen, in IP-Symcon nach und notieren diese.

Die Daten werden als JSON an IP-Symcon geschickt.

| Eigenschaft| Wert                                                                                                          |
| :--------: | :-----------------------------------------------------------------------------------------------------------: |	
|URL:        | https:// _IP-Symon Connect Adresse_ .ipmagic.de/hook/IFTTT                                                      |
|Method:     | _POST_                                                                                                        |
|Content Type|  _application/json_                                                                                           |  
|Body        |  {"username":"ipsymcon","password":"mypassword","objectid":12345,"values":{"EventName":"{{EventName}}",       |
|            |  "Value1":"{{Value1}}", "Value2":"{{Value2}}","Value3":"{{Value3}}", "OccurredAt":"{{OccurredAt}}"<<<}>>>}    |


Das Modul in IP Symcon erwartet die Daten wie folgt:

| Eigenschaft           | Wert                                                                                                          |
| :-------------------: | :-----------------------------------------------------------------------------------------------------------: |
| _**URL**_             | IP-Symcon Connect Adresse/hook/IFTTT                                                                          |
|_**Method**_           | POST                                                                                                          |
|_**Content Type**_     | application/json                                                                                              |                                                                                          |
|_**Body**_             |  {"username":"ipsymcon","password":"mypassword","objectid":12345,"values":{"EventName":"{{EventName}}",       |
|                       |  "Value1":"{{Value1}}", "Value2":"{{Value2}}","Value3":"{{Value3}}", "OccurredAt":"{{OccurredAt}}"<<<}>>>}    |
|\[_username_\]         | Username im IFTTT IO wird auch in der Testumgebung der Instanz angezeigt                                      |
|\[_password_\]         | Passwort im IFTTT IO wird auch in der Testumgebung der Instanz angezeigt                                      |
|_objectid_             | ObjektID der IFTTT Instanz die die Daten entgegen nehmen soll                                                 |
|_values_ (\[payload\]) |{"value1":"value1string","value2":value2boolean,"value3":value3integer,"value4":value4float}                   |
	
Die Values werden innerhalb der Form als JSON übergeben. Keys sind immer in "" zu setzen ebenso String Variablen.
Integer, Float und Boolean sind keine "" um die Variable zu setzen. In dem Beispiel oben wenn es sich um eine Email
handeln würde wären also alle Variablen vom Typ String und werden in "" gesetzt.
		
Jetzt sollte wenn das Applet triggert ein Web Request an IP-Symcon geschickt werden. Der Inhalt wird in die gewählten Variablen der IFTTT Instanz
abgelegt. Sollten die Auswahl getroffen worden sein das das Modul die Variablen anlegt, so werden diese mit dem ersten Eintreffen von Daten in IP-Symcon
automatisch entsprechend dem Variablentyp der Daten in IP-Symcon angelegt.

Auf die Variablen können wir dann in IP-Symcon ein Ereigniss legen das z.B. bei einem bestimmten Wert oder einer Variablenänderung oder Variablenaktualisierung
weitere Dinge in IP-Symcon ausführt. Auf diese Weise lässt das ein oder andere in IP-Symcon einbinden für das es derzeit noch keine Skripte
oder Module gibt.

##### Google Home als Aktion (This) eines Applets	
Wenn wir Google Home zum Schalten eines Geräts in IP-Symcon benutzten wollen benutzten wir den Google Assistant Service als This Teil des Applets. Das Applet schickt dann Daten von IFTTT an
IP-Symcon wenn eine Aktion durch Google Home getriggert wird.
	
Zunächst wird ein Trigger (This) erstellt mit dem _Google Assistant_ Service.
	
Wie oben bereits beschrieben hier das gleiche Vorgehen
- _*New Applet*_
- _*This*_ auswählen
- _Google Assistant_ Service auswählen
- Zum Einschalten eines Geräts wählen wir _Say a simple phrase_ und füllen dort die Formulierung ein auf die Google Home reagieren soll
- Als _That_ wählen wir _Webhooks_ Service
- Hier tragen wir folgendes ein


| Eigenschaft| Wert                                                                                                                                 |
| :--------: | :----------------------------------------------------------------------------------------------------------------------------------: |	
|URL:        | https:// _IP-Symon Connect Adresse_ .ipmagic.de/hook/IFTTT                                                                           |
|Method:     | _POST_                                                                                                                               |
|Content Type|  _application/json_                                                                                                                  |  
|Body        |  {"username":"ipsymcon","password":"meinpasswort","objectid":12345,"values":{"EventName": "Wohnzimmer Licht","Status":true<<<}>>>}   |

Folgende Daten werden erwartet:

| Wert                  | Erläuterung                                                                                                   |
| :-------------------: | :-----------------------------------------------------------------------------------------------------------: |
|\[_username_\]         | Username im IFTTT IO wird auch in der Testumgebung der Instanz angezeigt                                      |
|\[_password_\]         | Passwort im IFTTT IO wird auch in der Testumgebung der Instanz angezeigt                                      |
|_objectid_             | ObjektID der IFTTT Instanz die die Daten entgegen nehmen soll                                                 |
|_EventName_            | Name des Events, wird in IP-Symcon als Variablenname gesetzt wenn das Modul die Variable anlegen sollte       |
|_Status_               | Wert auf den die Variable gesetzt wird, true für einschalten, false für ausschalten                           |

In IP-Symcon wählen wir _Google Home_ als Option im Konfigurationsformular.

![google home](img/ifttt_google_home.png?raw=true "google home")
 
Es gibt zwei Möglichkeiten, die erste ist es ein Skript zu starten, dieses wird im Konfigurationsformualr ausgewählt.

Ein einfachen Skript könnte so aussehen

```php
<?php
$state = $_IPS['State'];
 if($state)
 {
 	HM_WriteValueBoolean(12345, "STATE", true);
 }
 else
 {
 	HM_WriteValueBoolean(12345, "STATE", false);
 }
 ?>
```

Es wird der Wert in der Variable _**$_IPS['State']**_ an das Skript übergeben.

Die zweite Möglichkeit besteht darin das Modul eine Variable anlegen zu lassen, auf diese Variable kann dann ein Ereignis gelegt werden das ein weiteres Gerät schaltet.


###### Beispiel Google Home zur Rollladen Steuerung	

Zunächst wird ein Trigger (This) erstellt mit dem _Google Assistant_ Service.
	
Wie oben bereits beschrieben hier das gleiche Vorgehen
- _*New Applet*_
- _*This*_ auswählen
- _Google Assistant_ Service auswählen
- Zum Steuerung des Rollladen wählen wir _Say a phrase with a number_ und füllen dort die Formulierung ein auf die Google Home reagieren soll

![google assistent1](img/googleassistant1.png?raw=true "google assistent1")

![google assistent2](img/googleassistant2.png?raw=true "google assistent2")

- dann auf _Create Trigger_ drücken
- Als _That_ wählen wir _Webhooks_ Service
- Hier tragen wir folgendes ein


| Eigenschaft| Wert                                                                                                                                 |
| :--------: | :----------------------------------------------------------------------------------------------------------------------------------: |	
|URL:        | https:// _IP-Symon Connect Adresse_ .ipmagic.de/hook/IFTTT                                                                           |
|Method:     | _POST_                                                                                                                               |
|Content Type|  _application/json_                                                                                                                  |  
|Body        |  {"username":"ipsymcon","password":"meinpasswort","objectid":12345,"values":{"Level": {{NumberField}}<<<}>>>}                        |

Folgende Daten werden erwartet:

| Wert                  | Erläuterung                                                                                                   |
| :-------------------: | :-----------------------------------------------------------------------------------------------------------: |
|\[_username_\]         | Username im IFTTT IO wird auch in der Testumgebung der Instanz angezeigt                                      |
|\[_password_\]         | Passwort im IFTTT IO wird auch in der Testumgebung der Instanz angezeigt                                      |
|_objectid_             | ObjektID der IFTTT Instanz die die Daten entgegen nehmen soll                                                 |
|_Level_                | Name des Variable, wird in IP-Symcon als Variablenname gesetzt wenn das Modul die Variable anlegen sollte     |

![google assistent3](img/googleassistant3.png?raw=true "google assistent3")

In IP-Symcon wählen wir _Google Home_ als Option im Konfigurationsformular.

![google home](img/ifttt_google_home.png?raw=true "google home")
 
Es gibt zwei Möglichkeiten, die erste ist es ein Skript zu starten, dieses wird im Konfigurationsformular ausgewählt.

Ein einfachen Skript könnte so aussehen

```php
<?php
$level = $_IPS['Level'];
$hm_level = $level/100;
HM_WriteValueBoolean(12345, "LEVEL", $hm_level);
?>
```

Es wird der Wert in der Variable _**$_IPS['Level']**_ an das Skript übergeben.

Die zweite Möglichkeit besteht darin das Modul eine Variable anlegen zu lassen, auf diese Variable kann dann ein Ereignis gelegt werden das ein weiteres Gerät schaltet.



## 4. Funktionsreferenz

### IFTTT
Im der IFTTT Instanz ist einzustellen ob die Instanz nur zum Senden, Empfangen oder Senden/Empfangen
benutzt werden soll. So können für jede Anforderung die in IFTTT ein IF darstellt Variablen
und Konstanten definiert werden, die beim Auslösen des Events in IP-Symcon an IFTTT gesendet werden.
 
Triggert das Event das im Modul hinterlegt ist.
Dabei benutzt das Modul die im Modul eingestellten Werte bzw. Variablen und sendet einen Trigger an IFTTT. 

```php
IFTTT_TriggerEvent(integer $InstanceID) 
```
Parameter *$InstanceID* __ObjektID__ der IFTTT Instanz

  
```php
IFTTT_SendEventTrigger(integer $InstanceID, string $iftttmakerkey, string $event, string $value1, string $value2, string $value3);
```
*$InstanceID* ist die __ObjektID__ der IFTTT Instanz

*$iftttmakerkey* ist der Makerkey aus IFTTT

*$event* frei zu wählender Eventname. Keine Umlaute, Sonderzeichen und Leerzeichen verwenden.

*$value1 - $value3* frei zu wählende Werte die an IFTTT geschickt werden sollen.
  
   
Für den Empfang von Daten von IFTTT können Variablen im Modul ausgewählt werden. Dabei ist darauf
zu achten das der Variablentyp dem von IFTTT versendeten Daten entspricht sonst werden keine Daten
abgelegt. Alternativ kann man auch im Modul angeben dass das Modul die Variablen anlegen soll. Die
Variablen werden dann beim ersten Empfangen von Daten von IFTTT automatisch entsprechend dem Variablentyp
in IP-Symcon angelegt.  

### IFTTT IO
Der Webhook in IP-Symcon ist durch einen Benutzernamen und Passwort abgesichert. Im IFTTT IO
kann das vorgegebene Passwort bei Bedarf geändert werden. Der aktuelle Benutzername und das
aktuelle Passwort wird in der Testumgebung der IFTTT Instanz angezeigt zur Konfigurationshilfe. 


## 5. Konfiguration

### IFTTT IO:

| Eigenschaft | Typ     | Standardwert | Funktion                                                        |
| :---------: | :-----: | :----------: | :-------------------------------------------------------------: |
| username    | string  | 		       | username für IFTTT zur Authentifizierung bei IP-Symcon |
| password    | string  |              | password für IFTTT zur Authentifizierung bei IP-Symcon |

username und password sind vorab eingestellt können aber individuell angepasst werden.

### IFTTT:  

| Eigenschaft      | Typ     | Standardwert| Funktion                                                         |
| :--------------: | :-----: | :---------: | :--------------------------------------------------------------: |
| selection        | integer |      0      | Konfigurationsauswahl 1 Senden, 2 Empfang, 3 Senden/Empfang      |
| countrequestvars | integer |      0      | Anzahl der Variablen die von IFTTT beschrieben werden max 15     |
| countsendvars    | integer |      0      | Anzahl der Variablen die an IFTTT gesendet werden sollen max 3   |
| varvalue 1-3     | integer |      0      | ObjektID einer Variable                                          |
| modulinput 1-3   | integer |      0      | Aktivieren für Modulkonstante statt Variable                     |
| value 1-3        | string  |      0      | Konstante kann als Wert im Modul hinterlegt werden               |
| requestvarvalue  | integer |    false    | ObjektID einer Variable                                          |
| modulrequest     | string  |    false    | statt eigener Variable wird eine Variable vom Modul angelegt     |


## 6. Anhang

###  a. GUIDs und Datenaustausch:

#### IFTTT IO:

GUID: `{2E91373A-E70B-46D8-99A7-71A499F6783A}` 


#### IFTTT:

GUID: `{7CBB8C1B-6A40-4DE8-9882-D505B76BA09D}` 