<?php
/**
 * Helper class that returns the right language from a collection
 * of doctrine objects
 */
class Zend_View_Helper_FrequencyTable extends Zend_View_Helper_Abstract
{
	public function frequencyTable(QuestionnaireQuestion $qq)
	{
		$frequency = array();
		foreach ($qq->Answer as $answer) {
			if (key_exists($answer->answerPossibility_id, $frequency)) {
				$frequency[$answer->answerPossibility_id]++;
			} else {
				$frequency[$answer->answerPossibility_id] = 1;
			}
		}
		
		$html = '
			<table>
				<tbody>
					<tr>
						<th>' . _('id') . '</th>
						<th>' . _('label') . '</th>
						<th>' . _('waarde') . '</th>
						<th>' . _('aantal') . '</th>
					</tr>';
		
		$hasRows = false;
		foreach ($frequency as $id => $count) {
			
			$answerPossibility = Doctrine_Core::getTable('AnswerPossibility')
				->find($id);
				
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