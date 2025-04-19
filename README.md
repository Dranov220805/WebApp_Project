# Project Structure
-- app: handle system's logic  
|----- controllers: only use to directional (always note the route above the function)
|----- core: only use for Json Web Token function
|----- middlewares: only use to process the pre-request  
|----- models: only use to mapping with database  
|----- pattern: pattern code only  
|----- repository: get the necessary data from database only  
|----- services: process the logical of the function    
|----- .htaccess  
-- config: handle system's configuration  
|----- DatabaseManager.php: connect to database only  
|----- .htaccess 
-- data: saving documentation about the project (Class Diagram, sql file)
-- public: vendor for static informations  
|----- css: (main, base, responsive, grid,...)  
|----- img: divided by function  
|----- js: use 'module ES6' and Class Component  
 
-- route: redirect by router  
|----- .htaccess  
-- views: views only  
|----- .htaccess  
-- index.php  
-- .htaccess  

# This project is using composer, please install below library for system behave properly:
composer install            
composer require ramsey/uuid
composer require cloudinary/cloudinary_php 
composer require vlucas/phpdotenv   
composer require firebase/php-jwt      
