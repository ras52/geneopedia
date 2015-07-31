#!/bin/sh

set -e

ROOT=$(dirname $0)

if [ ! -x $ROOT/trash.sh ]; then
  echo Unable to determine root >&2
  exit 1
fi

rm -f $ROOT/site/include/config.ini

echo "About to be prompted for the MySQL root password" >&2
mysql -p -u root <<EOF
  DROP DATABASE geneopedia;
  DROP USER 'geneopedia'@'localhost';
EOF

MOGARGS="--trackers=127.0.0.1:7001 --domain=geneopedia --class=files"
mogtool $MOGARGS listkey | grep -Ev '#[0-9]+ files found' | while read ID; do 
  mogtool $MOGARGS delete $ID
done

mogadm class delete geneopedia files
mogadm domain delete geneopedia

crontab -r 
