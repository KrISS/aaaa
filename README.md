KrISS aaaa
==========
A simple and smart (or stupid) application as associative array

WIPOC project (Work In Progress and Proof Of Concept)

Presentation
------------
The goal is to build a really flexible framework that let you build
very rapidly a skeleton for apps in functional programming contrary
to [KrISS MVVM](https://github.com/kriss/mvvm) which is object-oriented programming.

Installation
------------
```bash
cd /var/www/html #it's up to you
git clone https://github.com/kriss/mvvm
```
composer is only required for running tests.
```bash
composer install
./vendor/bin/phpunit
```
if xdebug is installed you can also run code coverage
```bash
env XDEBUG_MODE=coverage ./vendor/bin/phpunit
firefox tests/html/index.html
```

Helpers
-------
helpers can be used independently outside KrISS aaaa.
Take a look at tests code to see how to use helpers.

Todo
----
A lot... well more than that... and even more...

Licence?
--------
Copyleft (ɔ) - Tontof - http://tontof.net

Use KrISS aaaa at your own risk.

[Free software means users have the four essential freedoms](http://www.gnu.org/philosophy/philosophy.html):
* to run the program
* to study and change the program in source code form
* to redistribute exact copies, and
* to distribute modified versions.
