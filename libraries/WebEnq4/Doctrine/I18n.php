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
 * Based on the Doctrine I18n behavior, adapted to support a language table
 * that supports re-use of translations.
 *
 * @package     WebEnq4_I18n
 * @link        www.doctrine-project.org
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author      Rolf Kleef <r.kleef@nivocer.com>
 */
class Webenq4_Doctrine_I18n extends Doctrine_Record_Generator
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
     * __construct
     *
     * @param string $options
     * @return void
     */
    public function __construct($options)
    {
        $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
    }

    public function buildRelation()
    {
        // almost like $this->buildForeignRelation('Translation');
        // but with a different 'local' field
        $options = array(
            'local'    => $this->_options['foreignKey'],
            'foreign'  => $this->getRelationLocalKey(),
            'localKey' => false
        );

        // @todo can probably be removed since we don't do cascading
        if (isset($this->_options['cascadeDelete']) && $this->_options['cascadeDelete'] && $this->_options['appLevelDelete']) {
            $options['cascade'] = array('delete');
        }

        $this->ownerHasMany($this->_table->getComponentName() . ' as Translation', $options);

        // @todo $this->buildLocalRelation() should do a similar thing in the other direction,
        // but the hasMany() leads to "General error: 1005" in defining the foreign key relation
    }

    public function setTableDefinition()
    {
        if (empty($this->_options['fields'])) {
            throw new Doctrine_I18n_Exception('Fields not set.');
        }

        $options = array('className' => $this->_options['className']);

        $cols = $this->_options['table']->getColumns();

        $columns = array();
        $reusableFields = array();
        foreach ($cols as $column => $definition) {
            $fieldName = $this->_options['table']->getFieldName($column);
            if (in_array($fieldName, $this->_options['fields'])) {
                if ($column != $fieldName) {
                    $column .= ' as ' . $fieldName;
                }
                $columns[$column] = $definition;
                $this->_options['table']->removeColumn($fieldName);

                $reusableFields[] = $fieldName;
            }
        }

        $this->hasColumns($columns);

        $defaultOptions = array(
                'fixed' => true,
                'primary' => true
        );
        $options = array_merge($defaultOptions, $this->_options['options']);

        //$this->hasColumn('id', 'integer', 4, array_merge($options, array('autoincrement' => true)));
        $this->hasColumn($this->_options['i18nField'], $this->_options['type'], $this->_options['length'], $options);

        $this->bindQueryParts(array('indexBy' => $this->_options['i18nField']));

        // add a field to the owner class to link here
        // @todo figure out how to do this the Doctrine-way
        $this->_options['table']->setColumn($this->_options['foreignKey'],
            'integer', null, array('unsigned' => true, 'notnull' => true, 'default' => 0)
        );

        // Rewrite any relations to our original table
        // @todo verify that this still works... but we don't have our text fields in relations at the moment
        $originalName = $this->_options['table']->getClassnameToReturn();
        $relations = $this->_options['table']->getRelationParser()->getPendingRelations();
        foreach($relations as $table => $relation) {
            if ($table != $this->_table->getTableName() ) {
                // check that the localColumn is part of the moved col
                if (isset($relation['local']) && in_array($relation['local'], $this->_options['fields'])) {
                    // found one, let's rewrite it
                    $this->_options['table']->getRelationParser()->unsetPendingRelations($table);

                    // and bind the rewritten one
                    $this->_table->getRelationParser()->bind($table, $relation);

                    // now try to get the reverse relation, to rewrite it
                    $rp = Doctrine_Core::getTable($table)->getRelationParser();
                    $others = $rp->getPendingRelation($originalName);
                    if (isset($others)) {
                        $others['class'] = $this->_table->getClassnameToReturn();
                        $others['alias'] = $this->_table->getClassnameToReturn();
                        $rp->unsetPendingRelations($originalName);
                        $rp->bind($this->_table->getClassnameToReturn() ,$others);
                    }
                }
            }
        }
    }
}