#!/bin/bash
ssh -o "StrictHostKeyChecking no" raucaixanh04@35.186.148.73
cd /var/www/html/9mobile.vn
git pull origin dev
composer install
cd web && drush updb -y
cd themes/custom/spectre/pattern-lab && composer install && php core/console --generate
exit
