# Mask-Development

This is the foundation of the Theme and Component driven development for the MASK Website. 

The Tech Stack: 

1. Wordpress
2. [Fusion Page Builder](https://wordpress.org/plugins/fusion/)
3. [Fusion Developer Guide](http://agencydominion.helpscoutdocs.com/)
4. [FoundationPress Theme](https://github.com/olefredrik/FoundationPress) (Extract of build tasks in /assets)
5. [Fusion Base Theme](https://github.com/agencydominion/fusion-base)

## Setup

### Downloads and Installation

1. Download and install MAMP 4 or something with equivalent PHP and MySQL versions
* https://www.mamp.info/en/downloads/
2. Clone https://github.com/MASK-Chicago/Mask-Development
3. Download Wordpress and copy all folders/files EXCEPT wp-content into Mask-Development
* https://wordpress.org/download/
4. Check your MAMP preferences:
* PHP Version 7.0.15
* Web Server Document Root should be set to your Mask-Development folder
5. Start MAMP.
 
### Database Changes

6. Open MAMP Web Start Page > Tools > phpMyAdmin
* http://localhost:8888/MAMP/index.php?page=phpmyadmin&language=English
7. Create a new database called wordpress
8. Click on the wordpress database, then click on Import tab at the top. Import the masksite gzip file.
* After it completes, you should see a bunch of tables in the database prefixed with *wp_vqbhrd142o_*
9. Click on table *wp_vqbhrd142o_options*. Click on Browse tab at the top.
10.   For table entries with option_name siteurl and home , edit their option_values to be http://localhost:8888 instead of http://masksite.flywheelsites.com
 
### Wordpress Instance

11. Go to http://localhost:8888/wp-admin/
12. Select English as language of choice
13. Enter the following for database information:
* Database name: wordpress
* Database username: root
* Database password: root
* Database host: localhost
* Table prefix: *wp_vqbhrd142o_*
14. Finish up and login with the mask admin credentials.