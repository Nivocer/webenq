#!/bin/sh
start=353
end=388


x=$start
while [ $x -le $end ]
do
echo " $x "
#wget "http://hva.localhost/interpretation/index/id/$x"
x=$(($x+1))
done


