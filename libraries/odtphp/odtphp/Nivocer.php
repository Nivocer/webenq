<?php

require_once('odf.php');
require_once('Nivocer/Segment.php');

/**
 * Nivocer specific extension of the Odf class of the OdtPhp project
 *
 * @package OdtPhp_Nivocer
 * @author Bart Huttinga <b.huttinga@nivocer.com>
 * @see http://www.nivocer.com/wiki/doku.php?id=dev:nivocer_library_of_php_code
 * @see http://www.odtphp.com
 * @license http://www.gnu.org/copyleft/gpl.html  GPL License
 */
class OdtPhp_Nivocer extends Odf
{
    /**
     * Class constructor
     * 
     * Executes the original class constructor, followed by the overridden method
     * _moveRowSegments(). The original _moveRowSegments() is executed in the parent's
     * constructur and cannot be overridden (as it is a private method), but does no
     * harm (it searches for patterns it won't find).
     *
     * @param string $filename The name of the odt file
     * @param array $config Configuration options
     * @return void
     * @throws OdfException
     */
    public function __construct($filename, $config = array())
    {
    	parent::__construct($filename, $config);
        $this->_moveRowSegments();
    }
    
    
    /**
     * Assing a template variable
     * 
     * In this overridden method an extra check is done. If the variable doesn't
     * exist it could possibly be in style.xml in stead of content.xml (i.e. in
     * case of header and footer texts).
     *
     * @param string $key name of the variable within the template
     * @param string $value replacement value
     * @param bool $encode if true, special XML characters are encoded
     * @throws OdfException
     * @return odf
     */
    public function setVars($key, $value, $encode = true, $charset = 'ISO-8859')
    {
    	try {
    		parent::setVars($key, $value, $encode, $charset);
    	} catch(OdfException $e) {
    		if (!$this->_findVarInStyles($key, $value, $encode, $charset)) {
    			throw $e;
    		}
    	}
    	return $this;
    }
    
    
    /**
     * Looks for a variable in the styles.xml section of a document
     * 
     * Opens file, finds variable, replaces it and saves file. All done at once,
     * for this function will only be called once or twice per document.
     *   
     * @return self
     */
    protected function _findVarInStyles($key, $value, $encode, $charset)
    {
        $zipHandler = $this->config['ZIP_PROXY'];
        $file = new $zipHandler();
    	
        if (!$file->open($this->tmpfile)) {
    		return false;
    	}
    	
        if (!$xml = $file->getFromName('styles.xml')) {
    		return false;
    	}
    	
    	$xml = $file->getFromName('styles.xml');
    	
        $value = ($encode === true) ? htmlspecialchars($value) : $value;
        $value = ($charset === 'ISO-8859') ? utf8_encode($value) : $value;
        $value = str_replace("\n", "<text:line-break/>", $value);
        $pattern = $this->config['DELIMITER_LEFT'] . $key . $this->config['DELIMITER_RIGHT'];
        
        $xml = str_replace($pattern, $value, $xml);
        
        $file->addFromString('styles.xml', $xml);
        $file->close();
        
    	return true;
    }
    
    
    /**
     * Move segment tags for lines of tables
     * Called automatically within the constructor
     *
     * @return void
     */    
    protected function _moveRowSegments()
    {
    	// Search all possible rows in the document
    	$reg1 = "#<table:table-row[^>]*>(.*)</table:table-row>#smU";
		preg_match_all($reg1, $this->contentXml, $matches);
		for ($i = 0, $size = count($matches[0]); $i < $size; $i++) {
			// Check if the current row contains a segment row.*
			$reg2 = '#\[(row.[\S]*)\](.*)\[/\\1\]#sm';
			if (preg_match($reg2, $matches[0][$i], $matches2)) {
				$balise = str_replace('row.', '', $matches2[1]);
				// Move segment tags around the row
				$replace = array(
					'[' . $matches2[1] . ']'	=> '',
					'[/' . $matches2[1] . ']'	=> '',
					'<table:table-row'			=> '[' . $balise . ']<table:table-row',
					'</table:table-row>'		=> '</table:table-row>[/' . $balise . ']'
				);
				$replacedXML = str_replace(array_keys($replace), array_values($replace), $matches[0][$i]);
				$this->contentXml = str_replace($matches[0][$i], $replacedXML, $this->contentXml);
			}
		}
    }
    
    
    /**
     * Add the merged segment to the document
     *
     * @param Segment $segment
     * @throws OdfException
     * @return odf
     */
    public function mergeSegment(Segment $segment)
    {
        if (! array_key_exists($segment->getName(), $this->segments)) {
            throw new OdfException($segment->getName() . 'cannot be parsed, has it been set yet ?');
        }
        $string = $segment->getName();
		$reg = '@\[' . $string . '\](.*)\[/' . $string . '\]@smU';
        $this->contentXml = preg_replace($reg, $segment->getXmlParsed(), $this->contentXml);
        return $this;
    }
    
    
    /**
     * Declare a segment in order to use it in a loop
     *
     * @param string $segment
     * @throws OdfException
     * @return Segment
     */
    public function setSegment($segment)
    {
        if (array_key_exists($segment, $this->segments)) {
            return $this->segments[$segment];
        }
        $reg = "#\[" . $segment . "\](.*)\[/" . $segment . "\]#sm";
        if (preg_match($reg, html_entity_decode($this->contentXml), $m) == 0) {
            throw new OdfException("'$segment' segment not found in the document");
        }
        $this->segments[$segment] = new OdtPhp_Nivocer_Segment($segment, $m[1], $this);
        return $this->segments[$segment];
    }
    
    
    /**
     * Returns the content XML
     * 
     * @return string Content XML
     */
    public function getContentXml()
    {
    	return $this->contentXml;
    }
}