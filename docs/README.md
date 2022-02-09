# Openclassrooms Blog PHP

> An awesome project.

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/28088ec6bd5a4c14bd5eaffc0745034c)](https://app.codacy.com/gh/EstelleMyddleware/p5-blog-php?utm_source=github.com&utm_medium=referral&utm_content=EstelleMyddleware/p5-blog-php&utm_campaign=Badge_Grade_Settings)

Openclassrooms project : develop your own blog using PHP.

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
  - Mezzio FastRoute Router
  - HttpInterop Response Sender
  - GuzzleHttp PSR7

### Success criteria

  The website must be responsive & secured. Code quality assessments done via Codacy.

### Required UML diagrams

- use case diagrams
- class diagram
- sequence diagrams

## Install using Docker

To install this project, you will need to have Docker (<https://www.docker.com/get-started>) installed on your Computer.
Once your Docker configuration is up and ready, you can launch the project by running the following command in your terminal :

``` docker-compose up --build ```

Once the Docker container is built, go to <http://estellegaits:8080/> on your browser or localhost if you do not want to change your Windows vhost file, in which case, you need to update the following line from apache/apache.conf file line 1  and replace estellegaits with localhost :

```ServerName estellegaits```

Then go to <http://localhost:8080/> where you should be able to access the blog.

If you want to access the DBMS (phpmyadmin), go to <http://estellegaits:8585/> or <http://localhost:8585/>, where you should be able to access the 'blog' database.
