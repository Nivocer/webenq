<?php

/**
 * Webenq_Model_Base_ReportElement
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $report_id
 * @property string $data
 * @property integer $sort
 * @property Webenq_Model_Report $Report
 * 
 * @package    Webenq_Models
 * @subpackage 
 * @author     Nivocer <webenq@nivocer.com>
 * @version    SVN: $Id: Builder.php,v 1.2 2011/07/12 13:39:03 bart Exp $
 */
abstract class Webenq_Model_Base_ReportElement extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('reportElement');
        $this->hasColumn('report_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('data', 'string', null, array(
             'type' => 'string',
             'notnull' => false,
             ));
        $this->hasColumn('sort', 'integer', 4, array(
             'type' => 'integer',
             'default' => 0,
             'length' => '4',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Webenq_Model_Report as Report', array(
             'local' => 'report_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE',
             'foreignKeyName' => 'reportElement_report_id_fk'));
    }
}