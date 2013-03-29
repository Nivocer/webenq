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
 * Add multilingual capabilities to your Doctrine models.
 *
 * <code>
 * Questionnaire:
 *   actAs:
 *     WebEnq4_Template_I18n:
 *       fields: [title]
 *   columns:
 *     title: string
 *     active: boolean
 * </code>
 *
 * Based on Doctrine I18n, adapted to support re-use of translations.
 *
 * @package     WebEnq4_I18n
 * @author      Rolf Kleef <r.kleef@nivocer.com>
 */
class WebEnq4_Doctrine_I18n extends Doctrine_I18n
{
    protected $_options = array(
                            'className'     => '%CLASS%Translation',
                            'tableName'     => '%TABLE%_translation',
                            'foreignKey'    => 'translation_id',
                            'fields'        => array(),
                            'generateFiles' => false,
                            'table'         => false,
                            'pluginTable'   => false,
                            'children'      => array(),
                            'i18nField'     => 'lang',
                            'type'          => 'string',
                            'length'        => 2,
                            'options'       => array(),
                            'cascadeDelete' => false, // translations can be used by multiple records
                            'appLevelDelete'=> false
                            );

    /**
     * Adds a translation_id column to the (semi)owner table and returns it as
     * the intended foreign key for the translations table.
     * The column is automatically added to the generated translation model so
     * we can create foreign keys back to the table object that refers to it.
     *
     * @param Doctrine_Table $table     the table object that owns the plugin
     * @return array                    an array of foreign key definitions
     */
    public function buildForeignKeys(Doctrine_Table $table)
    {
        $foreignKey = $this->_options['foreignKey'];

        // add a field in the calling table, to link to translation records
        $table->setColumn($foreignKey, 'integer', null,
            array('unsigned' => false, 'notnull' => true, 'default' => 0)
        );
        // make it an index, so we can use it as foreign key
        $table->addIndex($foreignKey, array('fields' => array($foreignKey)));

        $def = $table->getColumnDefinition($foreignKey);
        $def['primary'] = true;
        $fk['id'] = $def;

        return $fk;
    }

    /**
     * Build the local relationship on the generated model for this generator
     * instance which points to the invoking table in $this->_options['table']
     *
     * @param string $alias Alias of the foreign relation
     * @return void
     */
    public function buildLocalRelation($alias = null)
    {
        $options = array(
                'local'      => $this->getRelationLocalKey(),
                'foreign'    => $this->getRelationForeignKey(),
        );

        $this->hasMany($this->_options['table']->getComponentName(), $options);
    }

    /**
     * Get the local key of the generated relationship
     *
     * @return string $local
     */
    public function getRelationLocalKey()
    {
        return 'id';
    }

    /**
     * Get the foreign key of the generated relationship
     *
     * @return string $foreign
     */
    public function getRelationForeignKey()
    {
        return $this->_options['foreignKey'];
    }
}