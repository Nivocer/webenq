#!/bin/sh
#check coding guidelines
phpcpd --min-lines 12   --log-pmd ../public/tests/log/phpcpd.xml ../application/
phpcpd --min-lines 12   --log-pmd ../public/tests/log/libraries/phpcpd.xml ../libraries/WebEnq4/
