<?php

class HVA_Plugin_View extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$view = new Zend_View();
 
        $view->doctype('XHTML1_STRICT');
        $view->headTitle()->setSeparator(' - ')->append('Webenq 4');
        $view->headMeta()->appendHttpEquiv('Content-Type','text/html; charset=utf-8');

        /* CSS */
        $view->headLink()->appendStylesheet($view->baseUrl('css/global.css'));
        $this->_addCss($request, $view);
        
        /* base URL */
        $view->headScript()->appendScript('baseUrl = "' . $view->baseUrl() . '";');
        
        /* jQuery core & UI */
        $view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
        $view->jQuery()->enable(); 
        $view->jQuery()->uiEnable();  
        $view->jQuery()->setLocalPath($view->baseUrl('js/jquery/jquery-1.4.3.min.js'));
        $view->jQuery()->setUiLocalPath($view->baseUrl('js/jquery/jquery-ui-1.8.5.custom.min.js'));  
        $view->jQuery()->addStylesheet($view->baseUrl('css/jquery/ui-lightness/jquery-ui-1.8.5.custom.css'));
        
        /* jQuery plugins */
        $view->headScript()->appendFile($view->baseUrl('js/jquery/jquery.ui.datepicker-nl.js'));
        $view->headScript()->appendFile($view->baseUrl('js/jquery/plugins/jquery.maskedinput-1.2.2.min.js'));
//		$view->headScript()->appendFile($view->baseUrl('js/jquery/plugins/date-format-1.2.3.js'));
        
        /* JS */
        $view->headScript()->appendFile($view->baseUrl('js/global.js'));
        $this->_addJs($request, $view);
        
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);  
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        
        /* flash messenger */
        $messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
        $view->messages = $messenger->getMessages();
        
        /* navigation */
		$view->navigation($this->_getNavigation($request));
        
        /* store view object in registry */
        Zend_Registry::set('view', $view);
	}
	
	protected function _addJs(Zend_Controller_Request_Abstract $request, Zend_View $view)
	{
		/* controller */
		$js = 'js/' . $request->getControllerName() . '.js';
		if (file_exists($js)) {
			$view->headScript()->appendFile($view->baseUrl($js));
		}

		/* action */
		$js = 'js/' . $request->getControllerName() . '/' . $request->getActionName() . '.js';
		if (file_exists($js)) {
			$view->headScript()->appendFile($view->baseUrl($js));
		}
	}
	
	protected function _addCss(Zend_Controller_Request_Abstract $request, Zend_View $view)
	{
		/* controller */
		$css = 'css/' . $request->getControllerName() . '.css';
		if (file_exists($css)) {
			$view->headLink()->appendStylesheet($view->baseUrl($css));
		}
		
		/* action */
		$css = 'css/' . $request->getControllerName() . '/' . $request->getActionName() . '.css';
		if (file_exists($css)) {
			$view->headLink()->appendStylesheet($view->baseUrl($css));
		}
	}
	
	protected function _getNavigation(Zend_Controller_Request_Abstract $request)
	{
		$pages = array(
			array(
				'label' => _('Home'),
				'controller' => 'index',
				'action' => 'index',
			),
			array(
				'label' => _('Questionnaires'),
				'controller' => 'questionnaire',
				'action' => 'index',
			),
			array(
				'label' => _('Import'),
				'controller' => 'import',
				'action' => 'index',
			),
			array(
				'label' => _('Vragen'),
				'controller' => 'question',
				'action' => 'index',
			),
			array(
				'label' => _('Antwoordmogelijkheden'),
				'controller' => 'answer-possibility-group',
				'action' => 'index',
			),
			array(
				'label' => _('Test'),
				'controller' => 'test',
				'action' => 'index',
			),
		);
		
		return new Zend_Navigation($pages);
	}
}
