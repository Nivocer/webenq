phpdoc  -s                            \
    -d ../application/,../classes/,../libraries/WebEnq4/  \
    -i *.phtml,*.ini                \
    -t ../public/tests/phpdocs

phpdoc  -s                            \
    -d ../libraries/WebEnq4/  \
    -i *.phtml,*.ini                \
    -t ../public/tests/phpdocs/libraries
