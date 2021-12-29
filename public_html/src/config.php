<?php

/**
 * Database connection params
 */ 
define('__DBHOST', $_ENV['DB_HOST']);
define('__DBNAME', $_ENV['DB_NAME']);
define('__DBUSER',  $_ENV['DB_USER']);
define('__DBPASSWD',  $_ENV['DB_PASSWD']);

/**
 * SMTP connection params (for contact form)
 */
define('__SMTPPASSWD', $_ENV['SMTP_PASSWD']);
define('__SMTPUSERNAME', $_ENV['SMTP_USERNAME']);
define('__SMTPHOST', $_ENV['SMTP_HOST']);
define('__SMTPPORT', $_ENV['SMTP_PORT']);
define('__SMTPEMAILADDRESS',$_ENV['BLOG_ADMIN_EMAIL']);
define('__SMTPEMAILADDRESS2', $_ENV['BLOG_ADMIN_BACKUP_EMAIL']);
define('__SMTPFULLNAME', $_ENV['BLOG_ADMIN_FULLNAME']);
