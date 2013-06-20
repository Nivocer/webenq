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
 * @package    Webenq_Models
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Answer domain class definition
 *
 *
 * @package    Webenq_Models
 * @subpackage
 * @author     Nivocer <webenq@nivocer.com>
 */
class Webenq_Model_AnswerDomainChoice extends Webenq_Model_Base_AnswerDomainChoice
{
    /**
     * @var array Information about the items of a choice element
     */
    public $_items;

//todo merge with getAvailablePresentations (not yet implemented in this class)
    public static function getAvailablePresentations()
    {
        return array(
            'radio' => array(
                'label' => 'Ask as single choice list',
                'element'=>'Zend_Form_Element_Radio',
            ),
                'pulldown' => array(
                'label' => 'Ask as pulldown list',
                'element'=>'Zend_Form_Element_Select',
            ),
            'checkbox' => array(
                'label' => 'Ask as multiple choice list',
                'element'=>'Zend_Form_Element_MultiCheckbox',
            ),
            'textComplete' => array(
                'label' => 'Ask as open text (with autocomplete)',
                'element'=>'ZendX_JQuery_Form_Element_AutoComplete'
            ),
            'input' => array(
                'label' => 'Ask as open text',
                'element'=>'Zend_Form_Element_Text'
            ),
            'slider' => array(
                'label' => 'Ask as a slider',
                'element'=>'ZendX_JQuery_Form_Element_Slider'
            ),
        );
    }

    /**
     * Fills array with answer domain items, and adds translations
     *
     * @param bool $deep
     * @param bool $prefixKey Not used
     * @return array
     * @see Doctrine_Record::toArray()
     */
    public function toArray($deep = true, $prefixKey = false)
    {
        $result = parent::toArray($deep, $prefixKey);

        if ($deep) {
            if (!isset($this->_items) && isset($this->AnswerDomainItem)) {
                $items = $this->AnswerDomainItem->getNode()->getDescendants();
                if ($items) {
                    $this->_items = $items->toArray();
                }
            }

            if (isset($this->_items)) {
                $result['items'] = $this->_items;
            }

            // @todo We should find a way to do this via the I18n behavior, of find out why 'deep=true' doesn't do this
            $result['Translation'] = $this->Translation->toArray();
        }

        return $result;
    }

    /**
     * refresh
     * remove the locally stored array with info on sub items, then
     * refresh internal data from the database
     *
     * @param bool $deep
     * @throws Doctrine_Record_Exception
     * @return boolean
     * @see Doctrine_Record::refresh()
     */
    public function refresh($deep = false)
    {
        unset($this->_items);
        parent::refresh($deep);
    }

    /**
     * Imports data from a php array. If an array element 'items' exists, it
     * is stored in the object assuming this contains information about the
     * sub items of the tree root of answer domain items. This information is
     * processed in the save() function.
     *
     * @param string $array  array of data, see link for documentation
     * @param bool   $deep   whether or not to act on relations
     * @return void
     * @see Doctrine_Record::fromArray()
     */
    public function fromArray(array $array, $deep = true)
    {
        if ($deep) {
            if (isset($array['items'])) {
                // keep a local copy assuming
                $this->_items = $array['items'];
            }
        }

        parent::fromArray($array, $deep);
    }

    /**
     * Save this choice element
     *
     *
     *
     */
    public function save(Doctrine_Connection $conn = null)
    {

        if ($this->AnswerDomainItem->isModified(true)) {
            if (1 < Doctrine_Query::create()
            ->select('COUNT(id)')
            ->from('Webenq_Model_AnswerDomainChoice adc')
            ->where('adc.answer_domain_item_id = ?', $this->AnswerDomainItem->id)
            ->count()) {
                $this->AnswerDomainItem = $this->AnswerDomainItem->copy();
            }
        }

        parent::save($conn);
    }

    public function getAnswerOptionsArray(){

        foreach ($this->AnswerDomainItem->getNode()->getChildren() as $answerItem) {
            $return[$answerItem->id]=$answerItem->getTranslation('label');
        }
        return $return;
    }
}