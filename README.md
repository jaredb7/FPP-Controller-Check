# FPP-Controller-Check
This plugin allows you to enter a number of IP addresses you want to check, either manually or a startup (When the FPP boots)

_Optionally results can be emailed to the email in FPP Email settings_

    1. Enable Plugin
    2. Choose whether IP's should be checked at startup
    3. Choose whether results should be emailed to you
    4. Optional: Set a Email subject
    5. Set list of IP address, separated by a comma
    6. Click 'Save Config'

I created this plugin because I run a remote show (15 minutes drive) and had a issue in 2016 where sometimes a controller would not be up or my FPP BBB wasn't up. 

My Show powers up @ 5PM each day and I hacked together a script to ping my controllers and then email the results, so I could decide whether I needed to travel out to do some maintenance.

I turned that script into a plugin to make it easier to work with.

Settings Page       
![Alt text](/images/settings_page.png?raw=true "Settings Page")

Emailed Results
![Alt text](/images/email.png?raw=true "Settings Page")
