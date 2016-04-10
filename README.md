Clanx Hölfer DB
===

(A Symfony project created on March 23, 2016, 7:29 pm.)

This is the webpage for clanx festival Hölfer administration.

Installation
============

A) symfony
-------
* Install symfony local on your computer according to the online documentation:

  [Symfony Book: Installation](https://symfony.com/doc/current/book/installation.html)

  Be sure that the FosUserBundle is also installed. (see
      [online documentation](https://symfony.com/doc/master/bundles/FOSUserBundle/index.html))

* Create a project

* Get a mysql database started, create the db "clanx" and run the install script:

  ´$ mysql clanx -u username -p < ./sql/000install.sql´

* Create an admin user:

  ´$ php bin/console fos:user:create adminuser --super-admin´
