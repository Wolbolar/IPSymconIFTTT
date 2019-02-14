# IPSymconIFTTT
[![Version](https://img.shields.io/badge/Symcon-PHPModule-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-%3E%205.1-green.svg)](https://www.symcon.de/en/service/documentation/installation/)

Module for IP-Symcon version 4.1 or higher enables communication with the IFTTT service.
Connection of IFTTT to IP-Symcon via the _Webhooks_ Service.

## Documentation

**Table of Contents**

1. [Features](#1-features)
2. [Requirements](#2-requirements)
3. [Installation](#3-installation)
4. [Function reference](#4-functionreference)
5. [Configuration](#5-configuration)
6. [Annex](#6-annex)

## 1. Features

The Internet service IFTTT offers the possibility to link different Internet services together
and to create a workflow from a trigger and an action, called an applet. Unlike others services with IFTTT only _one_ trigger can be linked to an action.
The _Webhooks_ service of IFTTT can be included as a trigger (__*THIS*__) or as an action (__*THAT*__) in IFTTT applets. The _Webhooks_ service can be connected to any other services to an IFTTT applet.

####Trigger

The _Webhooks_ Service trigger allows the definition of an event and allows 3 variables to be transferred. Variables can be linked to the
module or values can be entered as a constant in the module itself, which are sent to IFTTT by IP-Symcon to trigger the THIS part of an applet. The passed variables can then be in a
IFTTT action. In this way channels from IFTTT can be triggered with IP-Symcon.

####Action

When a trigger of an IFTTT applet is triggered, an action can be triggered that forwards information to IP-Symcon.
Here it depends on the selected trigger which ingredients are available. With an email channel for example _From Subject_ and _Body_ are transmitted. Depending on the trigger different selections will be available.
For actions, the number of variables to be passed can be freely defined.
In IP Symcon, the incoming data is written to a variable. Because the number of possible combinations of services
and the number of variables to be transmitted differs from case to case, the number of variables in the module must be set.

Further information about IFTTT

[IFTTT](https://ifttt.com "IFTTT")

## 2. Requirements

 - IPS 4.1
 - IP-Symcon Connect
 - Account [IFTTT](https://ifttt.com "IFTTT")
 - IFTTT Account with configured _Webhooks_ service

## 3. Installation

### a. Vorbereitungen in IFTTT
 
 Sign in to IFTTT or create a new user if you do not already have an account.
 After logging in the menu bar, switch in the top menu to *My Applets*.
 
![Select Channel](img/ifttt-menu1.png?raw=true "IFTTT Menu")

 Go to _Services_ tab and enter *Webhooks* in the search field
 
 
![Select Maker Channel](img/ifttt-2.png?raw=true "Select Webhooks")
 
 Select the _Webhooks_ service and switch to _Settings_. Connect in the settings of the Webhooks.
 If we click on _Documentation_ in _Webbhooks_ at the top right, the entry will appear at the top of the new page
 _Your key is:_ We write down the key, we need this key to later to trigger applets from IP-Symcon.
 
### b. Loading the module

Open the IP Console's web console with _http://<IP-Symcon IP>:3777/console/_.

Then click on the module store icon in the upper right corner.

![Store](img/store_icon.png?raw=true "open store")

In the search field type

```
IFTTT
```  


![Store](img/module_store_search_en.png?raw=true "module search")

Then select the module and click _Install_

![Store](img/install_en.png?raw=true "install")


#### Install alternative via Modules instance

_Open_ the object tree.

![Objektbaum](img/object_tree.png?raw=true "object tree")	

Open the instance _'Modules'_ below core instances in the object tree of IP-Symcon (>= Ver 5.x) with a double-click and press the _Plus_ button.

![Modules](img/modules.png?raw=true "modules")	

![Plus](img/plus.png?raw=true "Plus")	

![ModulURL](img/add_module.png?raw=true "Add Module")
 
Enter the following URL in the field and confirm with _OK_:


```	
https://github.com/Wolbolar/IPSymconNanoleaf
```
    
and confirm with _OK_.    
    
Then an entry for the module appears in the list of the instance _Modules_

By default, the branch _master_ is loaded, which contains current changes and adjustments.
Only the _master_ branch is kept current.

![Master](img/master.png?raw=true "master") 

If an older version of IP-Symcon smaller than version 5.1 (min 4.1) is used, click on the gear on the right side of the list.
It opens another window,

![SelectBranch](img/select_branch_en.png?raw=true "select branch") 

here you can switch to another branch, for older versions smaller than 5.1 (min 4.1) select _Old-Version_ .

### c.  Setup in IP-Symcon

In IP-Symcon a separate instance of each event we want to trigger is created. The IFTTT IO and IFTTT splitter will be
automatically created. To create the instance, we change to the category under which we want to place the instance
and create a new _instance_ (_right click -> add object -> instance_).

![Instanz erstellen](img/ifttt_instance_en.png?raw=true "Instanz erstellen")

Via IFTTT we find the instance and with _Ok_ this is created.

##### Selection of communication and number of variables

There is the possibility to trigger an applet from IP-Symcon as well as the possibility to get data from IFTTT. Each IP-Symcon IFTTT instance represents an event that occurs in IFTTT. Any number of instances can be created.
After opening the configuration form the instance with a dobble click, a query appears asking if the current instance should only be used for sending, receiving or for sending/receiving, Google Home.

![Kommunikationabfrage](img/selection_en.png?raw=true "Auswahl")

After the appropriate selection has been made, we confirm with _Apply Changes_.

![Apply_Changes](img/apply_changes_en.png?raw=true "Apply Changes")

Now we have more options depending on the previous selection. Now we set the number of variables the instance should use.
To trigger an event in IFTTT, basically no variable is necessary, the _Eventname_ is enough to trigger an applet. But there is
the possibility to transfer up to _*three values*_ to IFTTT with the trigger. So we select _up to 3 values_ when need. In the reverse
direction it depends on the trigger how many variables are sent.

![Varanzahl](img/ifttt_form_makerkey_en.png?raw=true "Variablenanzahl eintragen")

There are countless channels and countless possibilities that IFTTT can send to IP-Symcon, depending on which applet
is used you must set as many variables as IFTTT expects. Make sure that the selected variable type corresponds to the expected value from IFTTT,
otherwise the data will not be stored. If no variables are already available, optionally a selection can be made, then the module creates a suitable variable for the variable type.
The only important thing is the number of variables matches with the number of IFTTT
sent data. The maximum number of variables that the module currently manages is 15.

After the appropriate number of variables has been selected, we confirm with _Apply Changes_ then **we close the instance** and then open it again.

Now add the IFTTT Makerkey in the config form field.

![Makerkey eintragen](img/ifttt_form_that_en.png?raw=true "form that")

The event name can be chosen freely. It is only to pay attention that no umlauts, special characters and spaces in the event name
be used. As a result of the fact that the event name can be freely determined, any number of instances can be created in IP-Symcon
each with a different event name. These then trigger different events (applets) in IFTTT.
Under value 1-3, a variable can be selected which should be sent to IFTTT.

Alternatively, it is also possible to select no variables but instead enter a constant value directly in the module
which should be handed over to IFTTT at an event. The tick at _IFTTT Return_ is disabled by default. If a tick is set here, a short message will appear
in the webfront if the message has been sent.
When all the settings have been made , we first have to configure IFTTT to be able to use the trigger in IFTTT.
	
### d. Configuring the Webhooks Applet in IFTTT

##### Webhooks as triggers of an applet

Now we need to create an applet in IFTTT that accepts our IP Symcon messages to IFTTT.
To do this we switch back to IFTTT to _My Applets_ and create a new applet with _New Applet_.

![Create Recipe](img/newapplet.png?raw=true "New Applet")

Then select __*this*__

![select this](img/this.png?raw=true "select this")

Enter _Webhooks_ in the search window and select the _Webhooks_ service

![search maker](img/step1webhooks.png?raw=true "search webhooks")

At Choose a Trigger, press Receive a web request

![choose trigger](img/step2webhooks.png?raw=true "choose trigger")

Here we enter the event name we have chosen in the IP-Symcon module. In the example _ips_test_. Then we press Create Trigger.


![create trigger](img/step3webhooks.png?raw=true "create trigger")
	
In the next step, select __*That*__

![select that](img/step4webhooks.png?raw=true "select that")

Here we now choose one of the available channels in IFTTT to be triggered from IP-Symcon. We can select the 3 values from IP-Symcon in the action.
In the example, I just use gmail, maybe something very banal because you can send directly from IP-Symcon emails at an event,
but this is just an example for one of the many IFTTT channels.

![select gmail](img/step1gmail.png?raw=true "select gmail")
	
choose Send a email

![send gmail](img/step2gmail.png?raw=true "send gmail")

Here we find again our event name and the values we sent from IP-Symcon to IFTTT. So we can then just tinker with
different evenets and different values.

![values gmail](img/step3gmail.png?raw=true "values1 gmail")

With _*Create Action*_ we save the recipe.

Finish now completes the applet.
	
![finish](img/step4gmail.png?raw=true "finish")

##### Webhooks as an action (That) of an applet

If we want to send data from IFTTT to IP-Symcon we integrate the _Webhooks_ service as action (That) into an applet. The applet then sends data from IFTTT
IP symcon when an action is triggered by a recipe.

First, a trigger (This) is created with the service that should set the _This_ (IF) part of the applet (see above).

As already described above, the same procedure
- _*New Applet*_
- select _*This*_ 
- select any available service (for simplification I'll use gmail again as an example)

e.g. select _Any new email in inbox_ 

![any new email](img/gmail1action.png?raw=true "any new email")

this time we choose __*That*__ as Action Maker Channel

![action gmail that](img/gmailthat.png?raw=true "action gmail that")

Choose action service

_Webhooks_

![make a web request](img/makeawebrequest.png?raw=true "make a webrequest")

select _Make a web request_ 

Now we have to enter the appropriate values

![form request](img/webhookrequestform.png?raw=true "form request")
	
We look in IP-Symcon for the object ID of the instance to which the data is to be sent and record it.

The data is sent to IP-Symcon as JSON.


| Property   | Value                                                                                                         |
| :--------: | :-----------------------------------------------------------------------------------------------------------: |	
|URL:        | https:// _IP-Symon Connect Adress_ .ipmagic.de/hook/IFTTT                                                     |
|Method:     | _POST_                                                                                                        |
|Content Type|  _application/json_                                                                                           |  
|Body        |  {"username":"ipsymcon","password":"mypassword","objectid":12345,"values":{"EventName":"{{EventName}}",       |
|            |  "Value1":"{{Value1}}", "Value2":"{{Value2}}","Value3":"{{Value3}}", "OccurredAt":"{{OccurredAt}}"<<<}>>>}    |


The module in IP Symcon expects the data as follows:

| Property              | Value                                                                                                         |
| :-------------------: | :-----------------------------------------------------------------------------------------------------------: |
| _**URL**_             | IP-Symcon Connect Adress/hook/IFTTT                                                                           |
|_**Method**_           | POST                                                                                                          |
|_**Content Type**_     | application/json                                                                                              |                                                                                          |
|_**Body**_             |  {"username":"ipsymcon","password":"mypassword","objectid":12345,"values":{"EventName":"{{EventName}}",       |
|                       |  "Value1":"{{Value1}}", "Value2":"{{Value2}}","Value3":"{{Value3}}", "OccurredAt":"{{OccurredAt}}"<<<}>>>}    |
|\[_username_\]         | Username in the IFTTT IO is also displayed in the test environment of the instance                            |
|\[_password_\]         | Password in the IFTTT IO is also displayed in the test environment of the instance                            |
|_objectid_             | ObjectID of the IFTTT instance that should receive the data                                                   |
|_values_ (\[payload\]) |{"value1":"value1string","value2":value2boolean,"value3":value3integer,"value4":value4float}                   |
	
	
The values ​​are passed as JSON within the form. Keys are always enclosed with "" as well as string variables.
Integer, Float and Boolean are not enclosed with "" to set the variable. In the example above with the email all
variables would be of type string and enclosed with "".

If the applet triggers a web request should be sent to IP Symcon. The content are the stored and selected variables of the IFTTT instance.
If the selection has been made that the module creates the variables, then these are created automatically  the first time data is received in IP Symcon
according to the variable type of the data in IP-Symcon.

##### Google Home as Action (This) of an applet	

When we are using Google Home to switch a device to IP Symcon, we are using the Google Assistant service as _This_ part of the applet. The applet then sends data from IFTTT
to IP Symcon when an action is triggered by Google Home.

First, a Trigger (This) is created using the _Google Assistant_ service.

As already described above, the same procedure
- _*New Applet*_
- select _*This*_ 
- select _Google Assistant_ Service 
- To turn on a device we choose _Say a simple phrase_ and fill in the wording to respond to the Google Home
- As _That_ we choose _Webhooks_ service
- Here we enter the following


| Property   | Value                                                                                                                                |
| :--------: | :----------------------------------------------------------------------------------------------------------------------------------: |	
|URL:        | https:// _IP-Symon Connect Adress_ .ipmagic.de/hook/IFTTT                                                                            |
|Method:     | _POST_                                                                                                                               |
|Content Type|  _application/json_                                                                                                                  |  
|Body        |  {"username":"ipsymcon","password":"meinpasswort","objectid":12345,"values":{"EventName": "Wohnzimmer Licht","Status":true<<<}>>>}   |

The following data is expected:

| Value                 | explanation                                                                                                   |
| :-------------------: | :-----------------------------------------------------------------------------------------------------------: |
|\[_username_\]         | Username in the IFTTT IO is also displayed in the test environment of the instance                            |
|\[_password_\]         | Password in the IFTTT IO is also displayed in the test environment of the instance                            |
|_objectid_             | ObjectID of the IFTTT instance that should receive the data                                                   |
|_EventName_            | name of the event, is set as the variable name in IP-Symcon if the module should create the variable          |
|_Status_               | Value to which the variable is set, true for turn on, false for turn off                                      |

In IP-Symcon we choose _Google Home_ as an option in the configuration form.

![google home](img/ifttt_google_home.png?raw=true "google home")
 
There are two possibilities, the first is to start a script, which is selected in the configuration form.

A simple script might look like this

```php
<?
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

The value in the variable _**$_IPS['State']**_ is passed to the script.

The second possibility is to have the module create a variable, then an event can be set on this variable which switches another device.


###### Example Google Home for shutter control	

First, a Trigger (This) is created using the _Google Assistant_ service.

As already described above, the same procedure
- _*New Applet*_
- select _*This*_ 
- select _Google Assistant_ as service
- To control the shutter, we select _Say a phrase with a number_ and fill in the wording to respond to the Google Home

![google assistent1](img/googleassistant1.png?raw=true "google assistent1")

![google assistent2](img/googleassistant2.png?raw=true "google assistent2")

- then press _Create Trigger_
- As _That_ we choose _Webhooks_ service
- Here we enter the following


| Property   | Value                                                                                                                                |
| :--------: | :----------------------------------------------------------------------------------------------------------------------------------: |	
|URL:        | https:// _IP-Symon Connect Adress_ .ipmagic.de/hook/IFTTT                                                                            |
|Method:     | _POST_                                                                                                                               |
|Content Type|  _application/json_                                                                                                                  |  
|Body        |  {"username":"ipsymcon","password":"meinpasswort","objectid":12345,"values":{"Level": {{NumberField}}<<<}>>>}                        |

The following data is expected:

| Value                 | explanation                                                                                                   |
| :-------------------: | :-----------------------------------------------------------------------------------------------------------: |
|\[_username_\]         | Username in the IFTTT IO is also displayed in the test environment of the instance                            |
|\[_password_\]         | Password in the IFTTT IO is also displayed in the test environment of the instance                            |
|_objectid_             | ObjectID of the IFTTT instance that should receive the data                                                   |
|_Level_                | Name of the variable, is set as the variable name in IP symcon if the module should create the variable       |

![google assistent3](img/googleassistant3.png?raw=true "google assistent3")

In IP-Symcon we choose _Google Home_ as an option in the configuration form.

![google home](img/ifttt_google_home.png?raw=true "google home")
 
There are two possibilities, the first is to start a script, which is selected in the configuration form.

A simple script might look like this

```php
<?
$level = $_IPS['Level'];
$hm_level = $level/100;
HM_WriteValueBoolean(12345, "LEVEL", $hm_level);
?>
```

The value in the variable _**$_IPS['State']**_ is passed to the script.

The second possibility is to have the module create a variable, then an event can be set on this variable which switches another device.

## 4. Function reference

### IFTTT
In the IFTTT instance, you must set whether the instance should be used only for sending, receiving or sending / receiving.
 
Triggers the event that is stored in the module.
The module uses the values or variables set in the module and sends a trigger to IFTTT.

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

For receiving data from IFTTT, variables in the module can be selected.
Make sure that the variable type corresponds to the data sent by IFTTT otherwise no data will be stored.
Alternatively, you can specify in the module that the module should create the variables. The
Variables are created from IP-Symcon automatically the first time data is received from IFTTT according to the variable type. 

### IFTTT IO
The webhook in IP-Symcon is secured by a username and password. In the IFTTT IO
If required, the default password can be changed in the IFTTT IO. The current username and the
current password is displayed in the test environment of the IFTTT instance.

## 5. Configuration:

### IFTTT IO:

| Property    | Type    | Value        | Function                                                        |
| :---------: | :-----: | :----------: | :-------------------------------------------------------------: |
| username    | string  | 		       | username for IFTTT for authentication with IP-Symcon            |
| password    | string  |              | password für IFTTT for authentication with IP-Symcon            |

username und password sind vorab eingestellt können aber individuell angepasst werden.

### IFTTT:  

| Property         | Type    | Value       | Function                                                           |
| :--------------: | :-----: | :---------: | :----------------------------------------------------------------: |
| selection        | integer |      0      | Configuration selection 1 transmit, 2 receive, 3 transmit / receive|
| countrequestvars | integer |      0      | Number of variables described by IFTTT max 15                      |
| countsendvars    | integer |      0      | Number of tags to be sent to IFTTT max 3                           |
| varvalue 1-3     | integer |      0      | ObjectID of a variable                                             |
| modulinput 1-3   | integer |      0      | Activate for module constant instead of variable                   |
| value 1-3        | string  |      0      | Constant can be stored as value in the module                      |
| requestvarvalue  | integer |    false    | ObjectID of a variable                                             |
| modulrequest     | string  |    false    | instead of your own variable, a variable is created by the module  |

## 6. Annex

###  a. Functions:

###  a. GUIDs and data exchange:

#### IFTTT IO:

GUID: `{2E91373A-E70B-46D8-99A7-71A499F6783A}` 


#### IFTTT:

GUID: `{7CBB8C1B-6A40-4DE8-9882-D505B76BA09D}`  