## chorechart App

This is a todo style app that uses Laravel for the back end and vue.js for the front end.
It features two types of users; admins and regular users. Admins have the ability to manage
users, create and assign chores, even to themselves.

A user is assigned chores and upon completion, they submit them for inspection
by an admin and receive points for chores that have been verified complete.

## Frameworks
Laravel <a href="https://www.laravel.com">Laravel.com</a>
vue js <a href="https://www.vuejs.com">vuejs.com</a>

## Features
* 
* create and manage chores
* manage users
* points system and rewards

## installation
Ensure you have php version 8 or higher installed


ensure you have node version 18 or higher installed


ensure you have MySQL version 8 or higher


Clone down the project
```
git clone git@github.com:adammakinson/chorechart.git
```

make a copy of .env.example as .env and update variables appropriately

Install PHP dependencies
```
composer install
```

Create a database for the first admin user in MySQL:
```
CREATE DATABASE <database name>;
```

Create a .env from the sample .env and update the pertinent entries


Run migrations
```
php artisan migrate
```

Run the seeders:
```
php artisan db:seed
```

Install NPM dependencies
```
npm install
```

Build the front end
```
npm run prod
```


## Deployment notes

You can follow the general instructions at https://community.synology.com/enu/forum/1/post/133463. Note, you will have to create the mariadb users from the
command line (SSH into environment).

One thing to note on synology is that since it uses php versioning, you'll
have to note the php version used by the web service app and run the artisan
commands according to that, for example php80 artisan... for php 8.0