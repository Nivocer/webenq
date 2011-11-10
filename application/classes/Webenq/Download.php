<?php
/**
 * Abstract downloader class
 *
 * This class contains the common parts for all
 * downloader classes. The inherited class is
 * responsible for creating the right document type
 * and preparing/sending the response to the client.
 *
 * @author Bart Huttinga <b.huttinga@nivocer.com>
 */
abstract class Webenq_Download extends Webenq
{
	/**
	 * Questionnaire
	 *
	 * @var Webenq_Model_Questionnaire $_questionnaire
	 */
	protected $_questionnaire;

    /**
     * Array containing the questionnaire data.
     *
     * @var array $_data
     */
    protected $_data = array();

	/**
	 * Factory method for easily getting an instance
	 * of the right downloader class.
	 *
	 * @param string $format
	 * @param Webenq_Model_Questionnaire $questionnaire
	 */
	static public function factory($format, Webenq_Model_Questionnaire $questionnaire)
	{
		$class = 'Webenq_Download_' . ucfirst(strtolower($format));
		return new $class($questionnaire);
	}

	/**
	 * Class constructor
	 *
	 * Sets the questionnaire property and calls the
	 * init() method that must be implemented by all
	 * downloader classes.
	 *
	 * @param Webenq_Model_Questionnaire $questionnaire
	 */
	public function __construct(Webenq_Model_Questionnaire $questionnaire)
	{
		$this->_questionnaire = $questionnaire;
        $this->_data = $questionnaire->getDataAsSpreadsheetArray();
		$this->init();
	}

	/**
	 * Prepares the document
	 *
	 * This method must be implemented by all downloader
	 * classes and should prepare the document, so that
	 * it is ready to be send to the client.
	 */
	abstract function init();

	/**
	 * Sends the document
	 *
	 * This method must be implemented by all downloader
	 * classes and should send the document to the client,
	 * using the provided response object.
	 *
	 * @param Zend_Controller_Response_Http $response
	 */
	abstract function send(Zend_Controller_Response_Http $response);

	/**
	 * Returns the name of the file for download
	 */
	public function getDownloadFilename()
	{
	    if ($title = $this->_questionnaire->getQuestionnaireTitle()->text) {
	        $filename = strtolower(preg_replace('/[^a-zA-Z0-9]/', null, $title));
	    } else {
	        $filename = 'notitle';
	    }
	    $filename .= date('_Ymd_His.') . $this->_extension;
		return $filename;
	}

	/**
	 * Returns the MIME type of the file for download
	 */
	public function getMimeType()
	{
		return $this->_mimeType;
	}
}