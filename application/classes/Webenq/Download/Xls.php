<?php
/**
 * Downloader class for XLS format
 *
 * This class generates a Microsoft Excel download
 * and sends it to the client.
 *
 * @category	Webenq
 * @package		Webenq
 * @author Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Download_Xls extends Webenq_Download
{
    /**
     * Extension of the file for download.
     *
     * @var string
     */
    protected $_extension = 'xls';

    /**
     * The MIME type of the file for download.
     *
     * @var string
     */
    protected $_mimeType = 'application/vnd.ms-excel';

    /**
     * An object representing the actual document
     *
     * @var PHPExcel
     */
    protected $_document;

    /**
     * Prepares the document so that it can be send to
     * the client.
     *
     * @return self
     */
    public function init()
    {
        // load library and create document
        require_once 'PHPExcel/Classes/PHPExcel.php';
        $xls = new PHPExcel();
        $sheet = $xls->getActiveSheet();

        // add data from array to xls
        foreach ($this->_data as $indexRow => $row) {
            foreach ($row as $indexCol => $value) {
                $sheet->setCellValueByColumnAndRow($indexCol, $indexRow+1, $value);
            }
        }

        $this->_document = $xls;

        return $this;
    }

    public function addWorkingSheet(array $data)
    {
        $sheet = $this->_document->createSheet();

        // add data from array to xls
        foreach ($data as $indexRow => $row) {
            foreach ($row as $indexCol => $value) {
                $sheet->setCellValueByColumnAndRow($indexCol, $indexRow+1, $value);
            }
        }
    }

    /**
     * Sends the document
     *
     * Sets and sends the headers using the provided
     * response object. And then writes the document
     * to the standard output.
     *
     * @param Zend_Controller_Response_Http $response
     */
    public function send(Zend_Controller_Response_Http $response)
    {
        $response
        ->setHeader('Content-Transfer-Encoding', 'binary')
        ->setHeader('Content-Type', $this->getMimeType())
        ->setHeader('Content-Disposition', 'attachment; filename="' . $this->getDownloadFilename() . '"')
        ->sendHeaders();

        $writer = $this->_getWriter();
        $writer->save('php://output');
    }



    public function save($fileNameBase)
    {
        $writer = $this->_getWriter();
        $fileName='outputs/'.$fileNameBase.$this->getDownloadFilename();
        $writer->save($fileName);
        return $fileName;
    }


    /**
     * Returns a writer instance for the given document
     * type.
     *
     * @return PHPExcel_Writer_IWriter
     */
    protected function _getWriter()
    {
        return PHPExcel_IOFactory::createWriter($this->_document, 'Excel5');
    }
}
