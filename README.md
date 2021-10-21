# p5-blog-php

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
 - PHP 7.4
 - Bootstrap 5
 - Bundles installed via Composer :
    - Twig
    - Autoload
    
### Success criteria
  The website must be responsive & secured. Code quality assessments must be performed via Symfony Insight or Codacy.
### Required UML diagrams :
- use case diagrams
- class diagram
- sequence diagrams
