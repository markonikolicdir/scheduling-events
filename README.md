# Scheduling events application

+ Symfony 5
+ Docker
+ FullCalendar

Register page, 
Login page with JWT token,
Events table(crud),
Events Calendar(JS) with CRUD api

# Setup app environment with Docker

+ install composer and docker (manually on local machine)
+ RUN composer install

# Create Docker Containers
+ docker-compose -d build
# Exec in container "scheduling-app"
+ docker exec -it scheduling-app bash
+ RUN ./setup.sh manually
