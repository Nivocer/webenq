<?php
/**
 * Controller plugin for pre-handling the request
 *
 * @package     Webenq
 * @subpackage  Plugins
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
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
        if ($request->isPost() && get_magic_quotes_runtime() == 1) {
            $unQuoted = $this->_unQuoteArrayRecursively($request->getPost());
            $request->setPost($unQuoted);
        }
    }

    protected function _unQuoteArrayRecursively(array $array)
    {
        $unQuoted = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $unQuoted[stripslashes($key)] = $this->_unQuoteArrayRecursively($val);
            } else {
                $unQuoted[stripslashes($key)] = stripslashes($val);
            }
        }
        return $unQuoted;
    }
}