#!/bin/bash

APP_BASE=`dirname $(readlink -f $0)`
APP_BASE=`dirname ${APP_BASE}`

cd ${APP_BASE}
echo ${APP_BASE}

echo "Checking the local config file"
[ -d ${APP_BASE}/app/Config/app_config.php ] || touch ${APP_BASE}/app/Config/app_config.php
chmod 666 ${APP_BASE}/app/Config/app_config.php
echo "Checking that the proper links exist"
[ -e ${APP_BASE}/app/webroot/assets ] || [ -h ${APP_BASE}/app/webroot/assets ] && rm ${APP_BASE}/app/webroot/assets
ln -s ../../Vendor ${APP_BASE}/app/webroot/assets
[ -e ${APP_BASE}/plugins ] || [ -h ${APP_BASE}/plugins ] && rm ${APP_BASE}/plugins
ln -s Plugin ${APP_BASE}/plugins
[ -e ${APP_BASE}/app/View/Themed ] || [ -h ${APP_BASE}/app/View/Themed ] && rm ${APP_BASE}/app/View/Themed
ln -s ../../Plugin/Utilities/View/Themed ${APP_BASE}/app/View/Themed
echo "Checking the permissions of the TEMP directories/files"
find ${APP_BASE}/app/tmp -type d -exec chmod -f 777 {} + 
find ${APP_BASE}/app/tmp -type f -exec chmod -f 666 {} + 
echo "Checking the permissions of the USER ADDED directories/files"
find ${APP_BASE}/app/webroot/files -type d -exec chmod -f 777 {} + 
find ${APP_BASE}/app/webroot/files -type f -exec chmod -f 666 {} + 
cd ${APP_BASE}/app
echo "Updating the database schema."
Console/cake schema update -y
cd ${APP_BASE}

echo "Scanning all of the plugins to see if they have post command updates as well"

for PLUGIN in ${APP_BASE}/Plugin/*; do
    if [[ -d ${PLUGIN} ]]; then
		BASENAME=`basename ${PLUGIN}`
		echo "------------- Checking Plugin: ${BASENAME} --------------------" 
		echo "---------------------------------------------------------------"
		echo " "
		
		if [[ -f "${PLUGIN}/bin/post-install-cmd.sh" ]]; then
			"${PLUGIN}/bin/post-install-cmd.sh"
		fi 
    fi
done