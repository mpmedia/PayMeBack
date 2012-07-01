PayMeBack
=========

Application for managing account between two persons.

PayMeBack rely on different open-source library:

* [Silex](http://silex.sensiolabs.org/)
* [Twig](http://twig.sensiolabs.org/)
* [Propel](http://www.propelorm.org/)

Configuration
-------------

Copy web/.htaccess.dist file to web/.htaccess and customize it if necessary with your server configuration.

Copy resources/propel/runtime-conf.xml.dist file to resources/propel/runtime-conf.xml and customize it with your database information.

Copy resources/bin/bootstrap.dist file to resources/bin/bootstrap and customize it with your database name.

Installation
------------

1. Go to your project directory and run `git clone https://github.com/maxailloud/PayMeBack.git`
2. Download the [`composer.phar`](http://getcomposer.org/composer.phar) executable
3. Run Composer to get the dependencies: `php composer.phar install`
4. Run `chmod +x resources/bin/bootstrap`, `resources/bin/bootstrap` and enter your password when ask


License
-------

PayMeBack is licensed under the WTFPL License - see the LICENCE file for details