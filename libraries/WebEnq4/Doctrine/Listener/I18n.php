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
 * @package    WebEnq4
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Listener for the I18n behavior: for now just stores the translation strings
 * but is intented to take care about re-using translations.
 *
 * @package     WebEnq4
 * @author      Rolf Kleef <r.kleef@nivocer.com>
 */
class WebEnq4_Doctrine_Listener_I18n extends Doctrine_Record_Listener
{
    /**
     * Array of options
     *
     * @var string
     */
    protected $_options = array();

    /**
     * __construct
     *
     * @param string $options
     * @return void
     */
    public function __construct(array $options)
    {
        $this->_options = $options;
    }

    public function preSave(Doctrine_Event $event)
    {
        $data = $event->getInvoker();

        // @todo eventually check if we have a proper translation already
        // for now: see if the translation has an id, assign time() if not
        // (this leads to problems when two save()'s happen at the same time)
        if (!isset($data->translation_id) || ($data->translation_id == 0)) {
            if ((count($data->Translation) > 0)
            && ($data->Translation->getFirst()->id == 0)) {
                $data->translation_id = time();
            }
        }

        // not a beauty either... make sure the translations have the id we think they have
        foreach ($data->Translation as $t) {
            $t->id = $data->translation_id;
        }
    }
}