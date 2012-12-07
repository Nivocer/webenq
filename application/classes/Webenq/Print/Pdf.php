<?php

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
