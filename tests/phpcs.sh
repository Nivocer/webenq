#!/bin/sh
#check coding guidelines
phpcs --report=xml --standard=Zend ../application/ > ./log/phpcs.xml
