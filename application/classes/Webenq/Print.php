<?php

abstract class Webenq_Print extends Webenq
{
	protected $_questionnaire = array();

    /**
     * Factory method for easily getting an instance
     * of the right printer class.
     *
     * @param string $format
     * @param array|Questionnaire $questionnaire
     */
	static public function factory($format, $questionnaire)
	{
        if (is_array($questionnaire)) {
            $array = $questionnaire;
            $questionnaire = new Webenq_Model_Questionnaire();
            $questionnaire->fromArray($array);
        }

		$class = 'Webenq_Print_' . ucfirst(strtolower($format));
		return new $class($questionnaire);
	}

	public function __construct(Questionnaire $questionnaire)
	{
		$this->_questionnaire = $questionnaire;
		$this->init();
	}

	abstract function init();

	abstract function send(Zend_Controller_Response_Http $response);

	public function getDownloadFilename()
	{
		return $this->_downloadFilename;
	}

	public function getMimeType()
	{
		return $this->_mimeType;
	}
}