# CountriES

Installation

 - Clone git repo
 
 `git clone https://github.com/eliasnacher/countries/`

 - Get into the project
 
 `cd countries`

 - Create dorcker containers
 
 `docker-compose up -d`
 
 - Execute database migrations (You can check the container name with `docker container ls`)
 
 `docker exec countries_web_1 php bin/console doctrine:migrations:migrate`
 
 - Now you can open the project in your browser
 
 `http:\\localhost:8080`
