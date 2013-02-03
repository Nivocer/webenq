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
 * @package    Webenq_Tests
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq_Tests
 */
class Webenq_Test_Model_CollectionPresentationTest extends Webenq_Test_Case_Model
{
    /**
     * Creates a questionnaire-question and tests if the default element
     * type is set correctly.
     */
    public function testDefaultTypeIsSetCorrectly()
    {
        $qq = new Webenq_Model_QuestionnaireQuestion();
        $cp = new Webenq_Model_CollectionPresentation();

        // open
        $qq->type = 'open';
        $cp->setDefaults($qq);
        $this->assertTrue($cp->type == Webenq::COLLECTION_PRESENTATION_OPEN_TEXT);

        // single
        $qq->type = 'single';
        $cp->setDefaults($qq);
        $this->assertTrue($cp->type == Webenq::COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST);

        // multiple
        $qq->type = 'multiple';
        $cp->setDefaults($qq);
        $this->assertTrue($cp->type == Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES);

        // hidden
        $qq->type = 'hidden';
        $cp->setDefaults($qq);
        $this->assertTrue($cp->type == Webenq::COLLECTION_PRESENTATION_OPEN_TEXT);

        // undefined type throws Exception
        $qq->type = 'undefind';
        try {
            $cp->setDefaults($qq);
        } catch (Exception $e) {}
        $this->assertTrue($e instanceof Exception);
    }
}