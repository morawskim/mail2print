#!/bin/sh

#Exit immediately if a command exits with a non-zero status.
set -e

cd $(dirname $(realpath $0))

if [ -d mail2print ]; then
  rm -rf mail2print
fi

mkdir -p mail2print/{DEBIAN,usr/bin,usr/share/doc/mail2print}

echo "Package: mail2print
Version: 0.1.0-1
Architecture: all
Maintainer: Marcin Morawski <marcin@morawskim.pl>
Depends: fetchmail (>= 6.3.21), php5 (>= 5.4.36)
Description: Mail2Print is PHP script, which read email from stdin,
 extract all supported attachments and prints them.
" > mail2print/DEBIAN/control

echo "#!/bin/sh
BIN_PATH='/usr/bin/mail2print.phar'
DOC_PATH='/usr/share/doc/mail2print'

if [ -f \$BIN_PATH ]; then
  chown root:root \$BIN_PATH
  chmod 0755 \$BIN_PATH
fi

if [ -d \$DOC_PATH ]; then
  chown root:root -R \$DOC_PATH
  chmod 0644 \$DOC_PATH/*.in \$DOC_PATH/*.md
fi

" > mail2print/DEBIAN/postinst
chmod 0755 mail2print/DEBIAN/postinst

cp ../fetchmailrc.in mail2print/usr/share/doc/mail2print/
cp ../mail2print.ini mail2print/usr/share/doc/mail2print/
cp ../README.md mail2print/usr/share/doc/mail2print/
cp ./mail2print.phar mail2print/usr/bin/

dpkg-deb --build mail2print/