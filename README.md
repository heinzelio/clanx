Clanx Hölfer DB
===============

(A Symfony project created on March 23, 2016, 7:29 pm.)

This is the webpage for clanx festival Hölfer administration.

Installation
============

A) Git Clone
------------
```
$ git clone https://github.com/chriglburri/clanx.git clanx.git
```

B) Update
---------
```
$ php composer update
```

(This installs symfony and all the required bundles. You may need to install composer for that. See [Composer Online](https://getcomposer.org/))

C) SQL Database
---------------
* Get a mysql database started

* Create the db "clanx" (preferably use phpmyadmin and create a new user account with a database.)

* Run the install script:

  ```
  $ mysql clanx -u username -p < ./sql/000install.sql
  ```

D) Create an admin user
-------------------------

```
$ php bin/console fos:user:create adminuser --super-admin
```
