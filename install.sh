#!/bin/sh

set -e

ROOT=$(dirname $0)

if [ ! -x $ROOT/install.sh ]; then
  echo Unable to determine root >&2
  exit 1
fi

if [ -e $ROOT/site/include/config.ini ]; then
  echo Site already configured >&2
  exit 1
fi

MYSQLPASS=$(pwgen -1s 16)
echo "About to be prompted for the MySQL root password" >&2
mysql -p -u root <<EOF
  CREATE DATABASE geneopedia;
  CREATE USER 'geneopedia'@'localhost' IDENTIFIED BY '$MYSQLPASS';
  GRANT ALL PRIVILEGES on geneopedia.* TO 'geneopedia'@'localhost';
EOF

sed -r "s/^([ \t]*password[ \t]*=[ \t]*)\"\"/\1\"$MYSQLPASS\"/;
        s/^([ \t]*secret[ \t]*=[ \t]*)\"\"/\1\"$(pwgen -1s 16)\"/;
        s/^([ \t]*domain[ \t]*=[ \t]*)\"localhost\"/\1\"$(hostname -f)\"/;" \
  < $ROOT/site/include/config.ini.template \
  > $ROOT/site/include/config.ini

mysql -p"$MYSQLPASS" -ugeneopedia geneopedia < $ROOT/db/001-users.sql

if [ -e $ROOT/install.local.sh ]; then
  . $ROOT/install.local.sh
fi

echo Done >&2
