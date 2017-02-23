#!/bin/bash          
DD=$(date +%d)
MM=$(date +%m)
YYYY=$(date +%Y)

FILES_DIR='/mojo/www/bd6_files/' #point this at the most recent drupal files dir you got
ENV='dev'
SITE='9916792c-83ad-452c-a5ef-b260b8521faf'

PANT_SQL_PORT=13165

read -sp "Pastina's sql Password: " SQL_PASS
if [[ -z "$SQL_PASS" ]]; then
echo "Whoops, need password"
exit
fi

read -sp "Pantheon Password: " PANT_PASS
if [[ -z "$PANT_PASS" ]]; then
echo "Whoops, need password"
exit
fi

read -sp "Pantheon Database Password: " PANT_SQL_PASS
if [[ -z "$PANT_SQL_PASS" ]]; then
echo "Whoops, need password"
exit
fi

#scp -r  motherjones_d6.prod@web-197.prod.hosting.acquia.com:/mnt/files/motherjones_d6/backups/prod-motherjones_d6-motherjones_d6-$YYYY-$MM-$DD.sql.gz . 

echo "Prod db backup downloaded"

#gunzip prod-motherjones_d6-motherjones_d6-$YYYY-$MM-$DD.sql.gz

echo "Prod db unzipped"

#mysql -u root -p"$SQL_PASS" mjd6 < prod-motherjones_d6-motherjones_d6-$YYYY-$MM-$DD.sql

echo "prod db pushed into database"

#php migrate_database.php "$SQL_PASS"

echo "prod db moved to wp db"

#php migrate_blocks.php "$SQL_PASS"

echo "blocks added to wp db"

#mysqldump -u root -p"$SQL_PASS" pantheon_wp > migrated-wp-db-$YYYY-$MM-$DD.sql

echo "wp db dumped to file at migrated-wp-db-$YYYY-$MM-$DD.sql"

#mysql -u pantheon -p"$PANT_SQL_PASS" -h dbserver.dev.$SITE.drush.in -P "$PANT_SQL_PORT" pantheon < migrated-wp-db-$YYYY-$MM-$DD.sql

echo "wp db uploaded to pantheon"

#rsync -rtvzl --chmod=ug+rwX -e "ssh -i /home/bbreedlove/.ssh/id_rsa" --exclude=/boost* --exclude=/cache-tmpfs* --exclude=/js* --exclude=/css* motherjones_d6.prod@web-197.prod.hosting.acquia.com:/mnt/files/motherjones_d6/files/ $FILES_DIR

echo "pulled prod files to pastina"


while [ 1 ]
do
  sshpass -p "$PANT_PASS" rsync --exclude=/imagefield_thumbs* --exclude=/resized* --exclude=/imagecache* --exclude=/cache-tmpfs* --exclude=/js* --exclude=/css* --partial -rlvz --size-only --ipv4 --progress -e 'ssh -o StrictHostKeyChecking=no -p 2222' $FILES_DIR --temp-dir=../tmp/ $ENV.$SITE@appserver.$ENV.$SITE.drush.in:files/ 
if [ "$?" = "0" ] ; then
echo "rsync completed normally"
exit
else
echo "Rsync failure. Backing off and retrying..."
sleep 180
fi
  
done

