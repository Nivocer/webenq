rm createReport.error
dir='test'
settings='hva-local-lwb'
reports="1"

for repdef in `echo $reports` 
do 
echo '---------'
echo $repdef $dir $settings
./createReport.sh $repdef $dir $settings
done
more createReport.error
