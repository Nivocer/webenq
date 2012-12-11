<?php
/**
 * Jasper Controller class
 *
 * Attempt to connect to java via php-class, has not been in production
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
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