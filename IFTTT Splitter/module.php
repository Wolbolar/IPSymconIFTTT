<?php

class IFTTTSplitter extends IPSModule
{

    public function Create()
    {
	//Never delete this line!
        parent::Create();
		
		//These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
		$this->RequireParent("{2E91373A-E70B-46D8-99A7-71A499F6783A}"); //IFTTT I/O	
    }

    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();

		// Wenn I/O verbunden ist
		if ($this->HasActiveParent())
			{
				$this->SetStatus(IS_ACTIVE);
			}
		else{
			$this->SetStatus(203);
		}
    }

		/**
        * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
        * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wiefolgt zur Verfügung gestellt:
        *
        *
        */

	// Data an Child weitergeben
    // Type String, Declaration can be used when PHP 7 is available
    //public function ReceiveData(string $JSONString)
	public function ReceiveData($JSONString)
	{
	 
		// Empfangene Daten vom IFTTT I/O
		$data = json_decode($JSONString);
		$dataio = json_encode($data->Buffer);
		$this->SendDebug("ReceiveData IFTTT Splitter:",$dataio,0);
			
		// Hier werden die Daten verarbeitet
		
	 
		// Weiterleitung zu allen Gerät-/Device-Instanzen
		$this->SendDataToChildren(json_encode(Array("DataID" => "{F294A60D-30AF-4452-9A17-44A89EBE6ADE}", "Buffer" => $data->Buffer))); //IFTTT Splitter Interface GUI
	}
	
			
	################## DATAPOINT RECEIVE FROM CHILD


    // Type String, Declaration can be used when PHP 7 is available
    //public function ForwardData(string $JSONString)
    public function ForwardData($JSONString)
	{
	 
		// Empfangene Daten von der Device Instanz
		$data = json_decode($JSONString);
		$datasend = $data->Buffer;
		$datasend = json_encode($datasend);
		$this->SendDebug("IFTTT Splitter Forward Data:",$datasend,0);
			
		// Hier würde man den Buffer im Normalfall verarbeiten
		// z.B. CRC prüfen, in Einzelteile zerlegen
		try
		{
			// Weiterleiten zur I/O Instanz
			$resultat = $this->SendDataToParent(json_encode(Array("DataID" => "{0259663C-D915-4A86-902B-70D865662E78}", "Buffer" => $data->Buffer))); //TX GUI
		}
		catch (Exception $ex)
		{
			echo $ex->getMessage();
			echo ' in '.$ex->getFile().' line: '.$ex->getLine().'.';
		}
	 
		
	 
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