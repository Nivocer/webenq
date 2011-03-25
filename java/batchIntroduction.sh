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
dir="fraijlemaborgQ3"

#pabo
type_reports="pabo"
reports="8 11"
#reports="11"
dir="oo-pabo"

#pedagogiek
type_reports="pedagogiek"
reports="9 10"
#reports="10"
dir="oo-pedagogiek"


#FMB
# niet doen, tenzij echt nodig.
#report  voltijd/int
#type_reports="tables"
#reports="6 7"


#opleidingen
reports="13 15"
type_reports="tablesSem openSem barchartsSem"
type_reports="openSem"

#docenten
#hoeft niet voor onderwijsevaluaties 2010/2011-semester 1
#reports=14
#type_reports="openSem"
dir="fmb201102"

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

