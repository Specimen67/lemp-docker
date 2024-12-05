#!/bin/bash

docker exec -i lemp-mysql mysql -u root -proot_password -e "SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE lemp_db.machines; SET FOREIGN_KEY_CHECKS = 1;"