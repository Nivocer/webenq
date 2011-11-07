rm createIntroduction.error
dir="test"



#1=nederlands, 3=engels, daar moet barchart, open en tables van gemaakt worden
#4=nederlands, 5=engels, daar moet open (open2) van gemaakt worden).

type_reports="open"
reports="4 5"
#reports="5"

type_reports="barchart open tables"
reports="1 3"
reports="1"

# niet doen, tenzij echt nodig.
#report  voltijd/int
#type_reports="tables"
#reports="6 7"

dir="fraijlemaborgQ3"
for repdef in `echo $reports` 
do 
echo '---------'
for type_report in `echo $type_reports`
do
echo $repdef $dir  $type_report
./createIntroduction.sh $repdef $type_report $dir
done
done




more createIntroduction.error

