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
 * @package    Webenq_Models
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>, Rolf Kleef <r.kleef@nivocer.com>
 */
class Webenq_Model_AnswerDomain extends Webenq_Model_Base_AnswerPossibilityGroup
{
    /*
     * Return the available answer options types
     */
    public static function getAvailableTypes()
    {
        $return=array('choice','numeric','text');
        return $return;
    }
    public static function getAnswerBoxWidthOptions()
    {
        return array(
            4=>"tiny (4)",
                8=>"very small (8)",
                15 =>"small (15)",
                50 =>"medium (50)",
                70 =>"large (70)",
                100 =>"very large (100)",
        );
    }
}