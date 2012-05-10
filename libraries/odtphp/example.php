<?php

/**
 * Example file for creating ODT files with the OdtPhp_Nivocer class
 * 
 * @author Bart Huttinga <b.huttinga@nivocer.com>
 */

/* Load the OdtPhp_Nivocer library */
require_once('odtphp/Nivocer.php');

/* Create instance of OdtPhp_Nivocer class */
$odf = new OdtPhp_Nivocer("example.odt");

/* Assign variables to the tempalte file */
$odf->setVars('koptekst', 'KOPTEKST');
$odf->setVars('titel', 'RAPPORT');
$odf->setVars('voettekst', 'VOETTEKST');

/* Create segment of questions and loop through */
$vragen = $odf->setSegment('vragen');
for ($j = 1; $j <= 4; $j++) {
	/* Assign variable */
    $vragen->setVars('vraag', 'Vraag ' . $j);
    /* Loop through sub-segment and merge */
    for ($i = 1; $i <= 3; $i++) {
        $vragen->antwoorden->antwoord('Antwoord ' . $i);
        $vragen->antwoorden->datum(date('d/m/Y'));
        $vragen->antwoorden->merge();
    }
    /* Loop through sub-segment and merge */
    for ($i = 1; $i <= 4; $i++) {   
        $vragen->opmerkingen->opmerking('Opmerking ' . $i);
        $vragen->opmerkingen->merge();        
    }
    /* Merge this loop of segment */
    $vragen->merge();
}
/* Merge segment to document */
$odf->mergeSegment($vragen);

/* Export result as attached file */
$odf->exportAsAttachedFile();