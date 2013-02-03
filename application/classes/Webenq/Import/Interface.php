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
 * @package    Webenq_Data_Import
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Interface that must be implemented by import classes
 * for importing data into Webenq.
 *
 * @package    Webenq_Data_Import
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
interface Webenq_Import_Interface
{
    /**
     * Class constructor
     *
     * @param Webenq_Import_Adapter_Abstract $adapter
     * @param string $language
     */
    public function __construct(Webenq_Import_Adapter_Abstract $adapter, $language);

    /**
     * The actual method for handling the import.
    */
    public function import();
}