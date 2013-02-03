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
 * Controller plugin for pre-handling the request
 *
 * @package    Webenq_Application
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
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