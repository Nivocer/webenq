<?php
/**
 * Controller plugin for settings up localization
 *
 * @package     Webenq
 * @subpackage  Plugins
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Plugin_Locale extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $session = new Zend_Session_Namespace();
	if ($request->getParam("language")) {
        	$session->language = $request->getParam("language");
	}

	if ($session->language) {
            $locale = Zend_Registry::get('Zend_Locale');
            $locale->setLocale($session->language);
        }
    }
}
