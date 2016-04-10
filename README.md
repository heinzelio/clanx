Clanx Hölfer DB
===============

(A Symfony project created on March 23, 2016, 7:29 pm.)

This is the webpage for clanx festival Hölfer administration.

Installation
============

A) symfony
----------
* Install symfony local on your computer according to the online documentation:

  [Symfony Book: Installation](https://symfony.com/doc/current/book/installation.html)

  Be sure that the FosUserBundle is also installed. (see
      [online documentation](https://symfony.com/doc/master/bundles/FOSUserBundle/index.html))

* Create a project

B) MySql
--------

* Get a mysql database started

* Create the db "clanx" (preferably use phpmyadmin and create a new user account with a database.)

* Run the install script:

  ```
  $ mysql clanx -u username -p < ./sql/000install.sql
  ```

C) Create an admin user
-------------------------

```
$ php bin/console fos:user:create adminuser --super-admin
```
