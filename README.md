phpFramework
============

MVC based php Framework

This is registry based framework where you put your controllers and model to utilize the power of registry.

Documentation comming soon.


# phpFramework Installation Instructions

This framework works on principal of MVC (Model View Controller). It is in development stage and is not fully stable and baked.
Although it stable enough to built good interactive and powerfull webpps.

Prerequisites: Apache 2.2+, MySQL 5+, PHP 5.3+, reWriteMod in apache Enabled.

Getting Started 

1. Download or fork the framework from https://github.com/garvitdelhi/phpFramework

2. phpFramework requires Apache, MySQL, and PHP to function. I will not cover here how
	to setup and install Apache, MySQL, or PHP. But, what you will need to do is place the code you
	have downloaded to the appropriate place on your filesystem.

3. Create a .htaccess file (you may already have it if you downloaded it from Git, it might be hidden).

4. Create a new MySQL Database on your server. Assign a new user to the database. Edit
	the db.conf file.

5. Use db.sql and import in your MySQL database which you created in last step to start your framework.

6. Go to settings table in database and change the siteurl of your siteurl where you are installing the framework. If you installng 
	it on localhost then type : http://localhost/

7. Go to controllers table in database and insert controller's name and active status it has to be 1 if active and 0 otherwise
	leave the priority as 0.

8. DON'T Edit registry folder files and index.php

9. Read structer.txt to readabout frameworks structure.

10. Edit framwwork.conf file after reading structure.txt.

11. After reading structure.txt you are ready to go and build your app.

12. For information on API's go to api/ folder.
