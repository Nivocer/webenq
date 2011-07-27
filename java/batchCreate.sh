rm createReport.error
dir='test'
settings='hva-local-lwb'

#fraijlemaborg 2010 Q3
tables2=" 164 169 174 179 184 189 194 199 "
tables=" 165 170 175 180 185 190 195 200 "
barcharts=" 166 171 176 181 186 191 196 201 "
open1=" 167 172 177 182 187 192 197 202 "
open2=" 168 173 178 183 188 193 198 203 " 
#reports=`echo $tables2 $tables $barcharts $open1 $open2`
#zonder tables2 (splits by opleiding, groep by docent)
#reports=`echo $tables $barcharts $open1 $open2`
#reports=$barcharts
#reports="169"
#reports=$tables
#dir="fraijlemaborgQ3"

reports='256 257'
dir="lwb201012b"


#258=open, 260=barcharts
reports='258 260'
#reports='260'
dir="ootest"


#oo pabo
reportsOpenLosPabo="271 273 275"
reportsOpenPraktijkPabo="303 305 307 309"
reportsBarchartsLosPabo="272 274 276"
reportsBarchartsPraktijkPabo="304 306 308 310"

#oo pedagogiek
reportsOpenLosPed="279 281 "
reportsOpenPraktijkPed="295 297 299 301"
reportsBarchartsLosPed="280 282"
reportsBarchartsPraktijkPed="296 298 300 302"


#Pabo
reportsOpen="$reportsOpenLosPabo $reportsOpenPraktijkPabo"
reportsBarcharts="$reportsBarchartsLosPabo $reportsBarchartsPraktijkPabo"
dir="oo-pabo"

#ped
#reportsOpen="$reportsOpenLosPed $reportsOpenPraktijkPed"
#reportsBarcharts="$reportsBarchartsLosPed $reportsBarchartsPraktijkPed"
#dir="oo-pedagogiek"

reports="$reportsBarcharts $reportsOpen"
#reports=$reportsBarcharts
#reports=$reportsOpen

#lmb
reports="293 294"
reports="312 313 314 315"
reports="316 317"
#open
dir="lwb201101b"

reports="321 323 325 327 329 331 333  322 324 326 328 330 332 334"
#tables:
#reports="334 333"
reports="322 324 326 328 330 332 334"
dir="lwb201102"
reports="326"
#dir="test"

reports="335 336"
dir="lwb201102b"
reports="326 336"
dir="test"


#fmb
reports="345 346 347 349 350 351 "
#barcharts nl:
#reports=351
#barcharts en:
#reports=347

#tables nl
#reports=350
#tables en
#reports=346
#portret tables:
#reports=346
#open nl
#reports=349
#open en
#reports=345
dir="fmb201102"
#settings='hva-local-fmb'

#leeuwenburg beetjeveel 
#reports="353 354 355 356 357 358 359 360 361 362 363 364 365 366 367 368 369 370 371 372 373 374 375 376 377 378 379 380 381 382 383 384 385 386 387 388"
#reports="372"
#dir="lwb201102c"
#settings='hva-local-lwb'

#leeuwenburg correctie
#dem minoren tables / open
#reports="322 321 372 371"
#open:
#reports="371 321"
#reports="372"
#dir="lwb201102d"
#vt prop blok 2

reports="1 2 3"
dir='test'

for repdef in `echo $reports` 
do 
echo '---------'
echo $repdef $dir $settings
./createReport.sh $repdef $dir $settings
done
more createReport.error
