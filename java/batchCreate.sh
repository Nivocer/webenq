rm createReport.error
dir='test'

#open
#reports='89 87 91 93 95 97 99 101 103 105 107 109 111 113 115 117 119 121 123 125 127 129 131 133 135 137 142 140 144 146 148 150 152 154 156'
#dir='lwb201008-open'
#tables
#reports='90 88 92 94 96 98 100 102 104 106 108 110 112 114 116 118 120 122 124 126 128 130 132 134 136 138 143 141 145 147 149 151 153 155 157 158'
#reports='90 88 92 98 100 102'
#dir='lwb201008-tables'

#minoren tables
#reports='94'
#dir='lwb201008-tables'
#minoren open
#reports='93'
#dir='lwb201008-open'


#lwb extra rapport  #4463
#reports="206 205"
#dir='lwb201009'

#fraijlemaborg 2010 Q3
tables2=" 164 169 174 179 184 189 194 199 "
tables=" 165 170 175 180 185 190 195 200 "
barcharts=" 166 171 176 181 186 191 196 201 "
open1=" 167 172 177 182 187 192 197 202 "
open2=" 168 173 178 183 188 193 198 203 " 
#reports=`echo  $tables2 $tables $barcharts $open1 $open2`
#zonder tables2 (splits by opleiding, groep by docent)
#reports=`echo  $tables $barcharts $open1 $open2`
#reports=$barcharts
#reports="169"
#reports=$tables
#dir="fraijlemaborgQ3"

#barcharts2 eerst 2 nl, dan 2 engels
reports="169 199 179 189 "
reports="189 "

dir="lwb201011"
reports="210 211"


for repdef in `echo $reports` 
do 
echo '---------'
echo $repdef $dir
./createReport.sh $repdef $dir
done
more createReport.error

