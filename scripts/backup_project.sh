#!/bin/bash

SOURCE_DIR="/home/bellapizza/bella-pizza-docker"
BACKUP_NAME="project_backup_$(date +%Y%m%d_%H%M%S).zip"
DEST_DIR="/home/bellapizza/bella-pizza-docker/backups/"


zip -r "$DEST_DIR/$BACKUP_NAME" "$SOURCE_DIR"


if [ $? -eq 0 ]; then
  echo "Backup created: $DEST_DIR/$BACKUP_NAME"
else
  echo "Backup failed!"
fi
