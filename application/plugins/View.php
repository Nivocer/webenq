<?php
/**
 * WebEnq4
 *
 *  LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    Webenq_Application
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Controller plugin for settings up the view
 *
 * @package    Webenq_Application
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Plugin_View extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $view = new Zend_View();

        $view->doctype('XHTML1_STRICT');
        $view->headTitle()->setSeparator(' - ')->append('WebEnq 4');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');

        /* CSS */
        $view->headLink()->appendStylesheet($view->baseUrl('css/global.css'));
        $this->_addCss($request, $view);

        /* base URL */
        $view->headScript()->appendScript('baseUrl = "' . $view->baseUrl() . '";');

        /* jQuery core & UI */
        $view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
        $view->jQuery()->enable();
        $view->jQuery()->uiEnable();
        $view->jQuery()->setLocalPath($view->baseUrl('js/jquery/jquery.min.js'));
        $view->jQuery()->setUiLocalPath($view->baseUrl('js/jquery/jquery-ui.custom.min.js'));
        $view->jQuery()->addStylesheet($view->baseUrl('css/jquery/ui-lightness/jquery-ui.custom.min.css'));

        /* jQuery plugins */
        $language= Zend_Registry::get('Zend_Locale')->getLanguage();
        $view->headScript()->appendFile($view->baseUrl("js/jquery/jquery.ui.datepicker-$language.js"));
        $view->headScript()->appendFile($view->baseUrl('js/jquery/plugins/jquery.maskedinput.min.js'));
        $view->headScript()->appendFile($view->baseUrl('js/jquery/plugins/jquery.json.min.js'));
//        $view->headScript()->appendFile($view->baseUrl('js/jquery/plugins/date-format.js'));

        /* WebEnq4 library */
        $view->addHelperPath('WebEnq4/View/Helper/', 'WebEnq4_View_Helper');

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

        /* language selector */
        $view->languageSelection = $this->_getLanguageSelector($view);

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
                'label' => t('Home'),
                'controller' => 'index',
                'action' => 'index',
            ),
            array(
                'label' => t('Questionnaires'),
                'controller' => 'questionnaire',
                'action' => 'index',
            ),
            array(
                'label' => t('Reports'),
                'controller' => 'report',
                'action' => 'index',
            ),
            array(
                'label' => t('Import'),
                'controller' => 'import',
                'action' => 'index',
            ),
            array(
                'label' => t('Categories'),
                'controller' => 'category',
                'action' => 'index',
                ),
            array(
                'label' => t('Questions'),
                'controller' => 'question',
                'action' => 'index',
            ),
            array(
                'label' => t('Answer-possibilities'),
                'controller' => 'answer-possibility-group',
                'action' => 'index',
            ),
//            array(
//                'label' => t('Test'),
//                'controller' => 'test',
//                'action' => 'index',
//            ),
            array(
                'label' => t('Users'),
                'controller' => 'user',
                'action' => 'user',
            ),
        );

        if (Zend_Auth::getInstance()->hasIdentity()) {
            $pages[] = array(
                'label' => t('Log out'),
                'controller' => 'user',
                'action' => 'logout',
            );
        }

        return new Zend_Navigation($pages);
    }

    protected function _getLanguageSelector($view)
    {
        // get current locale settings
        $locale = Zend_Registry::get('Zend_Locale');

        // get all languages
        $languages = Webenq_Language::getLanguages();

        // return html for language selector
        $html = '<ul id="language_selector">';
        foreach ($languages as $language) {
            $html .= ($language === $locale->getLanguage()) ? '<li class="active">' : '<li>';
            $html .= '<a href="' . $view->baseUrl('/language/select/language/' . $language) . '">' . $language . '</a>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}