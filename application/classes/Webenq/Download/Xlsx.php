<?php
/**
 * Downloader class for XLSX format
 *
 * This class generates a Microsoft Excel 2007
 * download and sends it to the client.
 *
 * @category	Webenq
 * @package		Webenq
 * @author Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Download_Xlsx extends Webenq_Download_Xls
{
    /**
     * Extension of the file for download.
     *
     * @var string
     */
    protected $_extension = 'xlsx';

    /**
     * The MIME type of the file for download.
     *
     * @var string
     */
    protected $_mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /**
     * Returns a writer instance for the given document
     * type.
     *
     * @return PHPExcel_Writer_IWriter
     */
    protected function _getWriter()
    {
        return PHPExcel_IOFactory::createWriter($this->_document, 'Excel2007');
    }
}
