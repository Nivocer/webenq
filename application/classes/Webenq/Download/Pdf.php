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
 * @category   WebEnq4
 * @package    WebEnq4_Data
 * @subpackage Export
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Downloader class for PDF format
 *
 * This class generates a PDF download and sends
 * it to the client.
 *
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
