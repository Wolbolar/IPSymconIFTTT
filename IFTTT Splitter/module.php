<?

class IFTTTSplitter extends IPSModule
{

    public function Create()
    {
	//Never delete this line!
        parent::Create();
		
		//These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
		$this->RequireParent("{2E91373A-E70B-46D8-99A7-71A499F6783A}", "IFTTT I/O"); //IFTTT I/O	
		$this->RegisterPropertyBoolean("debugifttt", false);
    }

    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();
        $change = false;
		
		$debug = $this->ReadPropertyBoolean("debugifttt");
		if($debug)
		{
			$this->RegisterVariableString("BufferIN", "BufferIN", "", 1);
			$this->RegisterVariableString("CommandOut", "CommandOut", "", 2);
			IPS_SetHidden($this->GetIDForIdent('CommandOut'), true);
			IPS_SetHidden($this->GetIDForIdent('BufferIN'), true);
		}
		
		
		$ParentID = $this->GetParent();
		
			
		// Wenn I/O verbunden ist
		if ($this->HasActiveParent($ParentID))
			{
				//Instanz aktiv
			}
	
    }

		/**
        * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
        * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wiefolgt zur Verfügung gestellt:
        *
        *
        */
	protected $debug = false;
	
	

	################## DUMMYS / WOARKAROUNDS - protected

    protected function GetParent()
    {
        $instance = IPS_GetInstance($this->InstanceID);
        return ($instance['ConnectionID'] > 0) ? $instance['ConnectionID'] : false;
    }

    protected function HasActiveParent($ParentID)
    {
        if ($ParentID > 0)
        {
            $parent = IPS_GetInstance($ParentID);
            if ($parent['InstanceStatus'] == 102)
            {
                $this->SetStatus(102);
                return true;
            }
        }
        $this->SetStatus(203);
        return false;
    }

    private function SetValueBoolean($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueBoolean($id) <> $value)
        {
            SetValueBoolean($id, $value);
            return true;
        }
        return false;
    }

    private function SetValueInteger($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueInteger($id) <> $value)
        {
            SetValueInteger($id, $value);
            return true;
        }
        return false;
    }

    private function SetValueString($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueString($id) <> $value)
        {
            SetValueString($id, $value);
            return true;
        }
        return false;
    }

    protected function SetStatus($InstanceStatus)
    {
        if ($InstanceStatus <> IPS_GetInstance($this->InstanceID)['InstanceStatus'])
            parent::SetStatus($InstanceStatus);
    }

	// Data an Child weitergeben
	public function ReceiveData($JSONString)
	{
	 
		// Empfangene Daten vom IFTTT I/O
		$data = json_decode($JSONString);
		$datasend = $data->Buffer;
		$datasend = json_encode($datasend);
		$debug = $this->ReadPropertyBoolean("debugifttt");
		if($debug)
		{
			IPS_LogMessage("IFTTT Splitter Forward Data", $datasend);
			SetValueString($this->GetIDForIdent("BufferIN"), $datasend);
		}
		
		
		// Hier werden die Daten verarbeitet
		
	 
		// Weiterleitung zu allen Gerät-/Device-Instanzen
		$this->SendDataToChildren(json_encode(Array("DataID" => "{F294A60D-30AF-4452-9A17-44A89EBE6ADE}", "Buffer" => $data->Buffer))); //IFTTT Splitter Interface GUI
	}
	
			
	################## DATAPOINT RECEIVE FROM CHILD
	

	public function ForwardData($JSONString)
	{
	 
		// Empfangene Daten von der Device Instanz
		$data = json_decode($JSONString);
		$datasend = $data->Buffer;
		$datasend = json_encode($datasend);
		$debug = $this->ReadPropertyBoolean("debugifttt");
		if($debug)
		{
			IPS_LogMessage("IFTTT Splitter Forward Data", $datasend);
			SetValueString($this->GetIDForIdent("CommandOut"), $datasend);
		}
		
	 
		// Hier würde man den Buffer im Normalfall verarbeiten
		// z.B. CRC prüfen, in Einzelteile zerlegen
		try
		{
			//
		}
		catch (Exception $ex)
		{
			echo $ex->getMessage();
			echo ' in '.$ex->getFile().' line: '.$ex->getLine().'.';
		}
	 
		// Weiterleiten zur I/O Instanz
		$resultat = $this->SendDataToParent(json_encode(Array("DataID" => "{0259663C-D915-4A86-902B-70D865662E78}", "Buffer" => $data->Buffer))); //TX GUI
	 
		// Weiterverarbeiten und durchreichen
		return $resultat;
	 
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
}

?>