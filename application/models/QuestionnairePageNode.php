<?php

/**
 * Webenq_Model_QuestionnairePageNode
 *
 *
 * @package    Webenq_Models
 * @subpackage ##SUBPACKAGE##
 * @author     Nivocer <webenq@nivocer.com>
 * @version    SVN: $Id: Builder.php,v 1.2 2011/07/12 13:39:03 bart Exp $
 */
class Webenq_Model_QuestionnairePageNode extends Webenq_Model_Base_QuestionnairePageNode
{
    public function render($format)
    {
        switch ($format){
            // @todo we want to use a decorator, but it not working yet
            case 'previewTab':
                $return='<li><a href=#pageId-'.$this->id.'>';
                $return.= $this-> QuestionnaireElement->getTranslation('text');
                $return.='</a></li>';
                return $return;
            break;
            default:
                $return=new Zend_Form_SubForm();
                $return->setName('group-'.$this->id, new Zend_Loader_PluginLoader());
                return $return;
                return parent::render($format);
                /*$this->QuestionnaireElement->name ."</br>";
                if ($this->getNode()->hasChildren()) {
                    foreach ($this->getNode()->getChildren() as $node) {
                        $return.= $node->render($format);
                    }
                }
                return $return;
                */
            break;
        }
    }
}