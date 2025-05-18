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
|----- bin: create local websocket server for reaaltime editing
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

# After install required library, run the following command:
    docker-compose up --build

# Then access the program through following port:
    http://localhost:8080/

# Access to databse through following port:
    http://localhost:8081/

# The system will automatically redirect you to login page (or home page if already login)
# Prefix account for testing:
Email: thanhlongduong6a3@gmail.com
Password: @Long123

Email: duongthanhlong220805@gmail.com
Password: @Long123

# If you want to test register function, please use a VALID GMAIL for system behave properly
