PHP Handler for CSS-X-Fire
-------------------------------------
This file adds a PHP listener for the CSS-X-Fire firefox add-on

Installation
----------------
For this to work, you need to work out 3 steps:

#### a) Configure Apache
You need to set apache to listen to port 6776, so that it will call the php file. I did this by adding these lines:

    Listen 6776
    <VirtualHost  127.0.0.1:6776>
    ServerName localhost
    DocumentRoot c:/www/css-x-fire/
    </VirtualHost>
    
#### b) Add your site to the `sites.json` file
This file is a json object containing site addresses and their corresponding location on the hard drive. For example:

    {
        "http://mysite.com" : "c:\www\mysite\"
    }
    
*An important note - you must add trailing slashes!*

#### c) Install the css-x-fire addon

Usage
---------
First of all, I highly recommend that you have a backup of you files before using this. Version controll is the bst option IMO.
Now, enable the PHP file by setting the `$on` variable to true. 
You can now start editing your files using firebug!
