<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class ManagementController extends Zend_Controller_Action
{
    /**
     * Id of imported dataset
     */
    protected $_id;


    /**
     * Array of question objects
     */
    protected $_questions = array();


    /**
     * Initialisation
     *
     * @return void
     */
    public function init()
    {
        $this->_id = $this->_request->id;
        if (!$this->_id) {
            throw new Exception("No id given!");
        }
    }


    /**
     * Renders the overview of question types
     *
     * @return void
     */
    public function indexAction()
    {
        /* get questions */
        $questions = Doctrine_Query::create()
            ->from('QuestionnaireQuestion qq')
            ->innerJoin('qq.CollectionPresentation cp')
            ->where('qq.questionnaire_id = ?', $this->_request->id)
            ->orderBy('cp.weight, qq.id')
            ->execute();

        $this->view->questions = $questions;
    }

    /**
     * Renders the form to edit question types
     *
     * @return void
     */
    public function editAction()
    {
        /* get questionnaireQuestion */
        $qq = Doctrine_Core::getTable('QuestionnaireQuestion')->find($this->_id);

        /* build form */
        $form = new Webenq_Form_Management_Edit($qq);

        /* process form */
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {


                /* process */
                $meta = ($qq->meta) ? unserialize($qq->meta) : array();
                $valid = $meta['valid'];
                $meta['class'] = $valid[$data['class']];
                $qq->meta = serialize($meta);
                $qq->save();

//                $this->_convertLabelsToValues();

                /* to home-page */
                $this->_redirect("/management/index/id/" . $qq->Questionnaire->id);
            }
        }

        /* assign vars to view */
        $this->view->form = $form;
    }


    /**
     * Converts labels to values, based on question types
     */
    protected function _convertLabelsToValues()
    {
        /* get table models */
        $meta = new Webenq_Model_DbTable_Meta("meta_" . $this->_id);

        /* query for building table */
        $table = "values_" . $this->_id;
        $q = "CREATE TABLE " . $table . " (
            id INT NOT NULL, PRIMARY KEY (id), ";

        /* query for question types */
        $questionTypes = $meta->fetchAll("parent_id = 0", "id");
        foreach ($questionTypes as $questionType) {
            switch ($questionType->type) {
                case "Webenq_Model_Data_Question_Open_Date":
                    $q .= $questionType->question_id . " DATETIME DEFAULT NULL, ";
                    break;
                case "Webenq_Model_Data_Question_Closed_Scale_Two":
                case "Webenq_Model_Data_Question_Closed_Scale_Three":
                case "Webenq_Model_Data_Question_Closed_Scale_Four":
                case "Webenq_Model_Data_Question_Closed_Scale_Five":
                case "Webenq_Model_Data_Question_Closed_Scale_Six":
                case "Webenq_Model_Data_Question_Closed_Scale_Seven":
                    $q .= $questionType->question_id . " INT DEFAULT NULL, ";
                    break;
                default:
                    $q .= $questionType->question_id . " TEXT DEFAULT NULL, ";
                    break;
            }
        }
        $q = substr($q, 0, -2) . ") DEFAULT CHARSET=utf8;";

        /* get db connection */
        $db = Zend_Db_Table::getDefaultAdapter();
        $dbConnection = $db->getConnection();
        $dbConnection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

        /* create table */
        $dbConnection->exec("DROP TABLE IF EXISTS " . $table . ";");
        $dbConnection->exec($q);

        /* get labels and store values */
        $labels = new Webenq_Model_DbTable_Data("data_" . $this->_id);
        $values = new Webenq_Model_DbTable_Data("values_" . $this->_id);

        foreach ($labels->fetchAll() as $row) {
            $data = $row->toArray();
            foreach ($data as $questionId => $answer) {
                $type = $meta->fetchRow("question_id = '$questionId'");
                if ($type) {
                    if (preg_match("#^Webenq_Model_Data_Question_Open_Date#", $type->type)) {
                        $data[$questionId] = Webenq_Model_Data_Question_Open_Date::toFormat($answer, "Y-m-d H:i:s");
                    }
                    if (preg_match("#^Webenq_Model_Data_Question_Closed_Scale_#", $type->type)) {
                        $scaleValues = Webenq_Model_Data_Question_Closed_Scale::getScaleValues();
                        @$value = $scaleValues[$type->type][strtolower($answer)];
                        if (!$value) $value = -1;
                        $data[$questionId] = $value;
                    }
                }
            }
            $values->insert($data);
        }
    }
}