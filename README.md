# TodoApp
Symfony 3

Version: BETA

Simple todo application. 
You can add and manage tasks and categories in simple way.

### TodoApp screens:
![alt tag](https://raw.githubusercontent.com/marcinkazmierski/TodoApp/master/_data/login.png)

![alt tag](https://raw.githubusercontent.com/marcinkazmierski/TodoApp/master/_data/dashboard.png)


### How to install?

- clone a project
- download composer requires:
```
composer install
```
- create database:
```
php bin/console doctrine:schema:update --force
```
- build assets:
```
php bin/console assets:install web --symlink
```
- run cron tasks:
```
php bin/console app:cron:task:reminder --max-runtime 30
```
