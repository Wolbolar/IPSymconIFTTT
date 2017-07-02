<?

class IFTTTIO extends IPSModule
{

    public function Create()
    {
	//Never delete this line!
        parent::Create();
		
		//These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
		
		$this->RegisterPropertyString("username", "ipsymcon");
		$this->RegisterPropertyString("password", "user@h0me");
    }

    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();
        $change = false;
				
		$this->SetIFTTTInterface();
		$this->SetStatus(102);
	}	

	

			
################## Datapoints
 
	
		
			
	################## DATAPOINT RECEIVE FROM CHILD

    // Type String, Declaration can be used when PHP 7 is available
    //public function ForwardData(string $JSONString)
	public function ForwardData($JSONString)
	{
		// Empfangene Daten von der Splitter Instanz
		$data = json_decode($JSONString);
		
	 
		// Hier würde man den Buffer im Normalfall verarbeiten
		// z.B. CRC prüfen, in Einzelteile zerlegen
		try
		{
			// Absenden an IFTTT
		
			//IPS_LogMessage("Forward Data to IFTTT", utf8_decode($data->Buffer));
			
			//aufarbeiten
			$command = $data->Buffer;
			$result = $this->SendCommand ($command);
		}
		catch (Exception $ex)
		{
			echo $ex->getMessage();
			echo ' in '.$ex->getFile().' line: '.$ex->getLine().'.';
		}
	 
		return $result;
	}
	
	protected function SendJSON ($data)
	{
		// Weiterleitung zu allen Gerät-/Device-Instanzen
		$this->SendDataToChildren(json_encode(Array("DataID" => "{BC2FAD9D-C92E-4CFA-ADA5-79A56DA5D2F7}", "Buffer" => $data))); //IFTTT I/O RX GUI
	}
	
	public function SendEventTrigger(string $iftttmakerkey, string $event, string $value1, string $value2, string $value3)
		{
			
			$data = array("value1" => $value1, "value2" => $value2, "value3" => $value3);
			$data_string = json_encode($data);
						
			$URL = "https://maker.ifttt.com/trigger/".$event."/with/key/".$iftttmakerkey;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$URL);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout after 5 seconds
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string))
			);
			$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
			$result=curl_exec ($ch);
			$this->SendDebug("ResponseIFTTT",$result,0);
			curl_close ($ch);
			return $result;
		}
		
	protected function SendCommand ($command)
	{
				
		//Semaphore setzen
        if ($this->lock("EventSend"))
        {
        // Daten senden
	        try
	        {
				$iftttmakerkey = $command->iftttmakerkey;
				$event = $command->event;
				$values = $command->values;
				$value1 = $values->value1;
				$value2 = $values->value2;
				$value3 = $values->value3;
				$this->SendDebug("IFTTT I/O:","Trigger IFTTT Event ".utf8_decode($event),0);
							
				$result = $this->SendEventTrigger($iftttmakerkey, $event, $value1, $value2, $value3);
				$iftttpayload = array("value1" => $value1, "value2" => $value2, "value3" => $value3);
				$data_string = json_encode($iftttpayload);
				$this->SendDebug("IFTTT I/O:",utf8_decode($data_string)." gesendet.",0);
	        }
	        catch (Exception $exc)
	        {
	            // Senden fehlgeschlagen
	            $this->unlock("EventSend");
	            throw new Exception($exc);
	        }
        $this->unlock("EventSend");
        }
        else
        {
			echo "Can not send to parent \n";
			$result = false;
			$this->unlock("EventSend");
			//throw new Exception("Can not send to parent",E_USER_NOTICE);
		  }
		
		return $result;
	
	}
	
	protected function SetIFTTTInterface()
		{
			$ipsversion = $this->GetIPSVersion();
			if($ipsversion == 0)
				{
					//prüfen ob Script existent
					$SkriptID = @IPS_GetObjectIDByIdent("IFTTTIPSInterface", $this->InstanceID);
					if ($SkriptID === false)
						{
							$ID = $this->RegisterScript("IFTTTIPSInterface", "IFTTT IPS Interface", $this->CreateWebHookScript(), 4);
							IPS_SetHidden($ID, true);
							$this->RegisterHookOLD('/hook/IFTTT', $ID);
						}
					else
						{
							//echo "Die Skript-ID lautet: ". $SkriptID;
						}
				}
			else
				{
					$SkriptID = @IPS_GetObjectIDByIdent("IFTTTIPSInterface", $this->InstanceID);
					if ($SkriptID > 0)
					{
						$this->UnregisterHook("/hook/IFTTT");
						$this->UnregisterScript("IFTTTIPSInterface");
					}
					$this->RegisterHook("/hook/IFTTT");
				}
		}
	
	private function RegisterHookOLD($WebHook, $TargetID)
		{
			$ids = IPS_GetInstanceListByModuleID("{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}");
			if (sizeof($ids) > 0)
			{
				$hooks = json_decode(IPS_GetProperty($ids[0], "Hooks"), true);
				$found = false;
				foreach ($hooks as $index => $hook)
				{
					if ($hook['Hook'] == $WebHook)
					{
						if ($hook['TargetID'] == $TargetID)
							return;
						$hooks[$index]['TargetID'] = $TargetID;
						$found = true;
					}
				}
				if (!$found)
				{
					$hooks[] = Array("Hook" => $WebHook, "TargetID" => $TargetID);
				}
				IPS_SetProperty($ids[0], "Hooks", json_encode($hooks));
				IPS_ApplyChanges($ids[0]);
			}
		}
		
		private function RegisterHook($WebHook)
		{
  			$ids = IPS_GetInstanceListByModuleID("{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}");
  			if(sizeof($ids) > 0)
				{
  				$hooks = json_decode(IPS_GetProperty($ids[0], "Hooks"), true);
  				$found = false;
  				foreach($hooks as $index => $hook)
					{
					if($hook['Hook'] == $WebHook)
						{
						if($hook['TargetID'] == $this->InstanceID)
  							return;
						$hooks[$index]['TargetID'] = $this->InstanceID;
  						$found = true;
						}
					}
  				if(!$found)
					{
 					$hooks[] = Array("Hook" => $WebHook, "TargetID" => $this->InstanceID);
					}
  				IPS_SetProperty($ids[0], "Hooks", json_encode($hooks));
  				IPS_ApplyChanges($ids[0]);
				}
  		}
		
	/**
     * Löscht einen WebHook, wenn vorhanden.
     *
     * @access private
     * @param string $WebHook URI des WebHook.
     */
    protected function UnregisterHook($WebHook)
    {
        $ids = IPS_GetInstanceListByModuleID("{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}");
        if (sizeof($ids) > 0)
        {
            $hooks = json_decode(IPS_GetProperty($ids[0], "Hooks"), true);
            $found = false;
            foreach ($hooks as $index => $hook)
            {
                if ($hook['Hook'] == $WebHook)
                {
                    $found = $index;
                    break;
                }
            }
            if ($found !== false)
            {
                array_splice($hooks, $index, 1);
                IPS_SetProperty($ids[0], "Hooks", json_encode($hooks));
                IPS_ApplyChanges($ids[0]);
            }
        }
    }  
	
	/**
     * Löscht eine Script, sofern vorhanden.
     *
     * @access private
     * @param int $Ident Ident der Variable.
     */
    protected function UnregisterScript($Ident)
    {
        $sid = @IPS_GetObjectIDByIdent($Ident, $this->InstanceID);
        if ($sid === false)
            return;
        if (!IPS_ScriptExists($sid))
            return; //bail out
        IPS_DeleteScript($sid, true);
    } 
		
	private function CreateWebHookScript()
		{
        $Script = '<?
//Do not delete or modify.
IFTTTIO_ProcessHookDataOLD('.$this->InstanceID.');		
?>';	
		return $Script;
		}	
		
		public function ProcessHookDataOLD()
		{
			$webhookusername = $this->ReadPropertyString('username');
			$webhookpassword = $this->ReadPropertyString('password');
			
			//$this->SendDebug("SERVER ARRAY",print_r($_SERVER,true),0);
						
			//workaround for bug
			if(!isset($_IPS))
				global $_IPS;
			if($_IPS['SENDER'] == "Execute")
				{
				echo "This script cannot be used this way.";
				return;
				} 
			
			//workaround for bug
			if(!isset($_IPS))
				global $_IPS;
			if($_IPS['SENDER'] == "Execute")
				{
				echo "This script cannot be used this way.";
				return;
				} 

			 # Capture JSON content
			$iftttjson = file_get_contents('php://input');
			$data = json_decode($iftttjson);
			$username = $data->username;
			$password = $data->password;
			
			if(($username != $webhookusername) || ($password != $webhookpassword))
				{
				header('HTTP/1.0 401 Unauthorized');
				$this->SendDebug("IFTTT I/O:","Access denied",0);
				echo "Authorization required";
				return;
				}
			$objectid = $data->objectid;
			$values = $data->values;
			$this->SendDebug("IFTTT I/O:",utf8_decode($iftttjson)." empfangen.",0);
			$this->SendJSON($data);
		}
	
	/**
 	* This function will be called by the hook control. Visibility should be protected!
  	*/
		
	protected function ProcessHookData()
	{
		$webhookusername = $this->ReadPropertyString('username');
		$webhookpassword = $this->ReadPropertyString('password');
		
		//$this->SendDebug("SERVER ARRAY",print_r($_SERVER,true),0);
				
		//workaround for bug
		if(!isset($_IPS))
			global $_IPS;
		if($_IPS['SENDER'] == "Execute")
			{
			echo "This script cannot be used this way.";
			return;
			} 
			
		//workaround for bug
		if(!isset($_IPS))
			global $_IPS;
		if($_IPS['SENDER'] == "Execute")
			{
			echo "This script cannot be used this way.";
			return;
			} 

		# Capture JSON content
		$iftttjson = file_get_contents('php://input');
		$data = json_decode($iftttjson);
		$username = $data->username;
		$password = $data->password;
			
		if(($username != $webhookusername) || ($password != $webhookpassword))
			{
			header('HTTP/1.0 401 Unauthorized');
			$this->SendDebug("IFTTT I/O:","Access denied",0);
			echo "Authorization required";
			return;
			}
		$objectid = $data->objectid;
		$values = $data->values;
		$this->SendDebug("IFTTT I/O:",utf8_decode($iftttjson)." empfangen.",0);
		$this->SendJSON($data);	
	}
	
	################## SEMAPHOREN Helper  - private

    private function lock($ident)
    {
        for ($i = 0; $i < 3000; $i++)
        {
            if (IPS_SemaphoreEnter("IFTTT_" . (string) $this->InstanceID . (string) $ident, 1))
            {
                return true;
            }
            else
            {
                IPS_Sleep(mt_rand(1, 5));
            }
        }
        return false;
    }

    private function unlock($ident)
    {
          IPS_SemaphoreLeave("IFTTT_" . (string) $this->InstanceID . (string) $ident);
    }
	
	protected function GetIPSVersion ()
		{
			$ipsversion = IPS_GetKernelVersion ( );
			$ipsversion = explode( ".", $ipsversion);
			$ipsmajor = intval($ipsversion[0]);
			$ipsminor = intval($ipsversion[1]);
			if($ipsminor < 10) // 4.0
			{
				$ipsversion = 0;
			}
			elseif ($ipsminor >= 10 && $ipsminor < 20) // 4.1
			{
				$ipsversion = 1;
			}
			else   // 4.2
			{
				$ipsversion = 2;
			}
			return $ipsversion;
		}

}

?>