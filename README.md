# CES

Installation

- Clone git repo.

`git clone https://github.com/eliasnacher/countries/`

- Enter the project.

`cd countries`

- Create dorcker containers.

`docker-compose up -d`

- Run database migrations (You can check the container name with `docker container ls`).

`docker exec countries_web_1 php bin/console doctrine:migrations:migrate`

- Now you can open the project in your browser.

`http:\\localhost:8080`

Testing

- To test, run the following command.

`docker exec countries_web_1 php bin/phpunit`