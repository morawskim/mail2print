Mail2Print is PHP script, which read email from stdin, extract all supported attachments and prints them.
PHP 5.4 is required.

Instalation (Raspbian)
===========================
Download (or build) mail2print phar archive

    #build phar
    /usr/bin/php /path/to/phing-latest.phar -f /path/to/mail2printRepo/build/build.xml phar

Copy mail2print.phar to /usr/local/bin

    cp ./build/mail2print.phar /usr/local/bin
    
Copy and setup configuration for mail2print

    cp ./mail2print.ini /etc/
    vi /etc/mail2print.ini

Install fetchmail.

    apt-get install fetchmail
    
Create log file. Set owner and permission.

    touch /var/log/fetchmail
    chown fetchmail /var/log/fetchmail
    chmod 640 /var/log/fetchmail

Copy template fetchamilrc to /etc/fetchmailrc

    cp fetchmailrc.in /etc/fetchmailrc
    
Edit main fetchmail config file
 
    vi /etc/fetchmailrc

Set permissions and change owner

    sudo chown fetchmail:root /etc/fetchmailrc
    sudo chmod 660 /etc/fetchmailrc

Edit /etc/default/fetchmail

    vi /etc/default/fetchmail

Change line from
    
    START_DAEMON=no
to

    START_DAEMON=yes

Add fetchmail to start on boot

    update-rc.d fetchmail defaults

Start fetchmail
    
    service fetchmail start

You may need these PHP extensions:

* php-openssl
* php-phar (dev only)
* php-zlib (dev only)


TODO
===========================
* Logging
* Unit test
* new filters (white and blacklist email address)