WEB_USER='root'
WEB_USERGROUP='twirlingvr'

echo "Start deployment"
cd '/alidata/www'
echo "pulling source code..."
git reset --hard origin/master
git clean -f
git pull origin master
echo "changing permissions..."
#chown -R $WEB_USER:$WEB_USERGROUP $WEB_PATH
echo "Finished."