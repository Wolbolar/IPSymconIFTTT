<?php

declare(strict_types=1);

class IFTTT extends IPSModule
{
    public function Create()
    {
        //Never delete this line!
        parent::Create();
        $this->ConnectParent('{B88FA10D-CCCA-483A-BAE2-01FEF13E7DD3}'); //IFTTT Splitter
        $this->RegisterPropertyString('iftttmakerkey', '');
        $this->RegisterPropertyString('event', '');
        $this->RegisterPropertyInteger('selection', 0);
        $this->RegisterPropertyInteger('countsendvars', 0);
        $this->RegisterPropertyInteger('countrequestvars', 0);
        $this->RegisterPropertyInteger('scriptid', 0);
        $this->RegisterPropertyString('command', '');
        for ($i = 1; $i <= 3; $i++) {
            $this->RegisterPropertyInteger('varvalue' . $i, 0);
        }
        for ($i = 1; $i <= 3; $i++) {
            $this->RegisterPropertyBoolean('modulinput' . $i, false);
        }
        for ($i = 1; $i <= 3; $i++) {
            $this->RegisterPropertyString('value' . $i, '');
        }
        for ($i = 1; $i <= 15; $i++) {
            $this->RegisterPropertyInteger('requestvarvalue' . $i, 0);
        }
        for ($i = 1; $i <= 15; $i++) {
            $this->RegisterPropertyBoolean('modulrequest' . $i, false);
        }
        $this->RegisterPropertyBoolean('iftttreturn', false);

        //we will wait until the kernel is ready
        $this->RegisterMessage(0, IPS_KERNELMESSAGE);
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        if (IPS_GetKernelRunlevel() !== KR_READY) {
            return;
        }

        $this->ValidateConfiguration();
    }

    private function ValidateConfiguration()
    {
        $iftttmakerkey    = $this->ReadPropertyString('iftttmakerkey');
        $event            = $this->ReadPropertyString('event');
        $selection        = $this->ReadPropertyInteger('selection');
        $countsendvars    = $this->ReadPropertyInteger('countsendvars');
        $countrequestvars = $this->ReadPropertyInteger('countrequestvars');
        $checkformsend    = false;
        $checkformget     = false;

        if ($selection == 4)// Google Home
        {
            $scriptid     = $this->ReadPropertyInteger('scriptid');
            $modulrequest = $this->ReadPropertyBoolean('modulrequest1');
            // Valuecheck
            if ($modulrequest === false && $scriptid == 0) {
                $errorid = 280;
                $this->SetStatus($errorid); // please complete scriptid field, errorid 280
            }

            $this->SetStatus(IS_ACTIVE);
        }

        if ($selection == 1 || $selection == 3) // Senden , Senden / Empfangen
        {
            $iftttass = [
                [0, 'Trigger Event', 'Execute', -1]];

            $this->RegisterProfileIntegerAss('IFTTT.Trigger', 'Execute', '', '', 0, 0, 0, 0, $iftttass);
            $this->RegisterVariableInteger('IFTTTTriggerEventButton', 'IFTTT Trigger Event Button', 'IFTTT.Trigger', 1);
            $this->EnableAction('IFTTTTriggerEventButton');

            //key prüfen
            if ($iftttmakerkey == '') {
                $this->SetStatus(206); // IFTTT Maker Feld darf nicht leer sein
                //$this->SetStatus(104);
            }
            //event prüfen
            if ($event == '') {
                $this->SetStatus(209); // Event Feld darf nicht leer sein
                //$this->SetStatus(104);
            }
            //event prüfen
            $eventcheck = false;
            if ($event !== '') {
                if (!preg_match('#^[a-zA-Z0-9_-]+$#', $event)) {
                    $this->SetStatus(207); //event Keine Sonderzeichen oder Leerzeichen
                    // String enthält auch andere Zeichen, Großbuchstaben, Sonderzeichen
                    //$this->SetStatus(104);
                } else {
                    $eventcheck = true;
                    // String enthält nur Kleinbuchstaben und Zahlen und _
                }
            }
            //maker key prüfen
            $makerkeycheck = false;
            if ($iftttmakerkey !== '') {
                if (!preg_match('#^[a-zA-Z0-9_-]+$#', $iftttmakerkey)) {
                    $this->SetStatus(208); //maker key, keine Sonderzeichen oder Leerzeichen
                    // String enthält auch andere Zeichen, Großbuchstaben, Sonderzeichen
                    //$this->SetStatus(104);
                } else {
                    $makerkeycheck = true;
                    // String enthält nur Kleinbuchstaben und Zahlen und _
                }
            }

            if ($countsendvars > 3) {
                $countsendvars = 3;
            }
            $varvaluecheck = false;
            $valuecheck    = false;
            // Trigger Vars
            for ($i = 1; $i <= $countsendvars; $i++) {
                ${'varvalue' . $i}   = $this->ReadPropertyInteger('varvalue' . $i);
                ${'modulinput' . $i} = $this->ReadPropertyBoolean('modulinput' . $i);
                ${'value' . $i}      = $this->ReadPropertyString('value' . $i);
                //Valuecheck
                if (${'modulinput' . $i} === false && ${'varvalue' . $i} === 0) {
                    $errorid = 220 + $i;
                    $this->SetStatus($errorid); //IFTTT This: select a value or enter value  in module. , errorid 221 - 235
                    break;
                } else {
                    $varvaluecheck = true;
                }
                //check Modul Value
                if (${'modulinput' . $i} === true && ${'value' . $i} === '') {
                    $errorid = 240 + $i;
                    $this->SetStatus($errorid); // IFTTT This: missing value, enter value in field value, errorid 241 - 255
                    break;
                } else {
                    $valuecheck = true;
                }
            }

            if ($makerkeycheck === true && $eventcheck == true && $varvaluecheck === true && $valuecheck === true) {
                $checkformsend = true;
            } elseif ($makerkeycheck === true && $eventcheck == true && $countsendvars === 0) {
                $checkformsend = true;
            }
        }

        if ($selection == 2 || $selection == 3) // Empfang , Senden / Empfangen
        {
            if ($countrequestvars > 15) {
                $countrequestvars = 15;
            }
            // Action Vars
            for ($i = 1; $i <= $countrequestvars; $i++) {
                ${'requestvarvalue' . $i} = $this->ReadPropertyInteger('requestvarvalue' . $i);
                ${'modulrequest' . $i}    = $this->ReadPropertyBoolean('modulrequest' . $i);
                $checkformget             = false;
                //Valuecheck
                if (${'modulrequest' . $i} === false && ${'requestvarvalue' . $i} === 0) {
                    $errorid = 260 + $i;
                    $this->SetStatus($errorid); //select a value or enter value in module, errorid 261 - 275
                    break;
                } else {
                    $checkformget = true;
                }
            }
        }

        if ($selection == 1 && $checkformsend == true) // Senden
        {
            $this->SetStatus(102);
        } elseif ($selection == 2 && $checkformget == true) // Empfang
        {
            $this->SetStatus(102);
        } elseif ($selection == 3 && $checkformsend == true && $checkformget == true) // Senden / Empfangen
        {
            $this->SetStatus(102);
        }
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        switch ($Message) {
            case IM_CHANGESTATUS:
                if ($Data[0] === IS_ACTIVE) {
                    $this->ApplyChanges();
                }
                break;

            case IPS_KERNELMESSAGE:
                if ($Data[0] === KR_READY) {
                    $this->ApplyChanges();
                }
                break;

            default:
                break;
        }
    }

