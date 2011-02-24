<?php
class Webenq_Plugin_Request extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ($request->isXmlHttpRequest()) {
			if (!$request->getParam('format')) {
				$request->setParam('format', 'html');
			}
		}
	}
}
