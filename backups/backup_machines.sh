#!/bin/bash

BACKUP_DIR="$HOME/lemp-docker/backups"


mkdir -p "$BACKUP_DIR"

TIMESTAMP=$(date +"%d%m%y-%H%M")

BACKUP_FILE="machines_backup${TIMESTAMP}.csv"

docker exec lemp-mysql sh -c "mysql -u lemp_user -plemp_password -D lemp_db -e 'SELECT * FROM machines INTO OUTFILE \"/var/lib/mysql-files/$BACKUP_FILE\" FIELDS TERMINATED BY \",\" ENCLOSED BY \"\\\"\" LINES TERMINATED BY \"\\n\";'"

docker cp lemp-mysql:/var/lib/mysql-files/$BACKUP_FILE "$BACKUP_DIR"

docker exec lemp-mysql rm -f /var/lib/mysql-files/$BACKUP_FILE

echo "Sauvegarde terminée : $BACKUP_DIR/$BACKUP_FILE"
