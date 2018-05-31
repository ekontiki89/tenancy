[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=sudo systemctl restart nginx
autostart=true
autorestart=true
user=forge
numprocs=8
redirect_stderr=true
stdout_logfile=/home/vagrant/Code/tenancy/storage/logs/worker.log