    protected function SetRequestVariable($key, $value, $type, $i)
    {
        $ident = 'IFTTTAktionVar' . $i;
        $VarID = @$this->GetIDForIdent($ident);
        if ($VarID === false) {
            $VarID = $this->CreateVarbyType($type, $i, $key);
        }

        $this->SetVarbyType($type, $VarID, $key, $value);
    }

    protected function CreateVarbyType($type, $i, $key)
    {
        $ident = 'IFTTTAktionVar' . $i;
        if ($type == 'string') {
            $VarID = $this->RegisterVariableString($ident, $key, '~String', $i);
        } elseif ($type == 'integer') {
            $VarID = $this->RegisterVariableInteger($ident, $key, '', $i);
        } elseif ($type == 'double') //float
        {
            $VarID = $this->RegisterVariableFloat($ident, $key, '', $i);
        } elseif ($type == 'boolean') {
            $VarID = $this->RegisterVariableBoolean($ident, $key, '~Switch', $i);
        } else {
            $VarID = null;
        }

        return $VarID;
    }

    protected function SetVarbyType($type, $VarID, $key, $value)
    {
        if ($type == 'string') {

            SetValueString($VarID, $value);
            IPS_SetInfo($VarID, $key);
        } elseif ($type == 'integer') {
            SetValueInteger($VarID, $value);
            IPS_SetInfo($VarID, $key);
        } elseif ($type == 'double') //float
        {
            SetValueFloat($VarID, $value);
            IPS_SetInfo($VarID, $key);
        } elseif ($type == 'boolean') {
            SetValueBoolean($VarID, $value);
            IPS_SetInfo($VarID, $key);
        } elseif ($type == 'NULL') {
            $this->SendDebug('IFTTT', 'Vartype not known', 0);
        }

        return $VarID;
    }

    protected function WriteValues($valuesjson)
    {
        $this->SendDebug('Values from IFTTT', $valuesjson, 0);
        $selection = $this->ReadPropertyInteger('selection');
        $values    = json_decode($valuesjson);
        $eventname = 'IFTTTEvent';
        if (isset($values->EventName)) {
            $eventname = $values->EventName;
        }

        $valuesarr   = json_decode($valuesjson, true);
        $countvalues = count($valuesarr);

        if ($selection == 4) {
            $countrequestvars = 1;
        } else {
            $countrequestvars = $this->ReadPropertyInteger('countrequestvars');
        }
        if ($selection == 4) {
            $scriptid     = $this->ReadPropertyInteger('scriptid');
            $modulrequest = $this->ReadPropertyBoolean('modulrequest1');
            if ($modulrequest == true) {
                $i = 1;
                foreach ($values as $key => $value) {
                    $type = gettype($value); // Typ prüfen
                    $this->SetRequestVariable($key, $value, $type, $i);
                    $i = $i + 1;
                }
            } else {
                // state
                if (isset($values->Status)) {
                    $state = $values->Status;
                    $this->SendDebug('IFTTT', 'no value for state ' . $state . ' was send to the script with id ' . $scriptid, 0);
                    IPS_RunScriptEx($scriptid, ['State' => $state, 'EventName' => $eventname]);
                }

                // level
                if (isset($values->Level)) {
                    $level = $values->Level;
                    $this->SendDebug('IFTTT', 'no value for level ' . $level . ' was send to the script with id ' . $scriptid, 0);
                    IPS_RunScriptEx($scriptid, ['Level' => $level, 'EventName' => $eventname]);
                }
            }
            return;
        }

        if ($countvalues == $countrequestvars && $selection != 4) {
            $i = 1;
            foreach ($values as $key => $value) {
                $type = gettype($value); // Typ prüfen
                $this->SendDebug('IFTTT', 'value: ' . $value . ', variable type: ' . $type, 0);
                $requestvarvalue = $this->ReadPropertyInteger('requestvarvalue' . $i);  // Prüfen ob Modulvariable oder Var anlegen
                if ($requestvarvalue == 0) {
                    $this->SetRequestVariable($key, $value, $type, $i);
                } else {
                    $checkvartype = $this->CompareVartype($type, $requestvarvalue);
                    if ($checkvartype) {
                        SetValue($requestvarvalue, $value);
                    } else {
                        $this->SendDebug('IFTTT', 'variable type does not match, no value for ' . $value . ' was set.', 0);
                    }
                }
                $i = $i + 1;
            }
        } else {
            $this->SendDebug('IFTTT', 'number of variables do not match with with number of variables send from IFTTT!', 0);
            $this->SendDebug('IFTTT', 'no value was set', 0);
        }
    }

