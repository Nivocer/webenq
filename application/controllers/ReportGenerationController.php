<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class ReportGenerationController extends Zend_Controller_Action
{
    /**
     * Initialisation
     *
     * @return void
     */
    public function init()
    {
        $this->_id = $this->getRequest()->getParam("id");

        if (!$this->_id) {
            throw new Exception("No id given!");
        }
    }

    protected function _getSubDirs()
    {
        $dirs = array();
        $files = scandir(realpath("reports"));

        foreach ($files as $file) {
            if (is_dir(realpath('reports/' . $file)) && $file !== '.' && $file !== '..' && $file !== 'CVS') {
                $dirs[$file] = $file;
            }
        }

        return $dirs;
    }

    protected function _generateReports($dir)
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV);
        $host    = $config->resources->db->params->host;
        $port    = $config->resources->db->params->port;
        $db        = $config->resources->db->params->dbname;
        $user    = $config->resources->db->params->username;
        $pass    = $config->resources->db->params->password;

        /* remove old report */
        $repDef = new Webenq_Model_DbTable_ReportDefinitions();
        $row = $repDef->find($this->_id)->current();
        $file = $dir . '/' . $row->output_filename . "." . $row->output_format;
        if (file_exists($file)) {
            unlink($file);
        }

        /* prepare */
        if ($row->report_type === 'barcharts') {
            $this->_generateBarcharts($row, $dir);
        }

        /* init vars */
        $cwd = getcwd();
        $output = array();
        $returnVar = 0;

        /* create new report */
        chdir(APPLICATION_PATH . "/../java");
        $cmd = "java -cp .:./lib/* it.bisi.report.jasper.ExecuteReport $host:$port/$db $user $pass $this->_id $dir";
        ob_start();
        passthru($cmd, $returnVar);
        $output = ob_get_contents();
        ob_end_clean();
        chdir($cwd);

        /* error output? */
        if ($returnVar > 0) {
            $this->view->output = $output;
            return;
        }

        /* has file (or multiple files) been created? */
        $file = preg_replace('#^'.realpath('.').'/#', '', $dir) . "/$row->output_filename.$row->output_format";
        if (file_exists($file)) {
            $fileInfo = stat($file);
            $timeDiff = $fileInfo['mtime'] - time();
            if ($timeDiff < 2) {
                $this->view->file = $file;
                return;
            }
        } else {
            $files = scandir($dir);
            $reports = array();
            foreach ($files as $f) {
                $fileName = substr($f, 0, strlen($row->output_filename));
                $fileExt = substr($f, -1 * strlen($row->output_format));
                if ($fileName === $row->output_filename && $fileExt === $row->output_format) {
                    $reports[] = preg_replace('#^'.realpath('.').'/#', '', $dir) . '/' . $f;
                }
            }
            if (count($reports) > 0) {
                $this->view->file = $reports;
                return;
            }
               $this->view->output = "Onbekende fout opgetreden bij het genereren van het rapport.";
        }
    }


    public function indexAction()
    {
        $form = new Webenq_Form_ReportGeneration_Index($this->_getSubDirs());

        if ($this->_helper->form->isPostedAndValid($form)) {
            if ($this->_request->createDir) {
                $destination = realpath('reports/') . '/' . $this->_request->createDir;
                 @mkdir($destination);
            } else {
                $destination = realpath('reports/' . $this->_request->selectDir);
            }
            $this->_generateReports($destination);
        } else {
            $this->view->form = $form;
        }
    }

    protected function _generateBarcharts($row, $dir)
    {
        /* make directory */
        if (!is_dir("$dir/images")) {
            mkdir("$dir/images");
            system("chmod -R 774 $dir/images");
        }

        /* get questions */
        $questionsModel = new Webenq_Model_DbTable_Questions("questions_" . $row->data_set_id);
        $questions = $questionsModel->fetchAll("group_id > 0");
        $splitBy = $row->split_question_id;
        $ignoreQuestionIds = explode(',', $row->ignore_question_ids);

        /* get answers */
        foreach ($questions as $question) {

            if (in_array('"' . $question->id . '"', $ignoreQuestionIds)) {
                continue;
            }

            $answers = $questionsModel->getAnswers($question->id, $splitBy);
            if ($answers instanceof Webenq_Model_Data_Question_Closed_Scale) {
                $filename = "$dir/images/bar_report_" . $row->id . "_question_" . $question->id . ".png";
                $answers->generateBarchart($filename);
                system("chmod -R 774 $filename");
            } elseif (is_array($answers)) {
                foreach ($answers as $answer => $part) {
                    if ($part instanceof Webenq_Model_Data_Question_Closed_Scale) {
                        $filename = "$dir/images/bar_report_" . $row->id . "_question_$question->id" . "_splitanswer_" .
                            "$answer.png";
                        $part->generateBarchart($filename);
                        system("chmod -R 774 $filename");
                    }
                }
            }
        }
    }
}