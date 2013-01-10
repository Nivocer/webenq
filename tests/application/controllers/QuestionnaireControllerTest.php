<?php
/**
 * WebEnq4 Library
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
 * @category   Webenq
 * @package    Webenq_Controllers
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

class Webenq_Test_ControllerTestCase_QuestionnaireControllerTest extends Webenq_Test_Case_Controller
{
    public function testCorrectControllerIsUsed()
    {
        $this->dispatch('/questionnaire');
        $this->assertController('questionnaire');
    }
    public function testCorrectActionIsUsedIndex()
    {
        $this->dispatch('/questionnaire/index');
        $this->assertAction("index");
    }
    public function testCorrectActionIsUsedXform()
    {
        $this->dispatch('/questionnaire/xform/id/1');
        $this->assertAction("xform");
    }
    public function testCorrectActionIsUsedXformData()
    {
        $this->dispatch('/questionnaire/xform-data/id/1');
        $this->assertAction("xform-data");
    }
    public function testCorrectActionIsUsedAdd()
    {
        $this->dispatch('/questionnaire/add');
        $this->assertAction("add");
    }
    public function testCorrectActionIsUsedEdit()
    {
        $this->dispatch('/questionnaire/edit/id/1');
        $this->assertAction("edit");
    }
    public function testCorrectActionIsUsedOrder()
    {
        $this->dispatch('/questionnaire/order');
        $this->assertAction("order");
    }
    /*
    * @todo inactive test, don't yet know the needed input
    */
    /*
    public function testCorrectActionIsUsedaddQuestion()
    {
        $this->dispatch('/questionnaire/add-question');
        $this->assertAction("addQuestion");
    }
    */
    public function testCorrectActionIsUsedDelete()
    {
        $this->dispatch('/questionnaire/delete/id/1');
        $this->assertAction("index");// index is added to action stack
    }
    /* @todo inactive test, we need to change this controller
     */
     /*public function testCorrectControlleIsUsedCollect()
    {
        $this->dispatch('/questionnaire/collect/id/1');
        $this->assertAction("collect");
    }
    */
    public function testCorrectActionIsUsedDownload()
    {
        $this->dispatch('/questionnaire/download/id/1');
        $this->assertAction("download");
    }
    public function testCorrectActionIsUsedPrint()
    {
        $this->dispatch('/questionnaire/print/id/1');
        $this->assertAction("print");
    }
    public function testQuestionnaireViewIsRendered()
    {
        $this->loadDatabase();
        $this->dispatch('/questionnaire');
        //$this->assertQuery('tbody.questionnaire');

    }
}