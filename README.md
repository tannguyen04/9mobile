# 9mobile


[![CircleCI](https://circleci.com/gh/tannguyen04/9mobile.svg?style=shield)](https://circleci.com/gh/tannguyen04/9mobile)
[![Dashboard 9mobile](https://img.shields.io/badge/dashboard-9mobile-yellow.svg)](https://dashboard.pantheon.io/sites/9806d555-c138-4987-b536-a670efd06214#dev/code)
[![Dev Site 9mobile](https://img.shields.io/badge/site-9mobile-blue.svg)](http://dev-9mobile.pantheonsite.io/)

## Install
1. Install Docksal
2. ```
   git clone git@github.com:tannguyen04/9mobile.git
   cd 9mobile
   composer install --dev
   fin init
   ```
## Work with theme
We use `spectre` theme for styling 9mobile.vn
```
cd web/themes/custom/spectre
bower install

```
Please setup sass for local machine and free use sass. For more information please visit here https://sass-lang.com/
### Manage external libraries
We use `bower` for manage external libraries (update, install). More information to use `bower` please visit here https://bower.io/

Install new external library `bower install PACKAGE --save`
### Style guide driven
We use pattern lab for working in style guide driven
```
cd web/themes/custom/spectre/pattern-lab
composer start
```
For more information how to use pattern lab please visit here http://patternlab.io/
