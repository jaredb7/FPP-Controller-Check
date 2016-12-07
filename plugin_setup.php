<?php
//$DEBUG=true;

include_once "/opt/fpp/www/common.php";
#include_once "functions.inc.php";
include_once 'commonFunctions.inc.php';
$pluginName = "ControllerCheck";

//$DEBUG=true;
$myPid = getmypid();

$gitURL = "https://github.com/jaredb7/FPP-Controller-Check.git";


$pluginUpdateFile = $settings['pluginDirectory'] . "/" . $pluginName . "/" . "pluginUpdate.inc";


$logFile = $settings['logDirectory'] . "/" . $pluginName . ".log";


logEntry("plugin update file: " . $pluginUpdateFile);


if (isset($_POST['updatePlugin'])) {
    $updateResult = updatePluginFromGitHub($gitURL, $branch = "master", $pluginName);

    echo $updateResult . "<br/> \n";
}


if (isset($_POST['submit'])) {


    WriteSettingToFile("ENABLED", urlencode($_POST["ENABLED"]), $pluginName);
//    WriteSettingToFile("CNTRL_LIST", urlencode($_POST["CNTRL_LIST"]), $pluginName);
//    WriteSettingToFile("CNTRL_LOG_FILE", urlencode($_POST["CNTRL_LOG_FILE"]), $pluginName);

}
$ENABLED = $pluginSettings['ENABLED'];

//$CNTRL_LIST = array_map('trim', explode(",", $pluginSettings['CNTRL_LIST']));
//$CNTRL_LOG_FILE = urldecode($pluginSettings['CNTRL_LOG_FILE']);


//Set a default value
if (trim($CNTRL_LOG_FILE) == "") {
    $CNTRL_LOG_FILE = "/tmp/FPP.ControllerCheck.log";
}

?>

<html>
<head>
</head>

<div id="ControllerCheck" class="settings">
    <fieldset>
        <legend>ControllerCheck Support Instructions</legend>

        <p>Known Issues:
        <ul>
            <li>NONE</li>
        </ul>

        <p>Configuration:
        <ul>
            <li>Simply enter all the IP addresses of controllers or other FPP devices (Slave BBB) that you check the
                online state of
            </li>
        </ul>
        <p>


        <p>To report a bug, please file it against the ControllerCheck plugin project on Git:
            https://github.com/jaredb7/FPP-Plugin-ControllerCheck
            <form method="post"
                  action="http://<? echo $_SERVER['SERVER_NAME'] ?>/plugin.php?plugin=<? echo $pluginName; ?>&page=plugin_setup.php">


                <?

                $restart = 0;
                $reboot = 0;

                echo "ENABLE PLUGIN: ";

                if ($ENABLED == 1 || $ENABLED == "on") {
                    echo "<input type=\"checkbox\" checked name=\"ENABLED\"> \n";
//PrintSettingCheckbox("Radio Station", "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName = $pluginName, $callbackName = "");
                } else {
                    echo "<input type=\"checkbox\"  name=\"ENABLED\"> \n";
                }


                echo "<p/> \n";
                echo "<p/> \n";

//                echo "IP Address list, comma separated: \n";
//                echo "<input type=\"text\" name=\"CNTRL_LIST\" size=\"16\" value=\"" . implode(", ", $CNTRL_LIST) . "\"> \n";

                echo "<p/> \n";

//                echo "Ping results path and Name (/tmp/FPP.ControllerCheck.log) : \n";
//                echo "<input type=\"text\" name=\"CNTRL_LOG_FILE\" size=\"16\" value=\"" . $CNTRL_LOG_FILE . "\"> \n";

                ?>
        <p/>
        <input id="submit_button" name="submit" type="submit" class="buttons" value="Save Config">
        <?
        if (file_exists($pluginUpdateFile)) {
            //echo "updating plugin included";
            include $pluginUpdateFile;
        }
        ?>
        </form>
    </fieldset>
</div>
<br/>
</html>