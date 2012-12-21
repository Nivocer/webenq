#!/bin/sh
#check coding guidelines
phpcs --report=xml --standard=Zend --ignore=application/models/Base/* ../application/ > ../public/tests/log/phpcs.xml
phpcs --report=xml --standard=Zend  ../libraries/WebEnq4/ > ../public/tests/log/libraries/phpcs.xml
