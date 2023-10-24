#! /usr/bin/sh

DOCKER_COMPOSE="docker compose"

if [ "$1" == "up" ]
then
	$DOCKER_COMPOSE up $2

elif [ "$1" == "down" ]
then
	$DOCKER_COMPOSE down $2

elif [ "$1" == "php" ]
then
	$DOCKER_COMPOSE exec php bin/console $2

fi
