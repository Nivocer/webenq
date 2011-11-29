#!/bin/sh
reportControlFile=$1
echo ------ >> createReport.error
echo $1 >> createReport.error
echo ------ >> createReport.error


java -cp .:./lib/* it.bisi.report.jasper.ExecuteReport $reportControlFile 2>>createReport.error
#more createReport.error

