# Openclassrooms PHP Blog

> Openclassrooms PHP/Symfony developer course project 5 : develop your own blog using PHP.

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/28088ec6bd5a4c14bd5eaffc0745034c)](https://app.codacy.com/gh/EstelleMyddleware/p5-blog-php?utm_source=github.com&utm_medium=referral&utm_content=EstelleMyddleware/p5-blog-php&utm_campaign=Badge_Grade_Settings)

Full documentation for this project is available [here](https://estellemyddleware.github.io/p5-blog-php/)

## Features

- Front office accessible to all users
- Back office accessible to admins only

### Front office

The website includes the following pages :

- Homepage : short introduction of myself as a web developer (name, picture, description, PDF CV, social networks you can reach me on) as well as a contact form.
- Blogposts : display all articles ordered by latest. Each blog post card must include a title, updated at date, lead paragraph & link to article.
- CRUD blogpost : individual blog post / article pages with title, headline, content, author, updated at date, comments & publish a comment form
- Register / log in form
A navbar & a footer must be present on all pages.
Footer contains a link to Admin back office.

### Back office (admin interface)

- Only specific accounts with an admin role can access the back office

## Specs

- PHP 8
- Bootstrap 5
- Bundles installed via Composer :
  - Twig
  - Autoload
  - PHP CodeSniffer
  - PHP DotEnv
  - PHP Mailer
  - Mezzio FastRoute Router
  - HttpInterop Response Sender
  - GuzzleHttp PSR7

### Success criteria

  The website must be responsive & secured. Code quality assessments done via Codacy.

### Required UML diagrams

- use case diagrams
- class diagram
- sequence diagrams

## Set up your environment

If you would like to install this project on your computer, you will first need to [clone the repo](https://github.com/EstelleMyddleware/p5-blog-php) of this project using Git.

Inside the public_html folder, you need to create a .env file (same level as .env.example) in which you need to configure the appropriate values for your blog to run :

```text
# DOCKER Database standard parameters 
DB_HOST=mysql
DB_NAME=blog
DB_USER=root
DB_PASSWD=root
#  Replace with your personal SMTP config if you have one
SMTP_PASSWD=ThepasswordlinkedtoyourSMTPaccount
SMTP_USERNAME=email@example.com
SMTP_HOST=smtp.gmail.com
SMTP_PORT=465
# Also used for SMTP as the address contact form submissions are sent to
BLOG_ADMIN_EMAIL=contact@example.com
BLOG_ADMIN_BACKUP_EMAIL=thisismyblog@gmail.com
BLOG_ADMIN_FULLNAME='Emily Smith'
```

<!-- tabs:start  -->

## **Install using Docker**

To install this project, you will need to have [Docker](<https://www.docker.com/get-started>) installed on your Computer.
Once your Docker configuration is up and ready, you can launch the project by running the following command in your terminal :

``` docker-compose up --build ```

### Install dependencies

Then, you need to open a shell linked to the newly created PHP container and run the following command to install dependencies.

``` composer install ```

You may also need to run

``` composer dump-autoload ```

Once the Docker container is built, go to <http://estellegaits:8080/> on your browser or localhost if you do not want to change your Windows vhost file, in which case, you need to update the following line from apache/apache.conf file line 1  and replace estellegaits with localhost :

```ServerName estellegaits```

Then go to <http://localhost:8080/> where you should be able to access the blog.

If you want to access the DBMS (phpmyadmin) to be able to see and manage your database, go to <http://estellegaits:8585/> or <http://localhost:8585/>, where you should be able to access the 'blog' database.

The generated Docker container uses Apache (2.4.33 Alpine), PHP8 (FPM Alpine), MySQL & phpMyAdmin.

## **Install on local webserver**

If you're unable to use Docker, you can install this project on your WAMP, Laragon, MAMP, or other local webserver.
To do so, you will first need to ensure the following requirements are met.

### Requirements

- You need to have [composer](https://getcomposer.org/download/) on your computer
- Your server needs PHP version 8.0
- MySQL or MariaDB
- Apache or Nginx

The following PHP extensions need to be installed and enabled :

- pdo_mysql
- mysqli

### Install dependencies

Before running the project, you need to run the following commands in order to install the appropriate dependencies.

``` composer install ```

### Import database files

To generate the database, you need to import the blog.sql file into your DBMS.
 
<!-- tabs:end  -->

