#!/bin/sh
#$1=report id
#$2=directory
#$3=database  (hva-local-oo)

echo ------ >> createReport.error
echo $1 >> createReport.error
echo ------ >> createReport.error
path='/home/jaapandre/workspace/webenq4/public/reports'

#hva
dbuser='hva'
dbpassword=''

#webenq4
dbuser='webenq_org'
dbpassword=''

dir=$path/$2
dbUser='hva'
dbPass=''
reportId=$1
echo $dir

case $3 in 
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

java -cp .:./lib/* it.bisi.report.jasper.ExecuteReport 127.0.0.1:$port/$db $dbUser $dbPass $reportId $dir 2>>createReport.error
#more createReport.error

