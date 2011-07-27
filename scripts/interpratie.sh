#!/bin/sh
start=1230
end=1246
start=276
end=284
client="hva-local-lwb"
url="http://$client.localhost/interpretation/index/id"


x=$start
while [ $x -le $end ]
do
echo " $x "
#wget "http://hva-local-oo.localhost/interpretation/index/id/$x"
wget "$url/$x"
x=$(($x+1))
done


