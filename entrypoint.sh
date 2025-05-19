# entrypoint.sh
#!/bin/sh

php /home/app/artisan optimize:clear

php /home/app/artisan optimize

#php /home/app/artisan migrate --force

php /home/app/artisan storage:unlink

php /home/app/artisan storage:link

service supervisor start

/usr/sbin/cron

php /home/app/artisan serve --host=0.0.0.0

exec "$@"
