#!/bin/bash
date > /home/u219184746/domains/educorerp.com/public_html/cron_live.log
cd /home/u219184746/domains/educorerp.com/public_html
/usr/bin/php artisan schedule:run >> /dev/null 2>&1
