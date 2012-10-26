<?php
class Webenq_Test_Plugin_RequestTest extends Webenq_Test_Case_Plugin
{
	/**
	 * test if magic quoting is working properly if it is on, test only needed prior to php 5.4
	 */
	public function testRequestIsUnquotedWhenMagicQuotesAreEnabled()
	{
		//only test for php 5.0 to 5.3
		if (PHP_MAJOR_VERSION===5 && PHP_MINOR_VERSION<=3){
			$originalMagicQuotesSetting = ini_get('magic_quotes_runtime');
			ini_set('magic_quotes_runtime', 1);

			// define key and value
			$key = "test";
			$value = "te\st";

			// simulate post-request with magic quotes enabled
			$request = new Zend_Controller_Request_Http();
			$_SERVER['REQUEST_METHOD'] = 'POST';
			$request->setPost(addslashes($key), addslashes($value));

			// use plugin
			$plugin = new Webenq_Plugin_Request();
			$plugin->dispatchLoopStartup($request);

			$this->assertTrue($request->getPost($key) === $value);

			// define key and value
			$key = "'test/\\";
			$value = "'test/\\";

			// simulate post-request with magic quotes enabled
			$request = new Zend_Controller_Request_Http();
			$_SERVER['REQUEST_METHOD'] = 'POST';
			$request->setPost(addslashes($key), addslashes($value));

			// use plugin
			$plugin = new Webenq_Plugin_Request();
			$plugin->dispatchLoopStartup($request);

			$this->assertTrue($request->getPost($key) === $value);
			ini_set('magic_quotes_runtime', $originalMagicQuotesSetting);
		}
	}
}
