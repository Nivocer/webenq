<?php

class Zend_View_Helper_Messenger extends Zend_View_Helper_Abstract
{
    public function messenger(array $messages)
    {
        if (count($messages) > 0) {
            $html = '';
            foreach ($messages as $message) {
                $html .= "<span>$message</span>";
            }
            
            return "<div id=\"flash_messenger\">$html</div>";
        }
    }
}