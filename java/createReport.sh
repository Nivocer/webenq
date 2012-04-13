#!/bin/sh
reportControlFile=$1
echo ------ >> createReport.error
echo $1 >> createReport.error
echo ------ >> createReport.error

jars=`find lib -name '*.jar'|tr "\n" ":"`

#echo "java -cp .:./lib/*:$jars it.bisi.report.jasper.ExecuteReport 127.0.0.1:$port/$db $dbUser $dbPass $reportId $dir 2>>createReport.error"

#java -cp .:./lib/* it.bisi.report.jasper.ExecuteReport $reportControlFile 2>>createReport.error
java -cp .:./lib/*:$jars it.bisi.report.jasper.ExecuteReport $reportControlFile 2>>createReport.error
#more createReport.error

