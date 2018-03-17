#!/bin/bash
ssh -o "StrictHostKeyChecking no" raucaixanh04@35.186.148.73 << EOF
  cd /var/www/html/9mobile.vn
  git checkout dev
  git pull origin dev
  composer install
  cd web && drush updb -y
  drush cim --source=../config -y
  cd themes/custom/spectre/pattern-lab && composer install && php core/console --generate
  drush cc all
  exit
EOF
