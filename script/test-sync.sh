#!/bin/bash

##
# Script to test nextpush :
# 1. Register a new device
# 2. Register a new app
# 3. Sync
#
# URL, USER and PASSWORD need to be edited
##

URL=http://127.0.0.1:3000
USER=admin
PASSWORD=admin

echo "[+] Create device"
DEVICE_ID=$(curl -u "$USER:$PASSWORD" -X PUT --data "deviceName=device-test" $URL/index.php/apps/uppush/device/ | jq -r '.deviceId')
echo "[+] Create app"
APP_ID=$(curl -u "$USER:$PASSWORD" -X PUT --data "deviceId=$DEVICE_ID&appName=app-test" $URL/index.php/apps/uppush/app/ | jq -r '.token')
echo "[i] To send a msg:"
echo "curl --data \"your-message-here\" $URL/index.php/apps/uppush/push/$APP_ID"
echo "[+] Sync"
curl "$URL/index.php/apps/uppush/device/$DEVICE_ID"
