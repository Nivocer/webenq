rm createReport.error

reportControlFiles=$reportControlFiles" http://webenq4_clean.localhost/report/control/id/3"

for reportControlFile in `echo $reportControlFiles` 
do 
echo '---------'
echo "executing: $reportControlFile"
./createReport.sh $reportControlFile 
done
echo '---------'
echo  "report creation output:"
more createReport.error
