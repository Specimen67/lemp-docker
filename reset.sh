#!/bin/bash

docker stop $(docker ps -aq)
docker rm $(docker ps -aq)
docker volume rm $(docker volume ls -q)
docker rmi $(docker images -q) --force
docker network prune -f

bash ./init.sh