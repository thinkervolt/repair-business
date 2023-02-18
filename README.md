# REPAIR-BUSINESS

*Manage your Repair Business - Repairs, Invoices, Payments & Reports. Multi-User Management, ready for scalability.*

### ABOUT 

This Web Application was created to satisfy any repair business, it keeps customers, repairs and invoices well organized. Multi-user and user permissions makes life easy.
working in a team, with user control you can see who is working on the repair and prevent access to administration views.

It has all basic stuff reports, invoices, payments and settings, but the main goal is work in a **fast business environment**, when you need to print a receipt in minutes to satisfied long quantity of customers.

### REQUIREMENTS

REPAIR-HERO was made with Laravel 7 and tested on PHP-7 and PHP-8 without any issue.

* PHP7 - PHP8
* mySQL Database

### INSTALATION

* Create your environment file and setup your database connection to prevent installation erros.
* Generate your APP_KEY
* Run **composer-install** 
* Seed Database for **user**  and **configuration table**.

### DATABASE AUTO-BACKUP

If you setup your Task Scheduling with Cron the application will backup everyday the database to /storage/app/database-plan, you can run `php artisan database:plan` to test it. 

*Everything was made with standard code keeping in mind that it will be use on different kind of repair business and is ready to be customize, front end is open to be changed easily  and backend is easy to read like another Laravel App*

### DOCKER

Perform all basic operations `composer install, app_key generation and storage permissions` before build and run docker compose file inside of `docker` directory.
Docker Compose file has one-time command that will run migrations and database seed, make sure that you uncomment it until database seed is complete.
