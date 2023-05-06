#!/usr/bin/env bash
VERSION=$(sed -n 's/.*<version>\([^<]*\)<\/version>.*/\1/p' package-info.xml)
FILENAME=${1:-"GWBBC-$VERSION.zip"}
zip -9 -x=*.DS_Store* -Xor "$FILENAME" ./gwbbc.vendor ./src ./package-info.xml ./license.md ./readme.md
echo "$FILENAME"
