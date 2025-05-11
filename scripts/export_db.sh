#!/bin/bash

DATE=$(date +"%Y%m%d_%H%M%S")
FILENAME="RestaurantDB_${DATE}.sql"

docker exec bella_db mysqldump -u root -prootpass RestaurantDB > "$FILENAME"

if [ $? -eq 0 ]; then
  echo "Backup successful: $FILENAME"
else
  echo "Backup failed!"
fi
