#!/bin/sh
phpcs --report=xml --standard=Zend ../application/ > ./log/phpcs.xml
