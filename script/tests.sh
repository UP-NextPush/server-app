#!/bin/bash

##
# Script to test nextpush.
# Usage : ./tests.sh http://your.domain.tld/
##

ok() {
    echo "[*] $@"
}

step() {
    echo "[+] $@"
}

error() {
    echo "[!] $@"
}

quit() {
    error "$@"
    exit 0
}


check_server() {
    step "Checking URL"
    curl -s $URL/status.php | grep Nextcloud >/dev/null || quit "Nextcloud server not found"
    ok "Nextcloud server found."
}

test_matrix_gateway() {
    step "Checking matrix gateway"
    curl -s $URL/index.php/apps/uppush/gateway/matrix | grep '{"unifiedpush":{"gateway":"matrix"}}' >/dev/null || error "Can't access non-setup matrix gateway."
    curl -s $URL/_matrix/push/v1/notify | grep '{"unifiedpush":{"gateway":"matrix"}}' >/dev/null
    if [ $? -ne 0 ]; then
        error "Matrix gateway is not setup. cf. https://github.com/UP-NextPush/server-app#gateways"
    else
        curl -s -X POST --data '{"notification":{"devices":[{"app_id":"abcd","pushkey":"abcd"}]}}' $URL/_matrix/push/v1/notify | grep '{"rejected":\["abcd"\]}' >/dev/null
        if [ $? -ne 0 ]; then
            error "Error while notifying to the matrix gateway"
        else
            ok "Matrix gateway correctly set up"
        fi
    fi
}

test_sync() {
    step "Testing sync"
    echo -n "Username: "
    read USER
    echo -n "Password: "
    read -s PASSWORD
    echo

    step "Create device"
    DEVICE_ID=$(curl -s -u "$USER:$PASSWORD" -X PUT --data "deviceName=device-test" "$URL/index.php/apps/uppush/device/" | jq -r '.deviceId') || quit "Cannot create device"
    step "Create app"
    APP_ID=$(curl -s -u "$USER:$PASSWORD" -X PUT --data "deviceId=$DEVICE_ID&appName=app-test" "$URL/index.php/apps/uppush/app/" | jq -r '.token') || quit "Cannot create app"
    step "Sync"
    ( curl -s --max-time 1 "$URL/index.php/apps/uppush/device/$DEVICE_ID" & curl -s --data "TEST" "$URL/index.php/apps/uppush/push/$APP_ID" ) | grep "VEVTVA==" >/dev/null || quit "Cannot receive notification"
    ok "Synchronization worked"
}

[ $# -eq 1 ] || quit "Usage: $0 http://your.domain.tld"

URL="$1"

check_server
test_matrix_gateway

echo "Testing sync ? [Yy/Nn]"
read sync
[ "$sync" == "y" ] || [ "$sync" == "Y" ] && test_sync
