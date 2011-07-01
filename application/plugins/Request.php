<?php
class Webenq_Plugin_Request extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_setLayout($request);
        $this->_unQuotePost($request);
    }

    protected function _setLayout($request)
    {
        if ($request->isXmlHttpRequest()) {
            if (!$request->getParam('format')) {
                $request->setParam('format', 'html');
            }
        }
    }

    protected function _unQuotePost($request)
    {
        if ($request->isPost() && (bool) ini_get('magic_quotes_gpc')) {
            $unQuoted = array();
            foreach ($request->getPost() as $key => $val) {
                $unQuoted[stripslashes($key)] = stripslashes($val);
            }
            $request->setPost($unQuoted);
        }
    }
}
