#!/bin/bash

BACKUP_SCRIPT="$HOME/lemp-docker/backups/backup_machines.sh"
UPDATE_SCRIPT="$HOME/lemp-docker/daily_updates.sh"
LOG_DIR="$HOME/lemp-docker/logs"
BACKUP_DIR="$HOME/lemp-docker/backups"
UPLOAD_DIR="$HOME/lemp-docker/php/uploads"
mkdir -p "$BACKUP_DIR" "$LOG_DIR"

if [[ ! -f "$BACKUP_SCRIPT" || ! -f "$UPDATE_SCRIPT" ]]; then
  echo "Les scripts nécessaires n'existent pas :"
  [[ ! -f "$BACKUP_SCRIPT" ]] && echo "- $BACKUP_SCRIPT est manquant."
  [[ ! -f "$UPDATE_SCRIPT" ]] && echo "- $UPDATE_SCRIPT est manquant."
  exit 1
fi

CRON_JOBS=$(cat <<EOF
*/30 * * * * $BACKUP_SCRIPT
5 0 * * * $UPDATE_SCRIPT >> $LOG_DIR/update_reservations.log 2>&1
0 2 * * * find $BACKUP_DIR -type f -name 'machines_backup*' -mtime +14 -exec rm -f {} \;
EOF
)

(crontab -l 2>/dev/null; echo "$CRON_JOBS") | sort -u | crontab -

echo "Les tâches cron ont été configurées avec succès."


docker-compose up --build -d

chmod 777 $UPLOAD_DIR