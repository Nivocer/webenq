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
 * @package    Webenq
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Jasper Controller class
 *
 * Attempt to connect to java via php-class, has not been in production
 *
 * @package    Webenq
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 * @deprecated
 */

class JasperController extends Zend_Controller_Action
{
    /**
     * Initialisation
     *
     * @return unknown_type
     */
    public function init()
    {
        /* include files with helper functions */
        require_once "java/java-ext-loader.php";
        require_once "java/php-java-converter.php";

        /* define the right port */
        define("JAVA_HOSTS", "localhost:8081");

        /* check if the java extension has been loaded */
        checkJavaExtension();

        /* disable layout */
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }


    /**
     * Renders the overview of export options
     */
    public function indexAction()
    {
        $compileManager = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
        $report = $compileManager->compileReport(realpath("test.jrxml"));

        $fillManager = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");

        $params = new Java("java.util.HashMap");
        $params->put("text", "This is a test string");
        $params->put("number", 3.00);
        $params->put("date", convertValue("2007-12-31 0:0:0", "java.sql.Timestamp"));

        $emptyDataSource = new Java("net.sf.jasperreports.engine.JREmptyDataSource");
        $jasperPrint = $fillManager->fillReport($report, $params, $emptyDataSource);

        $outputPath = realpath(".")."/"."output.pdf";

        $exportManager = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
        $exportManager->exportReportToPdfFile($jasperPrint, $outputPath);

        $this->getResponse()
            ->setHeader("content-type", "application/pdf", true)
            ->sendHeaders();
        readfile($outputPath);
        unlink($outputPath);
    }
}