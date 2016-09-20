#TO USE: fill out the variables below, run in bash shell, enter password, wait
SITE='268f0bbd-c0a4-4874-a8c8-045ceef9cd5f' #this can be found in the url when you're logged in to pantheon and looking at the site you want to import for
ENV='dev' #same place as the site info, but right after the hashtag
FILES_DIR='./bd6_files/*' #point this at the most recent drupal files dir you got

read -sp "Your Pantheon Password: " PASSWORD
if [[ -z "$PASSWORD" ]]; then
echo "Whoops, need password"
exit
fi

while [ 1 ]
do
  sshpass -p "$PASSWORD" rsync --partial -rlvz --size-only --ipv4 --progress -e 'ssh -o StrictHostKeyChecking=no -p 2222' $FILES_DIR --temp-dir=../tmp/ $ENV.$SITE@appserver.$ENV.$SITE.drush.in:files/ 
if [ "$?" = "0" ] ; then
echo "rsync completed normally"
exit
else
echo "Rsync failure. Backing off and retrying..."
sleep 180
fi
done

