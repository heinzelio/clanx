Clanx Hölfer DB
===============

(A Symfony project created on March 23, 2016, 7:29 pm.)

This is the webpage for clanx festival Hölfer administration.

Installation
============
* create a database using phpmyadmin

* find out what port your mysql runs on:

```
$ mysql -u clanx -p
$ Enter password: ********
mysql> SHOW VARIABLES LIKE 'port';
```

* Clone the git repository to your development environment:

```
$ git clone https://github.com/chriglburri/clanx.git clanx.git && cd clanx.git
```

* If not yet done install composer

* call composer update (this is going to take a while since the whole framework will be downloaded

```
$ php /c/bitnami/wamp/php/composer.phar install
```

* Add symfony parameters:

```
←[30;46mdatabase_host←[39;49m (←[33m127.0.0.1←[39m): 127.0.0.1
←[30;46mdatabase_port←[39;49m (←[33mnull←[39m): 3306
←[30;46mdatabase_name←[39;49m (←[33msymfony←[39m): clanx
←[30;46mdatabase_user←[39;49m (←[33mroot←[39m): clanx
←[30;46mdatabase_password←[39;49m (←[33mnull←[39m): *****

←[30;46mmailer_transport←[39;49m (←[33msmtp←[39m): smtp
←[30;46mmailer_host←[39;49m (←[33m127.0.0.1←[39m): asmtp.mail.mymailprovider.com
←[30;46mmailer_user←[39;49m (←[33mnull←[39m): clanx@mymailprovider.com
←[30;46mmailer_password←[39;49m (←[33mnull←[39m): **********

←[30;46msecret←[39;49m (←[33mThisTokenIsNotSoSecretChangeIt←[39m):blablablaRandom_Ca_32_Chars
```

* clear the cache for the productive environment

```
$ php bin/console cache:clear -e prod
```

* create a symbolic link from your apache htdocs folder to your development environment.

* visit the page:

```
http://localhost/clanx/web/app_dev.php
```
