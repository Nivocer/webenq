#!/bin/sh
start=65
end=69


x=$start
while [ $x -le $end ]
do
echo $x
wget "http://hva-devel.localhost/interpretation/index/id/$x"
x=$(($x+1))
done