    protected function CompareVartype($type, $requestvarvalue)
    {
        $varinfo    = (IPS_GetVariable($requestvarvalue));
        $vartype    = $varinfo['VariableType'];
        $ipsvartype = false;
        if ($vartype == 0) //bool
        {
            $ipsvartype = 'boolean';
        } elseif ($vartype == 1) //integer
        {
            $ipsvartype = 'integer';
        } elseif ($vartype == 2) //float
        {
            $ipsvartype = 'double';
        } elseif ($vartype == 3) //string
        {
            $ipsvartype = 'string';
        }

        $this->SendDebug('IFTTT', 'variable with object id ' . $requestvarvalue . ' has variable type: ' . $vartype . ' (' . $ipsvartype . ')', 0);
        if ($type === $ipsvartype) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC.
     *
     * @param $objid
     *
     * @return bool|string
     */
    protected function ConvertVarString($objid)
    {
        $vartype = IPS_GetVariable($objid)['VariableType'];
        $value   = '';
        if ($vartype === 0)//Boolean
        {
            $value = GetValueBoolean($objid); // Boolean umwandeln in String
            $value = ($value) ? 'true' : 'false';
        } elseif ($vartype === 1)//Integer
        {
            $value = strval(GetValueInteger($objid));   // Integer Umwandeln in String
        } elseif ($vartype === 2)//Float
        {
            $value = strval(GetValueFloat($objid)); //Float umwandeln in String
        } elseif ($vartype === 3)//String
        {
            $value = GetValue($objid);  //string ok
        }
        return $value;

    }

    public function TriggerEvent()
    {
        $iftttmakerkey = $this->ReadPropertyString('iftttmakerkey');
        $event         = $this->ReadPropertyString('event');

        $countsendvars = $this->ReadPropertyInteger('countsendvars');
        if ($countsendvars > 0) {
            // Trigger Vars
            for ($i = 1; $i <= $countsendvars; $i++) {
                ${'modulinput' . $i} = $this->ReadPropertyBoolean('modulinput' . $i);
                if (${'modulinput' . $i}) {
                    ${'value' . $i} = $this->ReadPropertyString('value' . $i);
                    ${'key' . $i}   = 'value' . $i;
                } else {
                    ${'objidvalue' . $i} = $this->ReadPropertyInteger('varvalue' . $i);
                    ${'value' . $i}      = GetValue(${'objidvalue' . $i});
                    ${'key' . $i}        = IPS_GetName(${'objidvalue' . $i});
                    //${"value".$i} = $this->ConvertVarString(${"objidvalue".$i});
                }
            }

            $values = [];
            for ($i = 1; $i <= $countsendvars; $i++) {
                $values['value' . $i] = ${'value' . $i};
            }
            $count = count($values);
            if ($count == 1) {
                $values['value2'] = null;
                $values['value3'] = null;
            } elseif ($count == 2) {
                $values['value3'] = null;
            }
        } else {
            $values = ['value1' => null, 'value2' => null, 'value3' => null];
        }

        $values_string = json_encode($values);
        $this->SendDebug('IFTTT', 'Send trigger event ' . $event, 0);
        $this->SendDebug('IFTTT', 'Send trigger with values ' . $values_string, 0);
        $iftttreturn = $this->SendEventTriggerVar1to3($iftttmakerkey, $event, $values_string);
        return $iftttreturn;
    }

    protected function SendEventTriggerVar1to3(string $iftttmakerkey, string $event, string $values_string)
    {

        $values  = json_decode($values_string, true);
        $payload = ['iftttmakerkey' => $iftttmakerkey, 'event' => $event, 'values' => $values];

        //an Splitter schicken
        $result =
            $this->SendDataToParent(json_encode(['DataID' => '{78A293F6-50ED-4250-AF5A-05F6F2C563EB}', 'Buffer' => $payload])); //IFTTT Interface GUI
        return $result;
    }

    public function SendEventTrigger(string $iftttmakerkey, string $event, string $value1, string $value2, string $value3)
    {

        $values  = ['value1' => $value1, 'value2' => $value2, 'value3' => $value3];
        $payload = ['iftttmakerkey' => $iftttmakerkey, 'event' => $event, 'values' => $values];

        //an Splitter schicken
        $result =
            $this->SendDataToParent(json_encode(['DataID' => '{78A293F6-50ED-4250-AF5A-05F6F2C563EB}', 'Buffer' => $payload])); //IFTTT Interface GUI
        return $result;
    }

    public function ReceiveData($JSONString)
    {
        $data       = json_decode($JSONString);
        $objectid   = $data->Buffer->objectid;
        $values     = $data->Buffer->values;
        $valuesjson = json_encode($values);
        if (($this->InstanceID) == $objectid) {
            //Parse and write values to our variables
            $this->WriteValues($valuesjson);
        }
    }

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident) {
            case 'IFTTTTriggerEventButton':
                SetValue($this->GetIDForIdent('IFTTTTriggerEventButton'), $Value);
                $iftttreturn    = $this->TriggerEvent();
                $iftttreturnvis = $this->ReadPropertyBoolean('iftttreturn');
                if ($iftttreturnvis === true) {
                    $InstanzenListe = IPS_GetInstanceListByModuleID('{3565B1F2-8F7B-4311-A4B6-1BF1D868F39E}');
                    foreach ($InstanzenListe as $InstanzID) {
                        WFC_SendNotification($InstanzID, 'IFTTT', $iftttreturn, 'Execute', 4);
                    }
                }

                break;
            default:
                $this->SendDebug('IFTTT', 'Invalid ident', 0);
        }
    }

    protected function GetUsernamePassword()
    {
        $payload = ['event' => 'GET_WEBHOOK_USER_PASSWORD'];
        $result = $this->SendDataToParent(json_encode(['DataID' => '{78A293F6-50ED-4250-AF5A-05F6F2C563EB}', 'Buffer' => $payload])); //IFTTT Interface GUI
        $this->SendDebug('IFTTT Webhook', $result, 0);
        $webhooksettings = json_decode($result, true);
        return $webhooksettings;
    }

    protected function GetIOObjectID()
    {
        $InstanzenListe = IPS_GetInstanceListByModuleID('{2E91373A-E70B-46D8-99A7-71A499F6783A}');
        return $InstanzenListe[0];
    }

    /***********************************************************
     * Configuration Form
     ***********************************************************/

    /**
     * build configuration form.
     *
     * @return string
     */
    public function GetConfigurationForm()
    {
        // return current form
        return json_encode(
            [
                'elements' => $this->FormHead(),
                'actions'  => $this->FormActions(),
                'status'   => $this->FormStatus()]
        );
    }

    /**
     * return form configurations on configuration step.
     *
     * @return array
     */
    protected function FormHead()
    {
        $selection        = $this->ReadPropertyInteger('selection');
        $countsendvars    = $this->ReadPropertyInteger('countsendvars');
        $countrequestvars = $this->ReadPropertyInteger('countrequestvars');
        $form             = [
            [
                'type'    => 'Label',
                'caption' => 'Connection from IP-Symcon to IFTTT'],
            [
                'type'    => 'Label',
                'caption' => 'https://ifttt.com'],
            [
                'type'    => 'Label',
                'caption' => 'communication type with IFTTT: send, receive, send/receive, Google Home'],
            [
                'type'    => 'Select',
                'name'    => 'selection',
                'caption' => 'communication',
                'options' => [
                    [
                        'label' => 'Please Select',
                        'value' => 0],
                    [
                        'label' => 'Send',
                        'value' => 1],
                    [
                        'label' => 'Receive',
                        'value' => 2],
                    [
                        'label' => 'Send/Receive',
                        'value' => 3],
                    [
                        'label' => 'Google Home',
                        'value' => 4]]

            ]];
        if ($selection == 0)// keine Auswahl
        {
            $this->SendDebug('IFTTT', 'No selection', 0);
        } elseif ($selection == 1) // Senden
        {
            $form = array_merge_recursive(
                $form, [
                         [
                             'type'    => 'ExpansionPanel',
                             'caption' => 'IFTTT This',
                             'items'   => $this->FormSend($countsendvars)]]
            );
            $form = array_merge_recursive(
                $form, [
                         [
                             'type'    => 'ExpansionPanel',
                             'caption' => 'IFTTT Return Message',
                             'items'   => $this->IFTTTReturnMessage()]]
            );
        } elseif ($selection == 2) // Empfangen
        {
            $form = array_merge_recursive(
                $form, [
                         [
                             'type'    => 'ExpansionPanel',
                             'caption' => 'IFTTT That',
                             'items'   => $this->FormGet($countrequestvars)]]
            );
        } elseif ($selection == 3) // Senden / Empfangen
        {
            $form = array_merge_recursive(
                $form, [
                         [
                             'type'    => 'ExpansionPanel',
                             'caption' => 'IFTTT This',
                             'items'   => $this->FormSend($countsendvars)]]
            );
            $form = array_merge_recursive(
                $form, [
                         [
                             'type'    => 'ExpansionPanel',
                             'caption' => 'IFTTT Return Message',
                             'items'   => $this->IFTTTReturnMessage()]]
            );
            $form = array_merge_recursive(
                $form, [
                         [
                             'type'    => 'ExpansionPanel',
                             'caption' => 'IFTTT That',
                             'items'   => $this->FormGet($countrequestvars)]]
            );
        } elseif ($selection == 4) // Google Home
        {
            $form = array_merge_recursive(
                $form, [
                         [
                             'type'    => 'ExpansionPanel',
                             'caption' => 'Google Home via IFTTT',
                             'items'   => $this->FormGoogleHome()]]
            );
        }
        return $form;
    }

    protected function FormSend($countsendvars)
    {
        $form = [
            [
                'type'    => 'Label',
                'caption' => 'IFTTT maker key (look in IFTTT maker channel)'],
            [
                'name'    => 'iftttmakerkey',
                'type'    => 'ValidationTextBox',
                'caption' => 'IFTTT maker key'],
            [
                'type'    => 'Label',
                'caption' => 'please choose an event name (no special characters or blank)'],
            [
                'name'    => 'event',
                'type'    => 'ValidationTextBox',
                'caption' => 'event name'],
            [
                'type'    => 'Label',
                'caption' => 'number of variables for IFTTT This (max 3)'],
            [
                'name'    => 'countsendvars',
                'type'    => 'NumberSpinner',
                'caption' => 'number of variables']];

        if ($countsendvars > 0) {
            if ($countsendvars > 3) {
                $countsendvars = 3;
            }
            $form = array_merge_recursive(
                $form, [
                         [
                             'type'    => 'Label',
                             'caption' => 'variables with values for IFTTT']]
            );
            for ($i = 1; $i <= $countsendvars; $i++) {
                $form = array_merge_recursive(
                    $form, [
                             [
                                 'name'    => 'varvalue' . $i,
                                 'type'    => 'SelectVariable',
                                 'caption' => 'value ' . $i]]
                );
            }
            $form = array_merge_recursive(
                $form, [
                         [
                             'type'    => 'Label',
                             'caption' => 'alternative leave variable empty und click check mark']]
            );
            for ($i = 1; $i <= $countsendvars; $i++) {
                $form = array_merge_recursive(
                    $form, [
                             [
                                 'name'    => 'modulinput' . $i,
                                 'type'    => 'CheckBox',
                                 'caption' => 'use modul value ' . $i],
                             [
                                 'name'    => 'value' . $i,
                                 'type'    => 'ValidationTextBox',
                                 'caption' => 'value ' . $i]]
                );
            }
        }
        return $form;
    }

    private function IFTTTReturnMessage()
    {
        $form = [
            [
                'type'    => 'Label',
                'caption' => 'Return Message from IFTTT'],
            [
                'name'    => 'iftttreturn',
                'type'    => 'CheckBox',
                'caption' => 'IFTTT Return']];
        return $form;
    }

    protected function FormGet($countrequestvars)
    {
        $form = [
            [
                'type'    => 'Label',
                'caption' => 'variables with values for IFTTT That'],
            [
                'type'    => 'Label',
                'caption' => 'number of variables for a IFTTT That (max 15)'],
            [
                'name'    => 'countrequestvars',
                'type'    => 'NumberSpinner',
                'caption' => 'number of variables']];
        if ($countrequestvars > 0) {
            if ($countrequestvars > 15) {
                $countrequestvars = 15;
            }

            for ($i = 1; $i <= $countrequestvars; $i++) {
                $form = array_merge_recursive(
                    $form, [
                             [
                                 'name'    => 'requestvarvalue' . $i,
                                 'type'    => 'SelectVariable',
                                 'caption' => 'value ' . $i]]
                );
            }
            $form = array_merge_recursive(
                $form, [
                         [
                             'type'    => 'Label',
                             'caption' => 'alternative leave variable empty und click check mark for creating a new variable']]
            );
            for ($i = 1; $i <= $countrequestvars; $i++) {
                $form = array_merge_recursive(
                    $form, [
                             [
                                 'name'    => 'modulrequest' . $i,
                                 'type'    => 'CheckBox',
                                 'caption' => 'module create variable for value ' . $i]]
                );
            }
        }
        return $form;
    }

    protected function FormGoogleHome()
    {
        $form = [
            [
                'type'    => 'Label',
                'caption' => 'configure the IFTTT Applet in IFTTT, details can be found below in the test enviroment'],
            [
                'type'    => 'Label',
                'caption' => 'two options are available'],
            [
                'type'    => 'Label',
                'caption' => 'first option switch device via trigger a script'],
            [
                'type'    => 'Label',
                'caption' => 'Please select a script to trigger'],
            [
                'name'    => 'scriptid',
                'type'    => 'SelectScript',
                'caption' => 'Script Target'],
            [
                'type'    => 'Label',
                'caption' => 'second option leave field above empty and create variable'],
            [
                'type'    => 'Label',
                'caption' => 'click check mark for creating a new variable'],
            [
                'name'    => 'modulrequest1',
                'type'    => 'CheckBox',
                'caption' => 'module create variable for value']];
        return $form;
    }

    /**
     * return form actions by token.
     *
     * @return array
     */
    protected function FormActions()
    {
        $selection = $this->ReadPropertyInteger('selection');
        // $countrequestvars = $this->ReadPropertyInteger("countrequestvars");
        $event = $this->ReadPropertyString('event');
        $form  = [];
        if ($selection == 0)// keine Auswahl
        {
            $form = [];
        } elseif ($selection == 1) // Senden
        {
            $form = [
                [
                    'type'  => 'Image',
                    'image' => 'data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAASwAAABQCAYAAACj6kh7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjlGQkMzNDlBN0EyODExRTlCNTlERTA5QjI3NENEMUJGIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjlGQkMzNDlCN0EyODExRTlCNTlERTA5QjI3NENEMUJGIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6OUZCQzM0OTg3QTI4MTFFOUI1OURFMDlCMjc0Q0QxQkYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6OUZCQzM0OTk3QTI4MTFFOUI1OURFMDlCMjc0Q0QxQkYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5uinOUAAAEVklEQVR42uzdz28UVQDA8fdmZrfdsmwX5IcQAqkQsW3UqmmMF8IfUI548cAFLph4wZP8AcQYriaE5QDhYvBkE5rYg9GgqPWAJkD5ZSOlbXZp0bK13d3um8d7izFQtq3yZmja+X7SHpq205m37313ZrfJSHFKaxEnX4iu0a8+3jsxcHLezyz5o0FYFZf3fNhf3NDVJ5RYw7Tef/3Trtzc2HDopZ4eA1URQ68cPj+26e0P1vYYPMETIj9z9+d9N06+G0rzhZD/fkvqUMwHmfXfdB6/XU1ltwidkDEx6+a1exc/6RzvP7Fw3dh1MpZ/q29oz+F+EYpE8QQAECwAIFgACBYAECwAIFgACBYAECwAIFgACBYAECwAIFgACBYAECwAIFgACBYAECwAIFgACBYAECwAIFgACBYAECwAWCBI5FHLlU61lFrIRfdAeek2e186Z/aedXoVjKX5/brXkl3qr9T91lxjTFyP50Xc6/HpWys+H3OsoQxaFjtgLf1UYzxc/47+Z57EzY9mM8kLlnmAs5XS8KbyrUuh9FdqL3RazT7QTZpl92nLw2sX0+HMlMtE8rQSU9nd75UzL3fHFi0zlq216bGt01cHtPScttNWfTCim64+aY+ltmvy+8/rfkvO5Vi0lHKivedgYzsxzq+N5ZFLubnxYaf5ZW8uO/vHT6FMNZ0jbbWpOzuLPxZcxsPepLaSbt9azHUfiHfJabVt6sqFlKrMaMfCJi9YZiJsLt8c7P298NFyd6KOkzljsM+Sz05GLy067n9X8Eqq4LL9lJoTv3Qc+qy8zgRLxbc4zcL8tXfkzBFzVuhWcBM8c5bVLDLCC+uVN+5+cUw6rE77u3ahD3bv3lcP3MK33PzaOXn59KvFr8+5zi/lpcTjcdULghWI9tnR394ZOXvE6aQnrIlSrrOn2G6CFeOTmgxV/fXRL49mq6U/7b4TrP97pST9tJ1MKxmspc7R7SRVkRxn0BL73kovsOPoGqz/EnjHZ3n7uAdLXYpHxYQmE938WvSS0Hn7NuDLXIpH+fjlzP46B4sX3QGspgskACBYABCpRL6GJXWo/HC+8aJmZLT58Hyhnd9nBkCwnlAL1r003bZjb7N3pZ6/go23+O8FYfVvogUQrGgoIcbzPe9P5N88GPXFde/tQt/2v64MRBpCAMk+w2pcFUopY/jfE06tgBgl90V3vYq2CiDhwQJAsACAYAEgWAwBAIIFAAQLAMECAIIFAAQLAMECAIIFAAQLAMECAIIFAAQLAMECAIIFAAQLAMECAIIFAAQLwBqSzNt8yRhSbbanpRcwpQCCFWlYNk/fHOy4/+1p5aUjjWB+dnRIyRSzCiBY0YUlWy0N75r84cK8n4l00zaA5iyLWQUQrOiE0k/X/VZhPwGsqgskACBYAECwABAsACBYAECwABAsACBYAECwABAsACBYAECwABAsACBYAECwABAsACBYAECwABAsAHjRHgkwAM2WJsURXBguAAAAAElFTkSuQmCC'],
                [
                    'type'    => 'Label',
                    'caption' => 'IFTTT configuration:'],
                [
                    'type'    => 'ExpansionPanel',
                    'caption' => 'IFTTT This configuration:',
                    'items'   => [
                        [
                            'type'    => 'Label',
                            'caption' => ' - Select My Applets'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Push New Applet'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - push this'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - choose Webhooks'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Receive a webrequest'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Event Name: ' . $event],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Create Trigger'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - continue with That of your choice']]],
                [
                    'type'    => 'Label',
                    'caption' => '______________________________________________________________________________________________________'],
                [
                    'type'    => 'Label',
                    'caption' => 'Trigger IFTTT Event'],
                [
                    'type'    => 'Button',
                    'caption' => 'Trigger Event',
                    'onClick' => 'IFTTT_TriggerEvent($id);']];
        } elseif ($selection == 2) // Empfangen
        {
            $form = [
                [
                    'type'    => 'Label',
                    'caption' => 'IFTTT configuration:'],
                [
                    'type'    => 'ExpansionPanel',
                    'caption' => 'IFTTT That configuration:',
                    'items'   => [
                        [
                            'type'    => 'Label',
                            'caption' => ' - Method:'],
                        [
                            'type'    => 'Label',
                            'caption' => '     POST '],
                        [
                            'type'    => 'Label',
                            'caption' => ' - URL:'],
                        [
                            'type'    => 'ValidationTextBox',
                            'name'    => 'URL',
                            'value'   => $this->GetIPSConnect() . '/hook/IFTTT',
                            'width' => '550px',
                            'caption' => 'URL'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Content Type:'],
                        [
                            'type'    => 'Label',
                            'caption' => '      "application/json"'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Body: (example)'],
                        [
                            'type'    => 'ValidationTextBox',
                            'name'    => 'Body',
                            'value'   => '{"username":"'. $this->IFTTTConfigAuthUser(). '","password":"'. $this->IFTTTConfigAuthPassword(). '","objectid":' . $this->InstanceID
                                . ',"values":{"keyvalue1":"value1string","keyvalue2":value2float,"keyvalue3":value3int,"keyvalue4":value4bool}}',
                            'multiline' => true,
                            'width' => '550px',
                            'caption' => 'URL'],
                        [
                            'type'    => 'Label',
                            'caption' => '     example values begin and end with curly brackets'],
                        [
                            'type'    => 'Label',
                            'caption' => '     put keys always inside "", string value inside "", boolean, integer and float values without ""'],
                        [
                            'type'    => 'Label',
                            'caption' => '     username (standard ipsymcon), set username in IFTTT IO'],
                        [
                            'type'    => 'Label',
                            'caption' => '     password is set, for individual password set password in IFTTT IO']]]];
        } elseif ($selection == 3) // Senden / Empfangen
        {
            $form = [
                [
                    'type'    => 'Label',
                    'caption' => 'IFTTT configuration:'],
                [
                    'type'    => 'ExpansionPanel',
                    'caption' => 'IFTTT This configuration:',
                    'items'   => [
                        [
                            'type'    => 'Label',
                            'caption' => 'IFTTT This configuration:'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Select My Applets'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Push New Applet'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - push this'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - choose Webhooks'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Receive a webrequest'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Event Name: ' . $event],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Create Trigger'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - continue with That of your choice']]],
                [
                    'type'    => 'ExpansionPanel',
                    'caption' => 'IFTTT That configuration:',
                    'items'   => [
                        [
                            'type'    => 'Label',
                            'caption' => ' - Method:'],
                        [
                            'type'    => 'Label',
                            'caption' => '     POST '],
                        [
                            'type'    => 'Label',
                            'caption' => ' - URL:'],
                        [
                            'type'    => 'ValidationTextBox',
                            'name'    => 'URL',
                            'value'   => $this->GetIPSConnect() . '/hook/IFTTT',
                            'caption' => 'URL'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Content Type:'],
                        [
                            'type'    => 'Label',
                            'caption' => '      "application/json"'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Body: (example)'],
                        [
                            'type'    => 'ValidationTextBox',
                            'name'    => 'Body',
                            'value'   => '{"username":"'. $this->IFTTTConfigAuthUser(). '","password":"'. $this->IFTTTConfigAuthPassword(). '","objectid":' . $this->InstanceID
                                . ',"values":{"keyvalue1":"value1string","keyvalue2":value2float,"keyvalue3":value3int,"keyvalue4":value4bool}}',
                            'multiline' => true,
                            'width' => '300px',
                            'caption' => 'URL'],
                        [
                            'type'    => 'Label',
                            'caption' => '     example values begin and end with curly brackets'],
                        [
                            'type'    => 'Label',
                            'caption' => '     put keys always inside "", string value inside "", boolean, integer and float values without ""'],
                        [
                            'type'    => 'Label',
                            'caption' => '     username (standard ipsymcon), set username in IFTTT IO'],
                        [
                            'type'    => 'Label',
                            'caption' => '     password is set, for individual password set password in IFTTT IO']]],
                [
                    'type'    => 'Label',
                    'caption' => '______________________________________________________________________________________________________'],
                [
                    'type'    => 'Label',
                    'caption' => 'Trigger IFTTT Event'],
                [
                    'type'    => 'Button',
                    'caption' => 'Trigger Event',
                    'onClick' => 'IFTTT_TriggerEvent($id);']];
        } elseif ($selection == 4) // Google Home
        {
            $form = [
                [
                    'type'    => 'Label',
                    'caption' => 'IFTTT configuration:'],
                [
                    'type'    => 'ExpansionPanel',
                    'caption' => 'IFTTT This configuration:',
                    'items'   => [
                        [
                            'type'    => 'Label',
                            'caption' => ' - Select My Applets'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Push New Applet'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - push this'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - choose Google Assistant'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - choose Say a simple phrase'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - complete from and then push Create Trigger'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - push that'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - choose Action Service Webhooks'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - push Make a web request'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - choose Action Service Webhooks'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - URL:'],
                        [
                            'type'    => 'ValidationTextBox',
                            'name'    => 'URL',
                            'value'   => $this->GetIPSConnect() . '/hook/IFTTT',
                            'caption' => 'URL'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - choose Action Service Webhooks'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Method:'],
                        [
                            'type'    => 'Label',
                            'caption' => '     POST '],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Content Type:'],
                        [
                            'type'    => 'Label',
                            'caption' => '     application/json'],
                        [
                            'type'    => 'Label',
                            'caption' => ' - Body: (example)'],
                        [
                            'type'    => 'Label',
                            'caption' => '     {"username":"' . $this->IFTTTConfigAuthUser() . '","password":"' . $this->IFTTTConfigAuthPassword()
                                         . '","objectid":' . $this->InstanceID . ',"values":{"EventName": "Living Room","Status":false<<<}>>>}'],
                        [
                            'type'    => 'Label',
                            'caption' => '     EventName, choose name for this event, this is also the variable name if the variable is created in IP-Symcon'],
                        [
                            'type'    => 'Label',
                            'caption' => '     Status, false turn device off, true turns device on'],
                        [
                            'type'    => 'Label',
                            'caption' => '     put keys always inside "", string value inside "", boolean, integer and float values without ""'],
                        [
                            'type'    => 'Label',
                            'caption' => '     webhhookusername , set username in IFTTT IO'],
                        [
                            'type'    => 'Label',
                            'caption' => '     webhookpassword, set individual password in IFTTT IO']]]];
        }
        return $form;
    }

    protected function IFTTTConfigRequest($countrequestvars)
    {
        if ($countrequestvars == 0) {
            $form = '{ "type": "Label", "label": "         values  please select at least one value" }';
        } else {
            $form = '{ "type": "Label", "label": "         values              {';
            for ($i = 1; $i <= 4; $i++) {
                $form .= '\\"keyvalue' . $i . '\\":\\"value' . $i . '\\",';
            }
            $form = substr($form, 0, -1);
            $form .= ' }"},';
        }
        return $form;
    }

    protected function IFTTTConfigAuthUser()
    {
        $webhooksettings = $this->GetUsernamePassword();
        $username        = $webhooksettings['username'];
        return $username;
    }

    protected function IFTTTConfigAuthPassword()
    {
        $webhooksettings = $this->GetUsernamePassword();
        $password        = $webhooksettings['password'];
        return $password;
    }

    /**
     * return from status.
     *
     * @return array
     */
    protected function FormStatus()
    {
        $form = [
            [
                'code'    => IS_CREATING,
                'icon'    => 'inactive',
                'caption' => 'Creating instance.'],
            [
                'code'    => IS_ACTIVE,
                'icon'    => 'active',
                'caption' => 'IFTTT created.'],
            [
                'code'    => IS_INACTIVE,
                'icon'    => 'inactive',
                'caption' => 'interface closed.'],
            [
                'code'    => 201,
                'icon'    => 'inactive',
                'caption' => 'select number of values in module.'],
            [
                'code'    => 202,
                'icon'    => 'error',
                'caption' => 'special errorcode.'],
            [
                'code'    => 206,
                'icon'    => 'error',
                'caption' => 'IFTTT maker field must not be empty.'],
            [
                'code'    => 207,
                'icon'    => 'error',
                'caption' => 'event not valid.'],
            [
                'code'    => 208,
                'icon'    => 'error',
                'caption' => 'IFTTT maker key not valid.'],
            [
                'code'    => 209,
                'icon'    => 'error',
                'caption' => 'Event field must not be empty.'],
            [
                'code'    => 280,
                'icon'    => 'error',
                'caption' => 'please complete script id field.']];
        $form = array_merge_recursive(
            $form, $this->FormStatusErrorSelectorEnterThat()
        );
        $form = array_merge_recursive(
            $form, $this->FormStatusErrorSelectorEnter()
        );
        $form = array_merge_recursive(
            $form, $this->FormStatusErrorMissingValueinField()
        );

        return $form;
    }

    protected function FormStatusErrorSelectorEnter() // errorid 221 - 223
    {
        $form = [];
        for ($i = 1; $i <= 3; $i++) {
            $errorid = 220 + $i;
            $form    = array_merge_recursive(
                $form, [
                         [
                             'code'    => $errorid,
                             'icon'    => 'error',
                             'caption' => 'IFTTT IF: select a value ' . $i . ' or enter value ' . $i . ' in module.']]
            );
        }
        return $form;
    }

    protected function FormStatusErrorMissingValueinField() // errorid 241 - 243
    {
        $form = [];
        for ($i = 1; $i <= 3; $i++) {
            $errorid = 240 + $i;
            $form    = array_merge_recursive(
                $form, [
                         [
                             'code'    => $errorid,
                             'icon'    => 'error',
                             'caption' => 'IFTTT IF: missing value, enter value in field value ' . $i]]
            );
        }
        return $form;
    }

    protected function FormStatusErrorSelectorEnterThat() // errorid 261 - 275
    {
        $form = [];
        for ($i = 1; $i <= 15; $i++) {
            $errorid = 260 + $i;
            $form    = array_merge_recursive(
                $form, [
                         [
                             'code'    => $errorid,
                             'icon'    => 'error',
                             'caption' => 'IFTTT That: select a value ' . $i . ' or enter value ' . $i . ' in module.']]
            );
        }
        return $form;
    }

    // IP-Symcon Connect auslesen
    protected function GetIPSConnect()
    {
        $connectinfo = $this->GetConnectUrl();
        if ($connectinfo == false || $connectinfo == '') {
            //	$connectinfo = 'https://<IP-Symcon Connect>.ipmagic.de';
            $connectinfo = 'https://123456789abcdefgh.ipmagic.de';
        }
        return $connectinfo;
    }

    private function GetConnectUrl()
    {
        $instID = IPS_GetInstanceListByModuleID('{9486D575-BE8C-4ED8-B5B5-20930E26DE6F}')[0];
        if (IPS_GetKernelVersion() >= 5.2) {
            $url = CC_GetConnectURL($instID);
        } else {
            $url = CC_GetUrl($instID);
        }
        return $url;
    }
    
    //Profile
    protected function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits)
    {

        if (!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, 1);
        } else {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != 1) {
                $this->SendDebug('IFTTT', 'Variable profile type does not match for profile ' . $Name, 0);
            }
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
        IPS_SetVariableProfileValues(
            $Name, $MinValue, $MaxValue, $StepSize
        ); // string $ProfilName, float $Minimalwert, float $Maximalwert, float $Schrittweite

    }

    protected function RegisterProfileIntegerAss($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Associations)
    {
        if (count($Associations) === 0) {
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
        foreach ($Associations as $Association) {
            IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
        }

    }

    //Add this Polyfill for IP-Symcon 4.4 and older
    protected function SetValue($Ident, $Value)
    {

        if (IPS_GetKernelVersion() >= 5) {
            parent::SetValue($Ident, $Value);
        } else {
            SetValue($this->GetIDForIdent($Ident), $Value);
        }
    }
}
