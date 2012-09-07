GetSimple Cloud Backup
======================

Provides automated backups of [GetSimple](http://get-simple.info/) websites to Dropbox and Google Drive.

**Fair Warning**: this plugin is very much a work in progress. That means it probably isn't ready for public consumption just yet, and that unless you aren't comfortable with PHP, OAuth, or developing with 3rd-party APIs, other options might be better suited. 

If you're comfortable working with not-quite-production-ready code, though, please feel free to fork and contribute!

Questions? Comments? Hit me up [@rjzaworski](http://twitter.com/rjzaworski).

### Install

Clone the repo and copy the relevant pieces to the plugins directory of your GetSimple site:

    $ git clone https://github.com/rjz/getsimple-cloudbackup.git
    $ cp getsimple-cloudbackup/cloudbackup* /path/to/website/plugins

Login to getsimple administration, and click the "Cloud Backup" link in the "Backups" tab to configure cloud backups.

##### Backups via Dropbox

While this plugin remains under development, backups via dropbox require a custom Dropbox application.

1. Login to [Dropbox](http://dropbox.com)
2. [Create a new app](https://www.dropbox.com/developers/apps) using the developer dashboard
3. Copy the app key and secret into the corresponding fields in `cloudbackup/providers/dropbox.php`

(Don't forget to check out BenTheDesigner's [https://github.com/BenTheDesigner/Dropbox](Dropbox SDK on Github). We'll be using it heavily.)

##### Backups via Google Drive

1. Follow [google's instructions](https://developers.google.com/drive/quickstart#enable_the_drive_api) to create an application using the Drive API
2. Copy the app id and secret into the corresponding fields in `cloudbackup/providers/googledrive.php`

### What works...

The plugin currently ships with:

* Working strategy for creating site archive via tarball
* Working strategies for storing archive to Google Drive and Dropbox
* Automated backup scheduling

...in other words, the bare minimum needed to back up a website.

### What doesn't...

Features that are stubbed but nott fully realized yet include:

* A pretty admin interface
* The FTP backup strategy
* The Zip archive strategy
* E-mail status notifications
* Better testing

This list will grow shorter as I find the time.

License
-------

3rd-party libs/SDKs (included) are subject to their respective licenses.

All original code is released under the JSON license.
