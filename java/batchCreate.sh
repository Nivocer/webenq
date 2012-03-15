rm createReport.error
#reportControlFiles=$reportControlFiles" /home/jaapandre/workspace/webenq4_hva_oo/java/reportControl.xml"

#reportControlFiles=$reportControlFiles" /home/jaapandre/workspace/webenq4_hva_oo/java/reportControlHvA-rapport1.xml"
#reportControlFiles=$reportControlFiles" /home/jaapandre/workspace/webenq4_hva_oo/java/reportControlHvA-rapport2.xml"
#reportControlFiles=$reportControlFiles" /home/jaapandre/workspace/webenq4_hva_oo/java/reportControlHvA-rapport3.xml"
#reportControlFiles=$reportControlFiles" /home/jaapandre/workspace/webenq4_hva_oo/java/reportControlHvA-rapport4.xml"

#reportControlFiles=$reportControlFiles" http://demo4.webenq.org/report/control/id/1"
#reportControlFiles=$reportControlFiles" http://demo4.webenq.org/report/control/id/2"
#reportControlFiles=$reportControlFiles" http://demo4.webenq.org/report/control/id/3"
#reportControlFiles=$reportControlFiles" http://demo4.webenq.org/report/control/id/4"

#rapport 1 gedefinieerd via webenq.org
#reportControlFiles=$reportControlFiles" ./semester1-reportControlHvA-rapport1test.xml"

#hvaoo: semester 2011-2012 semester 1:
#reportControlFiles=$reportControlFiles" ./semester1-reportControlHvA-rapport1.xml"
#reportControlFiles=$reportControlFiles" ./semester1-reportControlHvA-rapport2.xml"
#reportControlFiles=$reportControlFiles" ./semester1-reportControlHvA-rapport3.xml"
#reportControlFiles=$reportControlFiles" ./semester1-reportControlHvA-rapport4.xml"

#reportControlFiles=$reportControlFiles" ./semester1-reportControlHvA-rapport6.xml"

#reportControlFiles=$reportControlFiles" http://webenq4.localhost/report/control/id/9"
#reportControlFiles=$reportControlFiles" http://webenq4.localhost/report/control/id/10"
#reportControlFiles=$reportControlFiles" http://webenq4.localhost/report/control/id/11"
#reportControlFiles=$reportControlFiles" http://webenq4.localhost/report/control/id/12"

#werkplekleren
#reportControlFiles=$reportControlFiles" ./semester1-reportControlHvA-rapport1Werkplekleren.xml"
reportControlFiles=$reportControlFiles" ./semester1-reportControlHvA-rapport2Werkplekleren.xml"
#reportControlFiles=$reportControlFiles" ./semester1-reportControlHvA-rapport3Werkplekleren.xml"
#semester1-reportControlHvA-rapport1Werkplekleren.xml 

#pabo blok 1
#response
#reportControlFiles=$reportControlFiles" http://webenq4.localhost/report/control/id/15"
#reportControlFiles=$reportControlFiles"  semester1-blok1-reportControlHva-rapport6.xml"


#reportControlFiles=$reportControlFiles" http://webenq4.localhost/report/control/id/17"

for reportControlFile in `echo $reportControlFiles` 
do 
echo '---------'
echo "executing: $reportControlFile"
./createReport.sh $reportControlFile
done
echo '---------'
echo  "report creation output:"
more createReport.error
