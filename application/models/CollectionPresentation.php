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
 * CollectionPresentation
 *
 * @package    Webenq_Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_CollectionPresentation extends Webenq_Model_Base_CollectionPresentation
{
    public function setUp()
    {
        parent::setUp();
        $this->hasMany(
            'Webenq_Model_CollectionPresentation as Children',
            array(
                'local' => 'parent_id',
                'foreign' => 'id',
            )
        );
        $this->hasOne(
            'Webenq_Model_CollectionPresentation as Parent',
            array(
                'local' => 'id',
                'foreign' => 'parent_id',
            )
        );
    }

    /**
     * Sets the defaults based on the given questionnaire question
     *
     * @param Webenq_Model_QuestionnaireQuestion $questionnaireQuestion
     * @return void
     */
    public function setDefaults(Webenq_Model_QuestionnaireQuestion $questionnaireQuestion)
    {
        switch ($questionnaireQuestion->type) {
            case 'open':
                $this->type = Webenq::COLLECTION_PRESENTATION_OPEN_TEXT;
                break;
            case 'single':
                $this->type = Webenq::COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST;
                break;
            case 'multiple':
                $this->type = Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES;
                break;
            case 'hidden':
                $this->type = Webenq::COLLECTION_PRESENTATION_OPEN_TEXT;
                break;
            default:
                throw new Exception('No question type set!');
        }
    }

    /**
     * Returns the parent of the current collection presentation object, or false
     * if there isn't one
     *
     * @return Webenq_Model_CollectionPresentation
     */
    public function getParent()
    {
        return Doctrine_Core::getTable('Webenq_Model_CollectionPresentation')
            ->find($this->parent_id);
    }

    /**
     * Returns the children of the current collection presentation object
     *
     * @return Doctrine_Collection
     */
    public function getChildren()
    {
        return Doctrine_Core::getTable('Webenq_Model_CollectionPresentation')
            ->findByParent_id($this->id);
    }

    /**
     * Returns an array of all the ancestors of the current collection presentation
     * object, or an empty array if there aren't any
     *
     * @return array
     */
    public function getParents(array $parents = array())
    {
        $parent = $this->getParent();
        if ($parent) {
            $parents[] = $parent;
            $parent->getParents($parents);
        }
        return $parents;
    }
}
