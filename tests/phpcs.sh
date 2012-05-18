#!/bin/sh
#check coding guidelines
phpcs --report=xml --standard=Zend ../application/ > ../public/tests/log/phpcs.xml
