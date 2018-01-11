cd /opt/pho-cli
sudo composer install
sudo echo -e "[program:pho]\ncommand=php /opt/pho-cli/bin/pho.php serve /opt/pho-cli/data\nuser=pho\nautostart=true\nautorestart=true\nstderr_logfile=/var/log/pho/long.err.log\nstdout_logfile=/var/log/pho/long.out.log" > /etc/supervisor/conf.d/pho.conf
sudo supervisorctl reload
sudo supervisorctl update
sudo systemctl enable supervisor
sudo service supervisor start