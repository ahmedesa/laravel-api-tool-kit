docker_compose = docker-compose -f docker-compose.yml

php_container = php

up:
	$(docker_compose) up --remove-orphans
upd:
	$(docker_compose) up -d --remove-orphans
down:
	$(docker_compose) down
bashroot:
	$(docker_compose) exec $(php_container) sh