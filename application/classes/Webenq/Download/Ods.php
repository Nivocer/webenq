<?php
/**
 * Downloader class for ODS format
 *
 * This class generates an Open Office text document
 * download and sends it to the client.
 *
 * @category	Webenq
 * @package		Webenq
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Download_Ods extends Webenq_Download
{
    /**
     * Extension of the file for download.
     *
     * @var string
     */
    protected $_extension = 'ods';

    /**
	 * The MIME type of the file for download.
	 *
	 * @var string
	 */
	protected $_mimeType = 'application/vnd.oasis.opendocument.spreadsheet';

	/**
	 * Filename for temporary storing the document
	 *
	 * @var string
	 */
	protected $_filename;

	/**
	 * Prepares the document so that it can be send to
	 * the client.
	 *
	 * @return self
	 */
	public function init()
	{
        $attrs = array();

		// convert array to ods-array
        $odsArray = array();
		foreach ($this->_data as $indexRow => $row) {
		    foreach ($row as $indexCol => $value) {
		        $odsArray[0]['rows'][$indexRow][$indexCol] = array(
		          'attrs' => array(),
		          'value' => str_replace('&#039;', '&apos;', htmlspecialchars($value, ENT_QUOTES)));
		    }
		}

		// create ods
		require_once('ods-php/ods.php');
		$ods = new ods();
		$ods->sheets = $odsArray;
		$filename = $this->_filename = sys_get_temp_dir() . '/' . uniqid() . '.ods';
		saveOds($ods, $filename);

		return $this;
	}

	/**
	 * Sends the document
	 *
	 * Sets the headers and body using the provided
	 * response object. And removes the temporary file
	 * before the response is sent to the client.
	 *
	 * @param Zend_Controller_Response_Http $response
	 */
	public function send(Zend_Controller_Response_Http $response)
	{
		if ($output = file_get_contents($this->_filename)) {
	    	$response
	    		->setHeader('Content-Transfer-Encoding', 'binary')
	    		->setHeader('Content-Type', $this->getMimeType())
	    		->setHeader('Content-Disposition', 'attachment; filename="' . $this->getDownloadFilename() . '"')
	    		->setBody($output);
	    	@unlink($this->_filename);
		}
	}
}