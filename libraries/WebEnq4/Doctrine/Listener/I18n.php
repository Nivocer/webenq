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
 * @package    WebEnq4_I18n
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Listener for the I18n behavior: try to reuse existing translations
 *
 * If there is a multi-lingual text in the database that has the same
 * strings for all the fields and languages we have, assign that id as our
 * translation_id
 *
 * This means that it is possible to save a text in just one language,
 * and get translations in all other available languages 'for free'.
 *
 * @package     WebEnq4_I18n
 * @author      Rolf Kleef <r.kleef@nivocer.com>
 */
class WebEnq4_Doctrine_Listener_I18n extends Doctrine_Record_Listener
{
    /**
     * Before saving the record, check if we can re-use existing translations.
     *
     * @see Doctrine_Record_Listener::preSave()
     */
    public function preSave(Doctrine_Event $event)
    {
        $data = $event->getInvoker();

        // @todo not doing re-use yet, just assign a new id
        if (!isset($data->translation_id) || ($data->translation_id == 0)) {
            $item_id = $this->findTranslation($data);
            if ($item_id) {
                $this->assignTranslationId($data, $item_id);
            } else {
                $this->assignTranslationId($data, $this->newTranslationId());
            }
        } else {
            $this->assignTranslationId($data, $data->translation_id);
        }
    }

    /**
     * Determine a new pseudo-unique id for a translation
     *
     * @return integer New id
     */
    private function newTranslationId()
    {
        return (int) (microtime(true)*1000000);
    }

    /**
     * Assign a new id to a translation
     *
     * @param Object Object with translations
     * @param integer Id to be assigned
     */
    private function assignTranslationId($data, $id)
    {
        $data->translation_id = $id;
        foreach ($data->Translation as $t) {
            $t->id = $id;
        }
    }

    /**
     * Find the best translations we already have
     *
     * Try to find a translation id where non of the fields is conflicting with
     * the fields we have (that are non-empty strings)
     *
     * @param Object Record that holds translations
     * @return mixed Item id with translation to be reused or false if none found
     * @todo Add better ways to check for actual changes?
     * @todo Not possible to store a text without a translation for a field we want to keep empty?
     * @todo Garbage collection?
     */
    private function findTranslation($data)
    {
        // don't re-use for now...
        return false;
    }
}