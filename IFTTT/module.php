<?

	class IFTTT extends IPSModule
	{
		
		public function Create()
		{
			//Never delete this line!
			parent::Create();
			$this->ConnectParent("{B88FA10D-CCCA-483A-BAE2-01FEF13E7DD3}"); //IFTTT Splitter
			$this->RegisterPropertyString("iftttmakerkey", "");
			$this->RegisterPropertyString("event", "");
			$this->RegisterPropertyInteger("selection", 0);
			$this->RegisterPropertyInteger("countsendvars", 0);
			$this->RegisterPropertyInteger("countrequestvars", 0);
			for ($i=1; $i<=3; $i++)
			{
				$this->RegisterPropertyInteger("varvalue".$i, 0);
			}
			for ($i=1; $i<=3; $i++)
			{
				$this->RegisterPropertyBoolean("modulinput".$i, false);
			}
			for ($i=1; $i<=3; $i++)
			{
				$this->RegisterPropertyString("value".$i, "");
			}
			for ($i=1; $i<=15; $i++)
			{
				$this->RegisterPropertyInteger("requestvarvalue".$i, 0);
			}
			for ($i=1; $i<=15; $i++)
			{
				$this->RegisterPropertyBoolean("modulrequest".$i, false);
			}
			$this->RegisterPropertyBoolean("iftttreturn", false);
		}
	
		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			
			//IFTTT Request ! Problem Maker kann nicht an IP Connect schicken
			//$idstring = $this->RegisterVariableString("IFTTTRequest", "IFTTT Request", "~String", 2);
			//IPS_SetHidden($idstring, true);
			
			
			
			$this->ValidateConfiguration();	
		}
		
		private function ValidateConfiguration()
		{
			$change = false;
			
			$iftttmakerkey = $this->ReadPropertyString('iftttmakerkey');
			$event = $this->ReadPropertyString('event');
			$selection = $this->ReadPropertyInteger("selection");
			$countsendvars = $this->ReadPropertyInteger("countsendvars");
			$countrequestvars = $this->ReadPropertyInteger("countrequestvars");
			
			if ($selection == 1 || $selection == 3) // Senden , Senden / Empfangen
			{
				$iftttass =  Array(
				Array(0, "Trigger Event",  "Execute", -1)
				);
						
				$this->RegisterProfileIntegerAss("IFTTT.Trigger", "Execute", "", "", 0, 0, 0, 0, $iftttass);
				$this->RegisterVariableInteger("IFTTTTriggerEventButton", "IFTTT Trigger Event Button", "IFTTT.Trigger", 1);
				$this->EnableAction("IFTTTTriggerEventButton");
				
				
				//key prüfen
				if ($iftttmakerkey == "")
					{
						$this->SetStatus(206); // IFTTT Maker Feld darf nicht leer sein
						//$this->SetStatus(104);
					}
				//event prüfen
				if ($event == "")
					{
						$this->SetStatus(209); // Event Feld darf nicht leer sein
						//$this->SetStatus(104);
					}	
				//event prüfen
				$eventcheck = false;
				if ($event !== "")
				{
					if (!preg_match("#^[a-zA-Z0-9_-]+$#", $event))
						{
							$this->SetStatus(207); //event Keine Sonderzeichen oder Leerzeichen
							// String enthält auch andere Zeichen, Großbuchstaben, Sonderzeichen
							//$this->SetStatus(104);
						} 
					else
						{
							$eventcheck = true;
							// String enthält nur Kleinbuchstaben und Zahlen und _
						}
				}
				//maker key prüfen
				$makerkeycheck = false;
				if ($iftttmakerkey !== "")
				{
					if (!preg_match("#^[a-zA-Z0-9_-]+$#", $iftttmakerkey))
						{
							$this->SetStatus(208); //maker key, keine Sonderzeichen oder Leerzeichen
							// String enthält auch andere Zeichen, Großbuchstaben, Sonderzeichen
							//$this->SetStatus(104);
						} 
					else
						{
							$makerkeycheck = true;
							// String enthält nur Kleinbuchstaben und Zahlen und _
						}
				}
				
				if($countsendvars > 3)
					$countsendvars = 3;
				$varvaluecheck = false;
				$valuecheck = false;
				// Trigger Vars
				for ($i=1; $i<=$countsendvars; $i++)
				{
					${"varvalue".$i} = $this->ReadPropertyInteger('varvalue'.$i);
					${"modulinput".$i} = $this->ReadPropertyBoolean('modulinput'.$i);
					${"value".$i} = $this->ReadPropertyString('value'.$i);
					//Valuecheck
					if(${"modulinput".$i} === false && ${"varvalue".$i} === 0)
					{
						$errorid = 220+$i;
						$this->SetStatus($errorid); //IFTTT This: select a value or enter value  in module. , errorid 221 - 235
						break;
					}
					else
					{
						$varvaluecheck = true;
					}	
					//check Modul Value
					if (${"modulinput".$i} === true && ${"value".$i} === "")
					{
						$errorid = 240+$i;
						$this->SetStatus($errorid); // IFTTT This: missing value, enter value in field value, errorid 241 - 255
						break;
					}
					else
					{
						$valuecheck = true;
					}	
				}
				$checkformsend = false;
				if ($makerkeycheck === true && $eventcheck == true && $varvaluecheck === true && $valuecheck === true)
				{
					$checkformsend = true;
				}
				elseif ($makerkeycheck === true && $eventcheck == true && $countsendvars === 0)
				{
					$checkformsend = true;
				}
			}
			
			if ($selection == 2 || $selection == 3) // Empfang , Senden / Empfangen
			{
				$checkformget = false;
				if($countrequestvars > 15)
					$countrequestvars = 15;
				$reqvarvaluecheck = false;
				// Action Vars
				for ($i=1; $i<=$countrequestvars; $i++)
				{
					${"requestvarvalue".$i} = $this->ReadPropertyInteger("requestvarvalue".$i);
					${"modulrequest".$i} = $this->ReadPropertyBoolean("modulrequest".$i);
					$checkformget = false;
					//Valuecheck
					if(${"modulrequest".$i} === false && ${"requestvarvalue".$i} === 0)
					{
						$errorid = 260+$i;
						$this->SetStatus($errorid); //select a value or enter value in module, errorid 261 - 275
						break;
					}
					else
					{
						$checkformget = true;
					}		
				}
			}
			
			if ($selection == 1 && $checkformsend == true) // Senden
			{
				$this->SetStatus(102);
			}
			elseif ($selection == 2 && $checkformget == true) // Empfang
			{
				$this->SetStatus(102);
			}
			elseif ($selection == 3 && $checkformsend == true && $checkformget == true) // Senden / Empfangen
			{
				$this->SetStatus(102);
			}
		}	
		
		protected function SetRequestVariable($key, $value, $type, $i)
		{
			$ident = "IFTTTAktionVar".$i;
			$VarID = @$this->GetIDForIdent($ident);	
			if ($VarID === false)
				{
					$VarID = $this->CreateVarbyType($type, $i, $key);
				}
				
			$this->SetVarbyType($type, $VarID, $key, $value);	
		}
		
		protected function CreateVarbyType($type, $i, $key)
		{
			$ident = "IFTTTAktionVar".$i;
			if ($type == "string")
				{
					$VarID = $this->RegisterVariableString($ident, $key, "~String", $i);
				}
			elseif ($type == "integer")
				{
					$VarID = $this->RegisterVariableInteger($ident, $key, "", $i);
				}
			elseif ($type == "double") //float
				{
					$VarID = $this->RegisterVariableFloat($ident, $key, "", $i);
				}
			elseif ($type == "boolean")
				{
					$VarID = $this->RegisterVariableBoolean($ident, $key, "~Switch", $i);
				}
			elseif ($type == "NULL")
				{
					$VarID = NULL;
				}
				
				return $VarID;
		}
		
		protected function SetVarbyType($type, $VarID, $key, $value)
		{	
			if ($type == "string")
				{
					SetValueString($VarID, $value);
					IPS_SetInfo ($VarID, $key);
				}
			elseif ($type == "integer")
				{
					SetValueInteger($VarID, $value);
					IPS_SetInfo ($VarID, $key);
				}
			elseif ($type == "double") //float
				{
					SetValueFloat($VarID, $value);
					IPS_SetInfo ($VarID, $key);
				}
			elseif ($type == "boolean")
				{
					SetValueBoolean($VarID, $value);
					IPS_SetInfo ($VarID, $key);
				}
			elseif ($type == "NULL")
				{
					// nichts
				}
				
				return $VarID;
		}
		
		protected function WriteValues($valuesjson)
		{
			$this->SendDebug("Values from IFTTT",$valuesjson,0);
			$values = json_decode($valuesjson);
			if(isset($values->EventName))
				$eventname = $values->EventName;
			if(isset($values->Value1))
				{
					$countvalues = 1;
					$value1 = $values->Value1;
				}	
			if(isset($values->Value2))
				{
					$countvalues = 2;
					$value2 = $values->Value2;
				}	
			if(isset($values->Value3))
				{
					$countvalues = 3;
					$value3 = $values->Value3;
				}				
			if(isset($values->OccurredAt))
				$occurredat = $values->OccurredAt;
			$countrequestvars = $this->ReadPropertyInteger('countrequestvars');
			if ( $countvalues == $countrequestvars)
			{
				$i = 1;
				foreach ($values as $key => $value)
					{
						$type = gettype($value);// Typ prüfen
						$requestvarvalue = $this->ReadPropertyInteger('requestvarvalue'.$i);  // Prüfen ob Modulvariable oder Var anlegen
						if (  $requestvarvalue == 0)
							{	
								$this->SetRequestVariable($key, $value, $type, $i);
							}
						else
							{
								$checkvartype = $this->CompareVartype($type, $requestvarvalue);
								if ($checkvartype)
								{
									SetValue($requestvarvalue, $value);
								}
								else
								{
									$this->SendDebug("IFTTT","Es wurde kein Wert für ".$value." gesetzt, Variablentyp stimmt nicht mit Wert überein.",0);
									IPS_LogMessage("IFTTT:", "Es wurde kein Wert für ".$value." gesetzt, Variablentyp stimmt nicht mit Wert überein.");
								}
							}
						$i = $i+1;
					}
			}
			else
			{
				$this->SendDebug("IFTTT","Die Anzahl der Variablen stimmt nicht mit der übermittelten Anzahl an Werten überein!",0);
				$this->SendDebug("IFTTT","Es wurden keine Werte gesetzt.",0);
				$this->SendDebug("IFTTT","Die Anzahl der Variablen stimmt nicht mit der übermittelten Anzahl an Werten überein!",0);
			}
		}
		
		protected function CompareVartype($type, $requestvarvalue)
		{
				$varinfo = (IPS_GetVariable($requestvarvalue));
				$vartype =  $varinfo["VariableType"];
				if ($vartype == 0) //bool
				{
					$ipsvartype = "boolean";
				}
				elseif ($vartype == 1) //integer
				{
					$ipsvartype = "integer";
				}
				elseif ($vartype == 2) //float
				{
					$ipsvartype = "double";
				}
				elseif ($vartype == 3) //string
				{
					$ipsvartype = "string";
				}
				
				if ($type ===  $ipsvartype)
				{
					return true;
				}
				else
				{
					return false;
				}
		}
		
		protected function SetupDataScript()
		{
			//prüfen ob Script existent
			$SkriptID = @$this->GetIDForIdent("IFTTTGetData");
				
			if ($SkriptID === false)
				{
					$SkriptID = $this->RegisterScript("IFTTTGetData", "IFTTT Get Data", $this->CreateDataScript(), 3);
					IPS_SetHidden($SkriptID, true);
					$this->SetIFTTTDataEvent($SkriptID);
				}
			else
				{
					//echo "Die Skript-ID lautet: ". $SkriptID;
				}	
		}
		
		protected function SetIFTTTDataEvent(integer $SkriptID)
		{
			//prüfen ob Event existent
			$ParentID = $SkriptID;

			$EreignisID = @($this->GetIDForIdent('EventIFTTTGetData'));
			if ($EreignisID === false)
				{
					$EreignisID = IPS_CreateEvent (0);
					IPS_SetName($EreignisID, "Event IFTTT Get Data");
					IPS_SetIdent ($EreignisID, "EventIFTTTGetData");
					IPS_SetEventTrigger($EreignisID, 0,  $this->GetIDForIdent('IFTTTRequest'));   //bei Variablenaktualisierung
					IPS_SetParent($EreignisID, $ParentID);
					IPS_SetEventActive($EreignisID, true);             //Ereignis aktivieren	
				}
				
			else
				{
				//echo "Die Ereignis-ID lautet: ". $EreignisID;	
				}
		}
		
		protected function CreateDataScript()
		{
			$Script = '<?
 $iftttdatajson = GetValueString('.$this->GetIDForIdent("IFTTTRequest").');
 $iftttdata = json_decode($iftttdatajson); // Standard Objekt
 //$iftttdata = json_decode($iftttdatajson, true); // Array
 
 //Standard Objekt oder Array auslesen
 foreach ($iftttdata as $key=>$data)
 {
 	 echo "Key: ".$key." => Value: ".$data."\n";
	 //add command here
 }
 ?>';
			return $Script;
		}
		
	
		
		/**
		* This function will be available automatically after the module is imported with the module control.
		* Using the custom prefix this function will be callable from PHP and JSON-RPC 
		*
		*/
		
		protected function ConvertVarString($objid)
		{
			$vartype = IPS_GetVariable($objid)['VariableType'];
			if ($vartype === 0)//Boolean
			{
			$value = GetValueBoolean($objid);// Boolean umwandeln in String
			$value = ($value) ? 'true' : 'false';
			}
			elseif($vartype === 1)//Integer
			{
				$value = strval(GetValueInteger($objid));   // Integer Umwandeln in String
			}
			elseif($vartype === 2)//Float
			{
				$value = strval(GetValueFloat($objid)); //Float umwandeln in String
			}
			elseif($vartype === 3)//String
			{
				$value = GetValue($objid);  //string ok
			}
			return $value;
			
		}

		public function TriggerEvent()
		{
			$iftttmakerkey = $this->ReadPropertyString('iftttmakerkey');
			$event = $this->ReadPropertyString('event');
			
			$countsendvars = $this->ReadPropertyInteger("countsendvars");
			if ($countsendvars > 0)
			{
				// Trigger Vars
				for ($i=1; $i<=$countsendvars; $i++)
				{
					${"modulinput".$i} = $this->ReadPropertyBoolean('modulinput'.$i);
					if (${"modulinput".$i})
					{
						${"value".$i} = $this->ReadPropertyString('value'.$i);
						${"key".$i} = "value".$i;
					}
					else 
					{
						${"objidvalue".$i} = $this->ReadPropertyInteger('varvalue'.$i);
						${"value".$i} = GetValue(${"objidvalue".$i});
						${"key".$i} = IPS_GetName(${"objidvalue".$i});
						//${"value".$i} = $this->ConvertVarString(${"objidvalue".$i});
					}
				}
				
				$values = array();
				for ($i=1; $i<=$countsendvars; $i++)
				{
					$values["value".$i] = ${"value".$i};
				}
				$count = count($values);
				if ($count == 1)
				{
					$values["value2"] = NULL;
					$values["value3"] = NULL;
				}	
				elseif ($count == 2)
				{
					$values["value3"] = NULL;
				}
			}
			else
			{
				$values = array("value1" => NULL, "value2" => NULL, "value3" => NULL);
			}	
			
			
			$values_string = json_encode($values);
			$iftttreturn = $this->SendEventTriggerVar1to3($iftttmakerkey, $event, $values_string);
			return $iftttreturn;
		}
		
		protected function SendEventTriggerVar1to3(string $iftttmakerkey, string $event, string $values_string)
		{
			
			$values = json_decode($values_string, true);
			$payload = array("iftttmakerkey" => $iftttmakerkey, "event" => $event, "values" => $values);
						
			//an Splitter schicken
			$result = $this->SendDataToParent(json_encode(Array("DataID" => "{78A293F6-50ED-4250-AF5A-05F6F2C563EB}", "Buffer" => $payload))); //IFTTT Interface GUI
			return $result;
		}
		
		public function SendEventTrigger(string $iftttmakerkey, string $event, string $value1, string $value2, string $value3)
		{
			
			$values = array("value1" => $value1, "value2" => $value2, "value3" => $value3);
			$payload = array("iftttmakerkey" => $iftttmakerkey, "event" => $event, "values" => $values);
						
			//an Splitter schicken
			$result = $this->SendDataToParent(json_encode(Array("DataID" => "{78A293F6-50ED-4250-AF5A-05F6F2C563EB}", "Buffer" => $payload))); //IFTTT Interface GUI
			return $result;
		}
		
		public function ReceiveData($JSONString)
		{
			$data = json_decode($JSONString);
			$objectid = $data->Buffer->objectid;
			$values = $data->Buffer->values;
			$valuesjson = json_encode($values);
			if (($this->InstanceID) == $objectid)
			{
				//Parse and write values to our variables
				$this->WriteValues($valuesjson);
			}
		}
		
		public function RequestAction($Ident, $Value)
		{
			switch($Ident) {
				case "IFTTTTriggerEventButton":
					SetValue($this->GetIDForIdent("IFTTTTriggerEventButton"), $Value);
					$iftttreturn = $this->TriggerEvent();
					$iftttreturnvis = $this->ReadPropertyBoolean('iftttreturn');
					if ($iftttreturnvis === true)
					{
						$InstanzenListe = IPS_GetInstanceListByModuleID("{3565B1F2-8F7B-4311-A4B6-1BF1D868F39E}");
						foreach ($InstanzenListe as $InstanzID)
						{
							WFC_SendNotification($InstanzID, 'IFTTT', $iftttreturn, 'Execute', 4);
						}		
					}
					
					break;	
				default:
					throw new Exception("Invalid ident");
			}
		}
		
		protected function GetUsernamePassword()
		{
			$objid = $this->GetIOObjectID();
			$username = IPS_GetProperty($objid, "username");
			$password = IPS_GetProperty($objid, "password");
			$webhooksettings = array ("username" => $username, "password" => $password);
			return $webhooksettings;		
		}


		protected function GetIOObjectID()
		{
			$InstanzenListe = IPS_GetInstanceListByModuleID("{2E91373A-E70B-46D8-99A7-71A499F6783A}");
			foreach ($InstanzenListe as $InstanzID)
				{
					return $InstanzID;
				}
		}
				
		//Configuration Form
		public function GetConfigurationForm()
		{
			$selection = $this->ReadPropertyInteger("selection");
			$countsendvars = $this->ReadPropertyInteger("countsendvars");
			$countrequestvars = $this->ReadPropertyInteger("countrequestvars");
			$formhead = $this->FormHead();
			$formstatus = $this->FormStatus();
			$formsend = $this->FormSend($countsendvars);
			$formget = $this->FormGet($countrequestvars);
			/*
			if ($selection == 2)
			{
				$formget = substr($this->FormGet($countrequestvars), 0, -1); // letztes Komma entfernen
			}
			else
			{
				$formget = $this->FormGet($countrequestvars);
			}
			*/
			$formreturn = '{ "type": "Label", "label": "Return Message from IFTTT" },
				{
					"name": "iftttreturn",
					"type": "CheckBox",
					"caption": "IFTTT Return"
				},';
			$formelementsend = '{ "type": "Label", "label": "__________________________________________________________________________________________________" }';
		
			if($selection == 0)// keine Auswahl
			{
				return	'{ '.$formhead.'],'.$formstatus.' }';
			}
			
			elseif ($selection == 1) // Senden 
			{
				$formactions = $this->FormActions(1, $countrequestvars);
				return	'{ '.$formhead.','.$formsend.$formreturn.$formelementsend.'],'.$formactions.','.$formstatus.' }';
			}
			
			elseif ($selection == 2) // Empfangen 
			{
				$formactions = $this->FormActions(2, $countrequestvars);
				return	'{ '.$formhead.','.$formget.$formelementsend.'],'.$formactions.','.$formstatus.' }';
			}
			
			elseif ($selection == 3) // Senden / Empfangen
			{
				$formactions = $this->FormActions(3, $countrequestvars);
				return	'{ '.$formhead.','.$formsend.$formreturn.$formget.$formelementsend.'],'.$formactions.','.$formstatus.' }';
			}
		
		}
		
		protected function FormSend($countsendvars)
		{
			$form = '{ "type": "Label", "label": "IFTTT This____________________________________________________________________________________________" },
			{ "type": "Label", "label": "IFTTT maker key (look in IFTTT maker channel)" },
		{ "name": "iftttmakerkey", "type": "ValidationTextBox", "caption": "IFTTT maker key" },
		{ "type": "Label", "label": "please choose an event name (no special characters or blank)" },
		{ "name": "event", "type": "ValidationTextBox", "caption": "event name" },
		{ "type": "Label", "label": "number of variables for IFTTT This (max 3)" },
		{ "type": "NumberSpinner", "name": "countsendvars", "caption": "number of variables" },'
		.$this->FormSendVars($countsendvars);
			return $form;
		}
		
		protected function FormSendVars($countsendvars)
		{
			if ($countsendvars > 0)
			{
				if($countsendvars > 3)
				$countsendvars = 3;
				$form = '{ "type": "Label", "label": "variables with values for IFTTT" },';
				for ($i=1; $i<=$countsendvars; $i++)
				{
					$form .= '{ "type": "SelectVariable", "name": "varvalue'.$i.'", "caption": "value '.$i.'" },';
				}
				$form .= '{ "type": "Label", "label": "alternative leave variable empty und click check mark" },';
				for ($i=1; $i<=$countsendvars; $i++)
				{
					$form .= '{
						"name": "modulinput'.$i.'",
						"type": "CheckBox",
						"caption": "use modul value '.$i.'"
					},	
			{ "name": "value'.$i.'", "type": "ValidationTextBox", "caption": "value '.$i.'" },';
				}
			}
			else
			{
				$form = "";
			}
			
			return $form;
		}
		
		protected function FormGet($countrequestvars)
		{			 
			$form = '{ "type": "Label", "label": "IFTTT That_______________________________________________________________________________________________" },
			{ "type": "Label", "label": "variables with values for IFTTT That" },
			{ "type": "Label", "label": "number of variables for a IFTTT That (max 15)" },
			{ "type": "NumberSpinner", "name": "countrequestvars", "caption": "number of variables" },'
			.$this->FormGetVars($countrequestvars);
			return $form;
		}
		
		protected function FormGetVars($countrequestvars)
		{
			if ($countrequestvars > 0)
			{
				if($countrequestvars > 15)
				$countrequestvars = 15;
				$form = '';
				for ($i=1; $i<=$countrequestvars; $i++)
				{
					$form .= '{ "type": "SelectVariable", "name": "requestvarvalue'.$i.'", "caption": "value '.$i.'" },';
				}
				$form .= '{ "type": "Label", "label": "alternative leave variable empty und click check mark for creating a new variable" },';
				for ($i=1; $i<=$countrequestvars; $i++)
				{
					$form .= '{
						"name": "modulrequest'.$i.'",
						"type": "CheckBox",
						"caption": "module create variable for value '.$i.'"
					},';
				}
			}
			else
			{
				$form = "";
			}
			
			
			
			return $form;
		}
		
		protected function FormHead()
		{
			$form = '"elements":
	[
		{ "type": "Label", "label": "Connection from IP-Symcon to IFTTT" },
		{ "type": "Label", "label": "https://ifttt.com" },
		{ "type": "Label", "label": "communication type with IFTTT: send, receive, send/receive" },
		{ "type": "Select", "name": "selection", "caption": "communication",
    "options": [
        { "label": "Please select", "value": 0 },
        { "label": "Send", "value": 1 },
        { "label": "Receive", "value": 2 },
        { "label": "Send/Receive", "value": 3 }
    ]
}';
			// End ]
			return $form;
		}
		
		protected function FormActions($type, $countrequestvars)
		{
			if ($type == 1) // Senden
			{	
				$event = $this->ReadPropertyString('event');
				$form = '"actions": [{ "type": "Label", "label": "IFTTT configuration:" },
				{ "type": "Label", "label": "IFTTT This configuration:" },
				{ "type": "Label", "label": " - Create a Recipe" },
				{ "type": "Label", "label": " - push this" },
				{ "type": "Label", "label": " - choose Maker Channel" },
				{ "type": "Label", "label": " - Receive a webrequest" },
				{ "type": "Label", "label": " - Event Name: '.$event.'" },
				{ "type": "Label", "label": " - Create Trigger" },
				{ "type": "Label", "label": " - continue with That of your choice" },
				{ "type": "Label", "label": "______________________________________________________________________________________________________" },
				{ "type": "Label", "label": "Trigger IFTTT Event" },
				{ "type": "Button", "label": "Trigger Event", "onClick": "IFTTT_TriggerEvent($id);" } ]';
				return  $form;
			}
			elseif ($type == 2) // Empfangen
			{
				$form = '"actions": [ { "type": "Label", "label": "IFTTT That configuration: " },
				{ "type": "Label", "label": " - Method:" },
				{ "type": "Label", "label": "     POST " },
				{ "type": "Label", "label": " - URI:" },
				{ "type": "Label", "label": "     '.$this->GetIPSConnect().'/hook/flow" },
				{ "type": "Label", "label": " - Header:" },
				{ "type": "Label", "label": "     {" },
				{ "type": "Label", "label": "      \"charset\":\"utf-8\"," },
				{ "type": "Label", "label": "      \"Content-Type\":\"application/json\"," },
				{ "type": "Label", "label": "     }" },
				{ "type": "Label", "label": " - Body: (example)" },
				{ "type": "Label", "label": "     {" },
				{ "type": "Label", "label": "     {\"objectid\":'.$this->InstanceID.',\"values\":{\"keyvalue1\":\"value1string\",\"keyvalue2\":value2float,\"keyvalue3\":value3int,\"keyvalue4\":value4bool}}"},
				{ "type": "Label", "label": "     }" },
				{ "type": "Label", "label": "     example values begin and end with curly brackets" },
				{ "type": "Label", "label": "     put keys always inside \"\", string value inside \"\", boolean, integer and float values without \"\"" },	
				{ "type": "Label", "label": "     show advanced options" },
				{ "type": "Label", "label": "     username (standard ipsymcon), set username in Flow IO" },
				{ "type": "Label", "label": "     password is set, for individual password set password in Flow IO" },
				{ "type": "Label", "label": " - Authentification:" },
				{ "type": "Label", "label": "     {" },
				{ "type": "Label", "label": "      \"type\":\"Basic\"," },
				{ "type": "Label", "label": "      \"username\":\"'.$this->IFTTTConfigAuthUser().'\"," },
				{ "type": "Label", "label": "      \"password\":\"'.$this->IFTTTConfigAuthPassword().'\"," },
				{ "type": "Label", "label": "     }" } ]';
				return  $form;
			}
			
			elseif ($type == 3) // Senden / Empfangen
			{
				$event = $this->ReadPropertyString('event');
				$form = '"actions": [ { "type": "Label", "label": "IFTTT configuration:" },
				{ "type": "Label", "label": "IFTTT This configuration:" },
				{ "type": "Label", "label": " - Create a Recipe" },
				{ "type": "Label", "label": " - push this" },
				{ "type": "Label", "label": " - choose Maker Channel" },
				{ "type": "Label", "label": " - Receive a webrequest" },
				{ "type": "Label", "label": " - Event Name: '.$event.'" },
				{ "type": "Label", "label": " - Create Trigger" },
				{ "type": "Label", "label": " - continue with That of your choice" },
				{ "type": "Label", "label": "______________________________________________________________________________________________________" },
				{ "type": "Label", "label": "IFTTT That configuration: " },
				{ "type": "Label", "label": " - Method:" },
				{ "type": "Label", "label": "     POST " },
				{ "type": "Label", "label": " - URI:" },
				{ "type": "Label", "label": "     '.$this->GetIPSConnect().'/hook/flow" },
				{ "type": "Label", "label": " - Header:" },
				{ "type": "Label", "label": "     {" },
				{ "type": "Label", "label": "      \"charset\":\"utf-8\"," },
				{ "type": "Label", "label": "      \"Content-Type\":\"application/json\"" },
				{ "type": "Label", "label": "     }" },
				{ "type": "Label", "label": " - Body: (example)" },
				{ "type": "Label", "label": "     {" },
				{ "type": "Label", "label": "       \"objectid\":'.$this->InstanceID.',\"values\":{\"keyvalue1\":\"value1string\",\"keyvalue2\":value2float,\"keyvalue3\":value3int,\"keyvalue4\":value4bool}"},
				{ "type": "Label", "label": "     }" },
				{ "type": "Label", "label": "     example values begin and end with curly brackets" },
				{ "type": "Label", "label": "     put keys always inside \"\", string value inside \"\", boolean, integer and float values without \"\"" },
				{ "type": "Label", "label": "     show advanced options" },
				{ "type": "Label", "label": "     username (standard ipsymcon), set username in Flow IO" },
				{ "type": "Label", "label": "     password is set, for individual password set password in Flow IO" },
				{ "type": "Label", "label": " - Authentification:" },
				{ "type": "Label", "label": "     {" },
				{ "type": "Label", "label": "      \"type\":\"Basic\"," },
				{ "type": "Label", "label": "      \"username\":\"'.$this->IFTTTConfigAuthUser().'\"," },
				{ "type": "Label", "label": "      \"password\":\"'.$this->IFTTTConfigAuthPassword().'\"" },
				{ "type": "Label", "label": "     }" },
				{ "type": "Label", "label": "______________________________________________________________________________________________________" },
				{ "type": "Label", "label": "Trigger IFTTT Event" },
				{ "type": "Button", "label": "Trigger Event", "onClick": "IFTTT_TriggerEvent($id);" } ]';
				return  $form;
			}
		}
			
		protected function IFTTTConfigRequest($countrequestvars)
		{
			$webhooksettings =	GetUsernamePassword();
			$username = $webhooksettings["username"];
			$password = $webhooksettings["password"];
			if ($countrequestvars == 0)
			{
				$form =  '{ "type": "Label", "label": "         values  please select at least one value" }';
			}
			else
			{	
				//{"actions":[ {"type":"Label","label":"values     {\"value1\":\"value2\",\"value3\":\"value4\"}"} ]}
				$form =  '{ "type": "Label", "label": "         values              {';
				for ($i=1; $i<=4; $i++)
				{
					$form .= "\\\"keyvalue".$i."\\\":\\\"value".$i."\\\",";
				}
				$form = substr($form, 0, -1);
				$form .= ' }"},';
			}
			return $form;
		}
		
		protected function IFTTTConfigAuthUser()
		{
			$webhooksettings =	$this->GetUsernamePassword();
			$username = $webhooksettings["username"];
			$password = $webhooksettings["password"];
			return $username;
		}
		
		protected function IFTTTConfigAuthPassword()
		{
			$webhooksettings =	$this->GetUsernamePassword();
			$password = $webhooksettings["password"];
			return $password;
		}
		
		protected function FormStatus()
		{
			$form = '"status":
            [
                {
                    "code": 101,
                    "icon": "inactive",
                    "caption": "Creating instance."
                },
				{
                    "code": 102,
                    "icon": "active",
                    "caption": "IFTTT created."
                },
				'.$this->FormStatusErrorSelectorEnterThat().'
                {
                    "code": 104,
                    "icon": "inactive",
                    "caption": "interface closed."
                },
				{
                    "code": 201,
                    "icon": "inactive",
                    "caption": "select number of values in module."
                },
				'.$this->FormStatusErrorSelectorEnter().'
				{
                    "code": 206,
                    "icon": "error",
                    "caption": "IFTTT maker field must not be empty."
                },
				'.$this->FormStatusErrorMissingValueinField().'
				{
                    "code": 207,
                    "icon": "error",
                    "caption": "event not valid."
                },
				{
                    "code": 208,
                    "icon": "error",
                    "caption": "IFTTT maker key not valid."
                },
				{
                    "code": 209,
                    "icon": "error",
                    "caption": "Event field must not be empty."
                }
			
            ]';
			return $form;
		}

		protected function FormStatusErrorSelectorEnter() // errorid 221 - 223
		{
			$form = "";
			for ($i=1; $i<=3; $i++)
			{
				$errorid = 220+$i;
				$form .= '{
                    "code": '.$errorid.',
                    "icon": "error",
                    "caption": "IFTTT IF: select a value '.$i.' or enter value '.$i.' in module."
                },'; 
			}
			return $form;
		}
		
		protected function FormStatusErrorMissingValueinField() // errorid 241 - 243
		{
			$form = "";
			for ($i=1; $i<=3; $i++)
			{
				$errorid = 240+$i;
				$form .= '{
                    "code": '.$errorid.',
                    "icon": "error",
                    "caption": "IFTTT IF: missing value, enter value in field value '.$i.'"
                },'; 
			}
			return $form;
		}
		
		protected function FormStatusErrorSelectorEnterThat() // errorid 261 - 275
		{
			$form = "";
			for ($i=1; $i<=15; $i++)
			{
				$errorid = 260+$i;
				$form .= '{
                    "code": '.$errorid.',
                    "icon": "error",
                    "caption": "IFTTT That: select a value '.$i.' or enter value '.$i.' in module."
                },'; 
			}
			return $form;
		}
		
	
		// IP-Symcon Connect auslesen
		protected function GetIPSConnect()
		{
			$InstanzenListe = IPS_GetInstanceListByModuleID("{9486D575-BE8C-4ED8-B5B5-20930E26DE6F}");
			foreach ($InstanzenListe as $InstanzID) {
				$ConnectControl = $InstanzID;
			} 
			$connectinfo = CC_GetUrl($ConnectControl);
			if ($connectinfo == false || $connectinfo == "")
				$connectinfo = 'https://<IP-Symcon Connect>.ipmagic.de';
			$connectinfo = "https://123456789abcdefgh.ipmagic.de";
			return $connectinfo;
		}
		
		//Profile
		protected function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits)
		{
			
			if(!IPS_VariableProfileExists($Name)) {
				IPS_CreateVariableProfile($Name, 1);
			} else {
				$profile = IPS_GetVariableProfile($Name);
				if($profile['ProfileType'] != 1)
				throw new Exception("Variable profile type does not match for profile ".$Name);
			}
			
			IPS_SetVariableProfileIcon($Name, $Icon);
			IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
			IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
			IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize); // string $ProfilName, float $Minimalwert, float $Maximalwert, float $Schrittweite
			
		}
		
		protected function RegisterProfileIntegerAss($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Associations)
		{
			if ( sizeof($Associations) === 0 ){
				$MinValue = 0;
				$MaxValue = 0;
			} 
			/*
			else {
				//undefiened offset
				$MinValue = $Associations[0][0];
				$MaxValue = $Associations[sizeof($Associations)-1][0];
			}
			*/
			$this->RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits);
			
			//boolean IPS_SetVariableProfileAssociation ( string $ProfilName, float $Wert, string $Name, string $Icon, integer $Farbe )
			foreach($Associations as $Association) {
				IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
			}
			
		}
	
	}

?>
