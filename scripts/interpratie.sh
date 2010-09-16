#!/bin/sh
start=90
end=93

start=98
end=113

x=$start
while [ $x -le $end ]
do
echo $x
wget "http://hva-devel.localhost/interpretation/index/id/$x"
x=$(($x+1))
done


