#!/bin/bash          
DD=$(date +%d -d "1 day ago")
MM=$(date +%m -d "1 day ago")
YYYY=$(date +%Y -d "1 day ago")
#DD=$(date +%d)  #this is for the same day, uncomment me if we're pushin for real
#MM=$(date +%m)
#YYYY=$(date +%Y 

FILES_DIR='/mojo/www/bd6_files/' #point this at the most recent drupal files dir you got
ENV='dev'
# Pantheon master stage
#SITE='9916792c-83ad-452c-a5ef-b260b8521faf'
#
#PANT_SQL_PORT=13165

# Pantheon dev-dev
SITE='fe3de411-a1e8-4729-8882-fbb44bb58aa5'

PANT_SQL_PORT=12318

read -sp "Pastina's sql Password: " SQL_PASS
if [[ -z "$SQL_PASS" ]]; then
echo "Whoops, need password"
exit
fi

echo "\n"

read -sp "Pantheon Password: " PANT_PASS
if [[ -z "$PANT_PASS" ]]; then
echo "Whoops, need password"
exit
fi

echo "\n"

read -sp "Pantheon Database Password: " PANT_SQL_PASS
if [[ -z "$PANT_SQL_PASS" ]]; then
echo "Whoops, need password"
exit
fi
echo "\n"

scp -r  motherjones_d6.prod@web-197.prod.hosting.acquia.com:/mnt/files/motherjones_d6/backups/prod-motherjones_d6-motherjones_d6-$YYYY-$MM-$DD.sql.gz . 

echo "Prod db backup downloaded"
echo "\n"


gunzip prod-motherjones_d6-motherjones_d6-$YYYY-$MM-$DD.sql.gz

echo "Prod db unzipped"
echo "\n"


php migrate_links.php "$SQL_PASS" #redirects must be in place for toc pages in migrate, below

echo "redirects added to wp db"
echo "\n"

mysql -u root -p"$SQL_PASS" mjd6 < prod-motherjones_d6-motherjones_d6-$YYYY-$MM-$DD.sql

echo "prod db pushed into database"
echo "\n"


php migrate_database.php "$SQL_PASS"

echo "prod db moved to wp db"
echo "\n"


php migrate_blocks.php "$SQL_PASS"

echo "blocks added to wp db"
echo "\n"

php migrate_options.php "$SQL_PASS"

echo "options updated in wp db"
echo "\n"

mysqldump -u root -p"$SQL_PASS" pantheon_wp > migrated-wp-db-$YYYY-$MM-$DD.sql

echo "wp db dumped to file at migrated-wp-db-$YYYY-$MM-$DD.sql"
echo "\n"


mysql -u pantheon -p"$PANT_SQL_PASS" -h dbserver.dev.$SITE.drush.in -P "$PANT_SQL_PORT" pantheon < migrated-wp-db-$YYYY-$MM-$DD.sql

echo "wp db uploaded to pantheon"
echo "\n"


rsync -rtvzl --ignore-existing --chmod=ug+rwX -e "ssh -i /home/bbreedlove/.ssh/id_rsa" --exclude=/boost* --exclude=/cache-tmpfs* --exclude=/js* --exclude=/css* motherjones_d6.prod@web-197.prod.hosting.acquia.com:/mnt/files/motherjones_d6/files/ $FILES_DIR

echo "pulled prod files to pastina"
echo "\n"



PSYNCED=false
until [ "$PSYNCED" = true ] ;
do
  sshpass -p "$PANT_PASS" rsync --ignore-existing --exclude=/imagefield_thumbs* --exclude=/resized* --exclude=/cloudfront_queue* --exclude=/imagecache* --exclude=/cache-tmpfs* --exclude=/js* --exclude=/css* --partial -rlvz --no-links --size-only --ipv4 --progress -e 'ssh -o StrictHostKeyChecking=no -p 2222' $FILES_DIR --temp-dir=../tmp/ $ENV.$SITE@appserver.$ENV.$SITE.drush.in:files/ 
if [ "$?" = "0" ] ; then
echo "rsync completed normally"
PSYNCED=true
else
echo "Rsync failure. Backing off and retrying..."
sleep 180
fi
  
done
echo "\n"



expect <<- DONE

spawn /home/bbreedlove/vendor/bin/terminus remote:wp $SITE.$ENV -- media regenerate --only-missing

expect  "*?assword:*" { send "$PANT_PASS\r"  }

expect "y/n*" { send "y\r" } 

expect {
  timeout {send "\r"; exp_continue}
  eof
}


DONE

