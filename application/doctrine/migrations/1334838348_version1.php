<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version1 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'report',
            'orientation',
            'enum',
            '1',
            array(
                'values' =>
                 array(
                      0 => 'a',
                      1 => 'p',
                      2 => 'l',
                 ),
                'default' => 'a',
                 'notnull' => '1',
             )
        );
    }

    public function down()
    {
        $this->removeColumn('report', 'orientation');
    }
}