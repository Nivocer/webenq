#!/bin/sh
mkdir ../public/tests/log/libraries
./pdepend.sh
./phpcpd.sh
./phpcs.sh
./phpdoc.sh
./phpmd.sh
phpunit
