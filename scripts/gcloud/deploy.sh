#!/bin/bash
ssh -o "StrictHostKeyChecking no" raucaixanh04@35.186.148.73 << EOF
  cd /var/www/html/9mobile.vn
  git fetch origin
  git checkout dev
  git clean -df web/themes/custom/spectre/
  git reset --hard
  git merge origin/dev
  composer install
  cd web && drush updb -y
  drush cim --source=../config -y
  cd themes/custom/spectre/.npm && npm install && gulp sass-dev && gulp build-pattern-lab && gulp pl-generate
  drush cache-rebuild all
  exit
EOF
