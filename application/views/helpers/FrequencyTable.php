<?php
/**
 * Helper class for rendering a frequency table
 */
class Zend_View_Helper_FrequencyTable extends Zend_View_Helper_Abstract
{
    public function frequencyTable(Webenq_Model_QuestionnaireQuestion $qq)
    {
        $frequency = array();
        foreach ($qq['Answer'] as $answer) {
            if (key_exists($answer['answerPossibility_id'], $frequency)) {
                $frequency[$answer['answerPossibility_id']]++;
            } else {
                $frequency[$answer['answerPossibility_id']] = 1;
            }
        }

        $html = '
            <table>
                <tbody>
                    <tr>
                        <th>' . t('id') . '</th>
                        <th>' . t('label') . '</th>
                        <th>' . t('value') . '</th>
                        <th>' . t('count') . '</th>
                    </tr>';

        $hasRows = false;
        foreach ($frequency as $id => $count) {

            $answerPossibility = Doctrine_Core::getTable('Webenq_Model_AnswerPossibility')->find($id);

            if ($answerPossibility) {
                $hasRows = true;
                $html .= '
                        <tr>
                            <td>' . $answerPossibility->id . '</td>
                            <td>' . $answerPossibility->AnswerPossibilityText[0]->text . '</td>
                            <td>' . $answerPossibility->value . '</td>
                            <td>' . $count . '</td>
                        </tr>';
            }
        }

        $html .= '
                </tbody>
            </table>';

        if ($hasRows) return $html;
    }
}