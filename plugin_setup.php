<?php
//$DEBUG=true;
//"/opt/fpp/www/common.php";
#include_once "functions.inc.php";
include_once 'commonFunctions.inc.php';

$pluginName = "FPP-Controller-Check";
$pluginVersion = "1.0";

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
//write settings out
if (isset($_POST['submit'])) {
    WriteSettingToFile("CNTRL_LIST", urldecode($_POST["CNTRL_LIST"]), $pluginName);
    WriteSettingToFile("EMAIL_SUBJECT", urldecode($_POST["EMAIL_SUBJECT"]), $pluginName);
}

$pluginConfigFile = $settings['configDirectory'] . "/plugin." . $pluginName;
//Set defaults
$CNTRL_LIST = [];//array
$CNTRL_LOG_FILE = "/tmp/FPP.ControllerCheck.log";
$EMAIL_SUBJECT = false;

//load settings if file exists
if (file_exists($pluginConfigFile)) {
    $pluginSettings = parse_ini_file($pluginConfigFile);

    //Read checkbox settings in
    $ENABLED = $pluginSettings['ENABLED'];
    $PING_AT_STARTUP = $pluginSettings['PING_AT_STARTUP'];
    $EMAIL_SUBJECT = $pluginSettings['EMAIL_SUBJECT'];

    //IP address list and email subject
    $CNTRL_LIST = array_map('trim', explode(",", $pluginSettings['CNTRL_LIST']));
    $EMAIL_SUBJECT = trim($pluginSettings['EMAIL_SUBJECT']);
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
            <li>This Plugin can be used to ping any number of IP's when your FPP device starts up or can serve as a page
                to check status manually
            </li>
            <li><b>1.</b> Enable Plugin
            </li>
            <li><b>2.</b> Choose whether IP's should be checked at startup
            </li>
            <li><b>3.</b> Choose whether results should be emailed to you</li>
            <li><b>4.</b> <i>Optional:</i> Set a Email subject</li>
            <li><b>5.</b> Set list of IP address, separated by a comma</li>
            <li><b>6.</b> Click 'Save Config'</li>

        </ul>
        <p>

        <p>To report a bug, please file it against the FPP-Plugin-ControllerCheck plugin project on Git:
            https://github.com/jaredb7/FPP-Plugin-ControllerCheck </p>

        <form method="post"
              action="http://<? echo $_SERVER['SERVER_NAME'] ?>/plugin.php?plugin=<? echo $pluginName; ?>&page=plugin_setup.php">

            <?
            $restart = 0;
            $reboot = 0;

            echo "<b>ENABLE PLUGIN:</b> ";
            //if($ENABLED== 1 || $ENABLED == "on") {
            //		echo "<input type=\"checkbox\" checked name=\"ENABLED\"> \n";
            PrintSettingCheckbox(" Plugin " . $pluginName, "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName, $callbackName = "");
            //	} else {
            //		echo "<input type=\"checkbox\"  name=\"ENABLED\"> \n";
            //}
            ?>
            <br>
            <?
            echo "<b>Ping Controllers at FPP Startup:</b> \n";
            PrintSettingCheckbox(" Plugin " . $pluginName, "PING_AT_STARTUP", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName, $callbackName = "");
            echo "<br><small>(use this to check controller status @ FPP Startup/pre-show)</small>";
            ?>
            <br>
            <br>
            <?

            echo "<b>Email Results:</b> \n";
            PrintSettingCheckbox(" Plugin " . $pluginName, "EMAIL_RESULTS", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName, $callbackName = "");
            echo "<br><small>(use this in conjunction with the above to have results emailed to you)</small>";
            ?>

            <br>
            <br>

            <b>Email Subject:</b>
            <input type="text" size="64" value="<? if ($EMAIL_SUBJECT != "") {
                echo $EMAIL_SUBJECT;
            } else {
                echo "Hi Admin, Results of Controller Check @ " . $settings['HostName'];
            } ?>" name="EMAIL_SUBJECT" id="EMAIL_SUBJECT">
            <br>
            <small>(use this to customize the email subject)</small>

            <br>
            <br>

            <b>IP Address(s):</b>
            <?
            echo "<input type=\"text\" name=\"CNTRL_LIST\" size=\"64\" value=\"" . implode(", ", $CNTRL_LIST) . "\"> \n";
            ?>
            <br>
            <small>(comma separated list)</small>

            <br>
            <br>

            <input id="submit_button" name="submit" type="submit" class="buttons" value="Save Config">
            <br>
            <br>
            <?
            if (file_exists($pluginUpdateFile)) {
                //echo "updating plugin included";
                include $pluginUpdateFile;
            }
            ?>
        </form>
        <br>

        <h3>Host Status:</h3>
        <table id="tblGpioOutputs" class="channelOutputTable">
            <tr class="tblheader">
                <td width="5%" align="left">#</td>
                <td width="10%" align="left">IP</td>
                <td width="10%" align="left">Status</td>
            </tr>

            <?
            //sudo rm /tmp/FPP.ControllerMonitor.log
            //Controller List is already an array, lets loop over it and ping each host
            //loop over each line and extract info
            foreach ($CNTRL_LIST as $id => $ip) {

                //ID in the list
                $host_id = $id;
                //IP address
                $host_ip = $ip;
                //host ping success
                $host_ping_success = false;

                // attempt to ping the host once
                $ping_result = exec("ping -q -c1 $ip", $ping_outcome, $ping_status);

                if ($ping_status == 0) {//0 no error
                    //ping success
                    $host_ping_success = true;
                } else {
                    //ping failed
                    $host_ping_success = false;
                }
                ?>

                <tr class="rowGpioDetails">
                    <td align="center"><? echo $host_id ?></td>
                    <td align="center"><? echo $host_ip ?></td>
                    <?
                    if ($host_ping_success == true) {
                        //class TD
                        echo "<td class='ping_success'> SUCCESS <br> <small>$ping_result</small></td>";
                    } else {
                        echo "<td class='ping_fail'> FAIL </td>";
                    }
                    ?>
                </tr>

                <?
            }
            ?>
        </table>
    </fieldset>
</div>
</html>