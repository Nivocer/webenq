#!/bin/sh
#$1=report id
#$2= uhm
#$3=directory
#$4=database  (hva-local-oo)

echo ------ >> createIntroduction.error
echo $1 >> createIntroduction.error
echo ------ >> createIntroduction.error
path='/home/jaapandre/workspace/webenq4_3/public/reports'
dir=$path/$3
echo $dir
dbUser='hva'
dbPass=''
reportId=$1

case $4 in 
	hva-local-oo)
	db='webenq_org_hva_oo'
	port=3306
	;;
	hva-server-oo)
	db='webenq_org_hva_oo'
	port=6603
	;;

	hva-local-fmb)
	db='webenq_org_hva_fmb'
	port=3306
	;;
	hva-server-fmb)
	db='webenq_org_hva_fmb'
	port=6603
	;;

	hva-local-lwb)
	db='webenq_org_hva_lwb'
	port=3306
	;;
	hva-server-lwb)
	db='webenq_org_hva_lwb'
	port=6603
	;;
	*)
	db='webenq_org_hva'
	port=3306
	;;
esac
echo $db;
java -cp .:./lib/* it.bisi.report.jasper.ExecuteIntroduction 127.0.0.1:$port/$db $dbUser $dbPass $1 $2 $dir 2>>createReport.error

#more createReport.error

