rm createReport.error
#reportControlFiles=$reportControlFiles" /home/jaapandre/workspace/webenq4_hva_oo/java/reportControl.xml"

reportControlFiles=$reportControlFiles" /home/jaapandre/workspace/webenq4_hva_oo/java/reportControlHvA-rapport1.xml"
#reportControlFiles=$reportControlFiles" /home/jaapandre/workspace/webenq4_hva_oo/java/reportControlHvA-rapport2.xml"
#reportControlFiles=$reportControlFiles" /home/jaapandre/workspace/webenq4_hva_oo/java/reportControlHvA-rapport3.xml"
#reportControlFiles=$reportControlFiles" /home/jaapandre/workspace/webenq4_hva_oo/java/reportControlHvA-rapport4.xml"


for reportControlFile in `echo $reportControlFiles` 
do 
echo '---------'
echo "executing: $reportControlFile"
./createReport.sh $reportControlFile
done
echo '---------'
echo  "report creation output:"
more createReport.error
