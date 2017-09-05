#!/bin/bash
#plugin enabled?
ENABLED=$(grep -i "^ENABLED\s*=.*" /home/fpp/media/config/plugin.FPP-Controller-Check | sed -e "s/.*=\s*//" -e 's/"//g')

#List of host IPS
CNTRL_LIST=$(grep -i "^CNTRL_LIST\s*=.*" /home/fpp/media/config/plugin.FPP-Controller-Check | sed -e "s/.*=\s*//" -e 's/"//g')

#Ping at startup?
PING_AT_STARTUP=$(grep -i "^PING_AT_STARTUP\s*=.*" /home/fpp/media/config/plugin.FPP-Controller-Check | sed -e "s/.*=\s*//" -e 's/"//g')

#Email results?
EMAIL_RESULTS=$(grep -i "^EMAIL_RESULTS\s*=.*" /home/fpp/media/config/plugin.FPP-Controller-Check | sed -e "s/.*=\s*//" -e 's/"//g')

#custom email subject
EMAIL_SUBJECT=$(grep -i "^EMAIL_SUBJECT\s*=.*" /home/fpp/media/config/plugin.FPP-Controller-Check | sed -e "s/.*=\s*//" -e 's/"//g')

#if we should ping at startup and plugin is enabled
if [ "${PING_AT_STARTUP}" == "ON" ] && [ "${ENABLED}" == "ON" ];
then
    #start by clearing the tmp log, redirect output so we don't see errors if the file doesn't exist
    sudo rm /tmp/FPP.ControllerMonitor.log &> /dev/null

    #split the list into an array, probably better ways to do this, this is just waht I found
    IFS=', ' read -r -a array <<< "${CNTRL_LIST}"

    #loop the array
    for index in "${!array[@]}"
    do
        ping -q -c1 ${array[index]} &> /dev/null
        if [ $? -eq 0 ]
        then
        echo "${index}. Host Found :: ${array[index]} @ `date`" >> /tmp/FPP.ControllerMonitor.log
        else
        echo "${index}. Ping Fail  :: ${array[index]} @ `date`" >> /tmp/FPP.ControllerMonitor.log
        fi
    done

    #Send email containing the log file
    if [ "${EMAIL_RESULTS}" == "ON" ]
    then
        #sub in a email subject is one doesn't exist for some reason
        if [ "${EMAIL_SUBJECT}" == "" ]
        then
            EMAIL_SUBJECT="Hi Admin, Results of Controller Check @ $(cat /etc/hostname 2> /dev/null)"
        fi

        mail -s "${EMAIL_SUBJECT}" root@localhost < /tmp/FPP.ControllerMonitor.log
    fi
fi