<?php
/**
 * Webenq
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
 * @package    Webenq_Output
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
* @package    Webenq_Output
*/
class Webenq_Print_Pdf extends Webenq_Print
{
    const PDF_MAX_WIDTH = 190;

    const PDF_BORDER_NOBORDER = 0;
    const PDF_BORDER_FRAME = 1;

    const PDF_LN_RIGHT = 0;
    const PDF_LN_NEXTLINE = 1;
    const PDF_LN_BELOW = 2;

    const PDF_ALIGN_LEFT = 'L';
    const PDF_ALIGN_CENTER = 'C';
    const PDF_ALIGN_RIGHT = 'R';

    const PDF_DEST_STRING = 'S';

    protected $_downloadFilename = 'questionnaire.pdf';

    protected $_mimeType = 'application/pdf';

    protected $_document;

    protected function _getWriter()
    {
        return PHPExcel_IOFactory::createWriter($this->_document, 'PDF');
    }

    public function init()
    {
        /* load library */
        require_once 'fpdf/fpdf.php';

        /* get questionnaire */
        $questionnaire = $this->_questionnaire;

        /* create new document */
        $this->_document = $pdf = new FPDF();
        $pdf->addPage();
        $pdf->setFont('Arial', '', 12);

        /* questions */
        foreach ($questionnaire['QuestionnaireQuestion'] as $questionnaireQuestion) {

            $value = $questionnaireQuestion['Question']['QuestionText'][0]['text'];
            if ($pdf->GetStringWidth($value) < self::PDF_MAX_WIDTH) {
                $pdf->Cell(0, 5, $value, self::PDF_BORDER_NOBORDER, self::PDF_LN_NEXTLINE);
            } else {
                while ($value) {
                    $rest = '';
                    while ($pdf->GetStringWidth($value) >= self::PDF_MAX_WIDTH) {
                        preg_match('/^(.*)\s(.*)$/', $value, $matches);
                        $value = $matches[1];
                        $rest = $matches[2] . ' ' . $rest;
                    }
                    $pdf->Cell(0, 5, $value, self::PDF_BORDER_NOBORDER, self::PDF_LN_NEXTLINE);
                    $value = $rest;
                    $rest = null;
                }
            }

            /* answer possibilities (if any) */
            if (isset($questionnaireQuestion['AnswerPossibilityGroup'])) {
                foreach ($questionnaireQuestion['AnswerPossibilityGroup']['AnswerPossibility'] as $answerPossibility) {
                    $value = '     O - ' . $answerPossibility['AnswerPossibilityText'][0]['text'];
                    $pdf->Cell(0, 5, $value, self::PDF_BORDER_NOBORDER, self::PDF_LN_NEXTLINE);
                }
            } else {
                $value = '__________________________________________________';
                $pdf->Cell(0, 5, $value, self::PDF_BORDER_NOBORDER, self::PDF_LN_NEXTLINE);

            }

            $pdf->Ln();
        }

        $this->_document = $pdf;

        return $this;
    }

    public function send(Zend_Controller_Response_Http $response)
    {
        $response
        ->setHeader('Content-Transfer-Encoding', 'binary')
        ->setHeader('Content-Type', $this->getMimeType())
        ->setHeader('Content-Disposition', 'attachment; filename="' . $this->getDownloadFilename() . '"')
        ->setBody($this->_document->output(null, self::PDF_DEST_STRING));
    }
}
