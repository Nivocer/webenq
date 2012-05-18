<?php

/**
 * Nivocer specific extension of the Segment class of the OdtPhp project
 * 
 * @package OdtPhp_Nivocer
 * @author Bart Huttinga <b.huttinga@nivocer.com>
 * @see http://www.nivocer.com/wiki/doku.php?id=dev:nivocer_library_of_php_code
 * @see http://www.odtphp.com
 * @license http://www.gnu.org/copyleft/gpl.html  GPL License
 */
class OdtPhp_Nivocer_Segment extends Segment
{
    /**
     * Replace variables of the template in the XML code
     * All the children are also called
     *
     * @return string
     */
    public function merge()
    {
        $this->xmlParsed .= str_replace(array_keys($this->vars), array_values($this->vars), $this->xml);
        if ($this->hasChildren()) {
            foreach ($this->children as $child) {
                $this->xmlParsed = str_replace($child->xml, ($child->xmlParsed=="")?$child->merge():$child->xmlParsed, $this->xmlParsed);
                $child->xmlParsed = '';
            }
        }
        $reg = "#\[" . $this->name . "\](.*)\[/" . $this->name . "\]#sm";
        $this->xmlParsed = preg_replace($reg, '$1', $this->xmlParsed);
        $this->file->open($this->odf->getTmpfile());
        foreach ($this->images as $imageKey => $imageValue) {
			if ($this->file->getFromName('Pictures/' . $imageValue) === false) {
				$this->file->addFile($imageKey, 'Pictures/' . $imageValue);
			}
        }
        $this->file->close();		
        return $this->xmlParsed;
    }
    
    
    /**
     * Analyse the XML code in order to find children
     *
     * @param string $xml
     * @return Segment
     */
    protected function _analyseChildren($xml)
    {
        $reg2 = "#\[([\S]*)\](.*)\[/(\\1)\]#sm";
        preg_match_all($reg2, $xml, $matches);
        for ($i = 0, $size = count($matches[0]); $i < $size; $i++) {
            if ($matches[1][$i] != $this->name) {
                $this->children[$matches[1][$i]] = new self($matches[1][$i], $matches[0][$i], $this->odf);
            } else {
                $this->_analyseChildren($matches[2][$i]);
            }
        }
        return $this;
    }
}