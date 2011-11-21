#!/bin/sh
#$1=report id
#$2=directory
#$3=database  (hva-local-oo)

echo ------ >> createReport.error
echo $1 >> createReport.error
echo ------ >> createReport.error
path='/home/jaapandre/workspace/webenq4/public/reports'
path='tempout'

dbUser='hva'
dbPass=''


dir=$path/$2
reportId=$1
echo $dir
db='webenq_org_hva_oo'
port=3306


java -cp .:./lib/* it.bisi.report.jasper.ExecuteReport 127.0.0.1:$port/$db $dbUser $dbPass $reportId $dir 2>>createReport.error
#more createReport.error

