# Ticketsysteem

## REQUIREMENTS
- Sql file importeren die staat in assets > db
- Deze map in een php server zetten.
- Mail server instellen
- php versie 8
- CSS3
- HTML 5
- Git Bash om repo te clonen


### mailserver config
- open de php.ini
- verander in
- SMTP=smtp.gmail.com
- smtp_port=587
- sendmail_from = my-gmail-id@gmail.com
- sendmail_path = C:\xampp\sendmail\sendmailexe\" -t"

- verander sendmail.ini
- verander in 
- smtp_server=smtp.gmail.com
- smtp_port=587
- error_logfile=error.log
- debug_logfile=debug.log
- auth_username=my-gmail-id@gmail.com
- auth_password=my-gmail-password
- force_sender=my-gmail-id@gmail.com
