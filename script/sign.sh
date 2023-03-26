#!/bin/bash

[ $# -ne 1 ] && exit 1

VERSION="$1"

[ -f "$VERSION.tar.gz" ] || wget "https://codeberg.org/NextPush/uppush/archive/$VERSION.tar.gz"
echo "Lien:"
echo "https://codeberg.org/NextPush/uppush/archive/$VERSION.tar.gz"
echo "Signature:"
openssl dgst -sha512 -sign ~/.nextcloud/certificates/uppush.key "$VERSION.tar.gz" | openssl base64

