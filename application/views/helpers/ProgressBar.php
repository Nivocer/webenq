<?php
class Zend_View_Helper_ProgressBar extends Zend_View_Helper_Abstract
{
	/**
	 * Renders a progressbar
	 * 
	 * @param array $data Can contain the keys 'total', 'ready' and 'percentage'
	 */
	public function progressBar($data)
	{
		$percentage = 0;
		
		if (isset($data['percentage'])) {
			$percentage = round($data['percentage']);
		} else if (isset($data['total'])) {
			if (isset($data['ready']) && (int) $data['ready'] > 0) {
				$percentage = round(100 / ($data['total'] / $data['ready']));
			}
		}
		
		$js = "$('#progressbar').progressbar({value: $percentage});";
		$this->view->jQuery()->addOnLoad($js);
		
		return '<div id="progressbar"></div>';
	}
}