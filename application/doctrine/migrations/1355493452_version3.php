<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version3 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('category', array(
             'id' => 
             array(
              'type' => 'integer',
              'fixed' => '0',
              'unsigned' => '1',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '4',
             ),
             'active' => 
             array(
              'type' => 'integer',
              'fixed' => '0',
              'unsigned' => '',
              'primary' => '',
              'default' => '1',
              'notnull' => '1',
              'autoincrement' => '',
              'length' => '1',
             ),
             ), array(
             'primary' => 
             array(
              0 => 'id',
             ),
             ));
        $this->createTable('categoryText', array(
             'id' => 
             array(
              'type' => 'integer',
              'fixed' => '0',
              'unsigned' => '1',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '4',
             ),
             'text' => 
             array(
              'type' => 'string',
              'fixed' => '0',
              'unsigned' => '',
              'primary' => '',
              'notnull' => '1',
              'autoincrement' => '',
              'length' => '255',
             ),
             'language' => 
             array(
              'type' => 'string',
              'fixed' => '0',
              'unsigned' => '',
              'primary' => '',
              'notnull' => '1',
              'autoincrement' => '',
              'default' => 'en',
              'length' => '2',
             ),
             'category_id' => 
             array(
              'type' => 'integer',
              'fixed' => '0',
              'unsigned' => '1',
              'primary' => '',
              'notnull' => '1',
              'autoincrement' => '',
              'length' => '4',
             ),
             ), array(
             'primary' => 
             array(
              0 => 'id',
             ),
             ));
        $this->addColumn('questionnaire', 'category_id', 'integer', '4', array(
             'fixed' => '0',
             'unsigned' => '1',
             'primary' => '',
             'notnull' => '',
             'autoincrement' => '',
             ));
        $this->addColumn('questionnaire', 'default_language', 'string', '2', array(
             'fixed' => '0',
             'unsigned' => '',
             'primary' => '',
             'default' => 'en',
             'notnull' => '1',
             'autoincrement' => '',
             ));
        $this->addColumn('questionnaire', 'date_start', 'timestamp', '25', array(
             'fixed' => '0',
             'unsigned' => '',
             'primary' => '',
             'notnull' => '1',
             'autoincrement' => '',
             ));
        $this->addColumn('questionnaire', 'date_end', 'timestamp', '25', array(
             'fixed' => '0',
             'unsigned' => '',
             'primary' => '',
             'default' => '0000-00-00 00:00:00',
             'notnull' => '1',
             'autoincrement' => '',
             ));
        $this->addColumn('questionnaire', 'active', 'integer', '1', array(
             'fixed' => '0',
             'unsigned' => '',
             'primary' => '',
             'default' => '1',
             'notnull' => '1',
             'autoincrement' => '',
             ));
    }

    public function down()
    {
        $this->dropTable('category');
        $this->dropTable('categoryText');
        $this->removeColumn('questionnaire', 'category_id');
        $this->removeColumn('questionnaire', 'default_language');
        $this->removeColumn('questionnaire', 'date_start');
        $this->removeColumn('questionnaire', 'date_end');
        $this->removeColumn('questionnaire', 'active');
    }
}