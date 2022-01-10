# Ticketsysteem

## REQUIREMENTS
- Sql file importeren die staat in assets > db
- Deze map in een php server zetten.
- Mail server instellen
- php versie 8
- CSS3
- HTML 5
- Git Bash om repo te clonen
- config file


### Mailserver config
- open de php.ini
- verander in
- SMTP=smtp.gmail.com
- smtp_port=587
- sendmail_from = my-gmail-id@gmail.com
- sendmail_path = C:\xampp\sendmail\sendmail.exe\" -t"

- verander sendmail.ini
- verander in 
- smtp_server=smtp.gmail.com
- smtp_port=587
- error_logfile=error.log
- debug_logfile=debug.log
- auth_username=my-gmail-id@gmail.com
- auth_password=my-gmail-password
- force_sender=my-gmail-id@gmail.com

### Config file
- zet je config file op zelfde niveau als de index.php
- maak een define met PROJECT_PATH met het pad van het project
- maak define('ROOT_PATH', dirname(__DIR__) . '/')
- maak define("MAIL_HEADERS", "MIME-Version: 1.0" . "\r\n"."Content-type:text/html;charset=UTF-8" . "\r\n");
- define('CONNECTION','');
- define('DATABASE','');
- define('USERNAME','');
- define('PASSWORD','');
