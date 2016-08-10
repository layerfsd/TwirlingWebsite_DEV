#!/bin/bash

WEB_PATH='/alidata/www/tl-dev'
WEB_USER='www'
WEB_USERGROUP='twirling'

echo "Start deployment"
cd $WEB_PATH
echo "pulling source code..."
git reset --hard origin/master
git clean -f
git pull origin master
echo "changing permissions..."
chown -R $WEB_USER:$WEB_USERGROUP $WEB_PATH
echo "Finished."