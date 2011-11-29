rm createReport.error
reportControlFiles="/home/jaapandre/workspace/webenq4/java/reportControl.xml"

for reportControlFile in `echo $reportControlFiles` 
do 
echo '---------'
echo "executing: $reportControlFile"
./createReport.sh $reportControlFile
done
echo '---------'
echo  "report creation output:"
more createReport.error
