<?php
/**
 * Downloader class for PDF format
 *
 * This class generates a PDF download and sends
 * it to the client.
 *
 * @category	Webenq
 * @package		Webenq
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Download_Pdf extends Webenq_Download_Xls
{
    /**
     * Extension of the file for download.
     *
     * @var string
     */
    protected $_extension = 'pdf';

    /**
	 * The MIME type of the file for download.
	 *
	 * @var string
	 */
	protected $_mimeType = 'application/pdf';

	/**
	 * Returns a writer instance for the given document
	 * type.
	 *
	 * @return PHPExcel_Writer_IWriter
	 */
	protected function _getWriter()
	{
		return PHPExcel_IOFactory::createWriter($this->_document, 'PDF');
	}
}
