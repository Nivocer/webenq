# we don't use controversial ruleset at this moment, it is mostly about camelcase, we checkt this with codesniffer
#one test is usefull in this ruleset: use of  Superglobals
phpmd ../application/ xml codesize,design,unusedcode,naming --reportfile ../public/tests/log/phpmd.xml
phpmd ../application/ html codesize,design,unusedcode,naming --reportfile ../public/tests/log/phpmd.html

phpmd ../libraries/WebEnq4/ xml codesize,design,unusedcode,naming --reportfile ../public/tests/log/libraries/phpmd.xml
phpmd ../libraries/WebEnq4/ html codesize,design,unusedcode,naming --reportfile ../public/tests/log/libraries/phpmd.html


