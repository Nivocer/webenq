<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class ToolController extends Zend_Controller_Action
{
    public function hvaAction()
    {
        $form = new Webenq_Form_Tool_Hva(array('xls', 'xlsx'));
        $errors = array();

        if ($this->_helper->form->isPostedAndValid($form)) {

            // make sure enough resources are assigned
            try {
                Webenq::setMemoryLimit('512M');
                Webenq::setMaxExecutionTime(0);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }

            // receive the file
            if ($form->file->receive()) {
                $filename = $form->file->getFileName();
            } else {
                $errors[] = 'Error receiving the file';
            }

            if (empty($errors)) {
                $adapter = Webenq_Import_Adapter_Abstract::factory($filename);
                $data = $adapter->getData();
                $this->_processHvaData($data);
            }
        }

        $this->view->errors = $errors;
        $this->view->form = $form;
    }

    protected function _processHvaData(array $data)
    {
        // get modules
        $modules = $this->_getModules($data);

        // get respondents
        $respondents = $this->_getRespondents($data);

        // get the attended modules per respondent
        $respondentsModules = $this->_getRespondentsModules($data, $respondents, $modules);

        // get start columns for module-related answers
        $moduleDataColumns = $this->_getModuleDataColumns($data, $modules);

        // build new data
        $new = $this->_getNewData($data, $respondents, $modules, $respondentsModules, $moduleDataColumns);
        $newFirstSheet = $new[0];

        // return file for download
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $download = new Webenq_Download_Xls();
        $download->setData($newFirstSheet)->init();
        $download->send($this->_response);
    }

    /**
     * Returns the modules found in the data
     *
     * @param array $data All data
     * @return array Modules
     */
    protected function _getModules(array $data)
    {
        $modules = array();
        $moduleNamePattern = '/^1:\s(.*)/';
        foreach ($data[0][0] as $column => $header) {
            if (preg_match($moduleNamePattern, $header, $matches)) {
                $modules[$column] = $matches[1];
            }
        }
        return $modules;
    }

    /**
     * Returns the respondents found in the data
     *
     * @param array $data All data
     * @return array Respondents
     */
    protected function _getRespondents(array $data)
    {
        $respondents = array();
        $respondentColumn = $this->_getRespondentColumn($data);
        foreach ($data[0] as $i => $row) {
            if ($i == 0) continue;
            $respondents[] = $row[$respondentColumn];
        }
        return $respondents;
    }

    /**
     * Returns the column index that contains the respondents
     *
     * @param array $data All data
     * @return int
     */
    protected function _getRespondentColumn(array $data)
    {
        $respondentHeaderPattern = '/^Respondent$/';
        foreach ($data[0][0] as $respondentColumn => $header) {
            if (preg_match($respondentHeaderPattern, $header)) {
                return $respondentColumn;
            }
        }
        return false;
    }

    /**
     * Returns the modules attended by respondents found in the data
     *
     * @param array $data All data
     * @param array $respondents Respondents
     * @param array $modules Modules
     * @return array RespondentsModules
     */
    protected function _getRespondentsModules($data, $respondents, $modules)
    {
        $respondentsModules = array();
        foreach ($data[0] as $i => $row) {
            if ($i === 0) continue;
            foreach ($modules as $col => $val) {
                if ((bool) $row[$col]) {
                    $respondent = $row[$this->_getRespondentColumn($data)];
                    $respondentsModules[$respondent][] = $val;
                }
            }
        }
        return $respondentsModules;
    }

    /**
     * Returns the new data
     *
     * @param array $data All data
     * @param array $respondents Respondents
     * @param array $modules Modules
     * @param array $moduleDataColumns Start columns of modules
     * @return array
     */
    protected function _getNewData($data, $respondents, $modules, $respondentsModules, $moduleDataColumns)
    {
        $new = array();

        // copy headers from original data
        $new[0][0] = $data[0][0];

        // add rows for each respondent/module
        foreach ($respondentsModules as $respondent => $respondentModules) {
            foreach ($respondentModules as $key => $module) {

                $newRow = array();

                $startColumn = $moduleDataColumns[$module]['start'];
                $endColumn = $moduleDataColumns[$module]['end'];

                $row = $this->_getRespondentRow($respondent, $data);
                foreach ($row as $column => $value) {
                    if ($this->_isModuleColumn($column, $moduleDataColumns, $data)) {
                        if ($column >= $startColumn && $column <= $endColumn) {
                            $newRow[$column] = $value;
                        } else {
                            $newRow[$column] = null;
                        }
                    } else {
                        $newRow[$column] = $value;
                    }
                }
                $newRow[] = $module;
                ksort($newRow);
                $new[0][] = $newRow;
            }
        }

        // merge modules (if they have the same questions)
        $this->_mergeModules($new, $moduleDataColumns);

        // add module header to first row
        $new[0][0][] = 'module';

        // remove all module atendency columns and reset keys for all rows
        foreach ($new[0] as $row => $void) {
            foreach ($modules as $column => $module) {
                unset($new[0][$row][$column]);
            }
            $new[0][$row] = array_merge($new[0][$row], array());
        }

        return $new;
    }

    /**
     * Merges the modules if the question texts are the same
     *
     * @param array &$data
     * @param array &$moduleDataColumns
     * @return array
     */
    protected function _mergeModules(array &$data, array &$moduleDataColumns)
    {
        $questions = $data[0][0];
        foreach ($moduleDataColumns as $module => &$columns) {
            foreach ($questions as $column => &$question) {
                if (preg_match("/^(\d*:\s*)$module:\s*(.*)$/", $question, $matches)) {
                    $question = $matches[1] . $matches[2];
                    if ($column === 1 + $columns['end']) $columns['end']++;
                }
            }
        }

        // find double questions
        $uniqueModuleQuestions = array();
        foreach ($questions as $column => $question) {
            if ($this->_isModuleColumn($column, $moduleDataColumns, $data)) {
                if (preg_match('/d*:\s*(.*)$/', $question, $matches)) {
                    if (isset($uniqueModuleQuestions[$matches[1]])) {
                        $uniqueModuleQuestions[$matches[1]][] = $column;
                    } else {
                        $uniqueModuleQuestions[$matches[1]] = array($column);
                    }
                }
            }
        }

        // merge questions
        $row = &$data[0][0];
        foreach ($uniqueModuleQuestions as $question => $columns) {
            $first = current($columns);
            foreach ($columns as $column) {
                if ($column === $first) {
                    $row[$column] = $question;
                } else {
                    unset($row[$column]);
                }
            }
        }

        // merge answers
        foreach ($data[0] as $i => &$row) {
            if ($i === 0) continue;
            foreach ($uniqueModuleQuestions as $question => $columns) {
                $first = current($columns);
                foreach ($columns as $column) {
                    if ($column > $first) {
                        if (!$row[$first] && $row[$column]) {
                            $row[$first] = $row[$column];
                        }
                        unset($row[$column]);
                    }
                }
            }
        }
    }

    /**
     * Returns the skeleton for the new data
     *
     * @param array $data All data
     * @param array $respondents Respondents
     * @param array $modules Modules
     * @return array
     */
    protected function _getModuleDataColumns($data, $modules)
    {
        $groups = array();
        foreach ($data[1] as $row) {
            $definition = $row[0];
            foreach ($modules as $name) {
                $pattern = "/^(\d*):.*($name)/";
                if (preg_match($pattern, $definition, $matches)) {
                    $groups[$matches[1]] = $matches[2];
                }
            }
        }

        $columns = array();
        foreach ($data[0][0] as $column => $header) {
            foreach ($groups as $key => $name) {
                $pattern = "/^$key:.*$/";
                if (preg_match($pattern, $header, $matches)) {
                    if (!isset($columns[$name]))
                        $columns[$name] = array('start' => $column);
                    $columns[$name]['end'] = $column;
                }
            }
        }
        return $columns;
    }

    /**
     * Checks if the given column number is for module answers
     *
     * @param int $column
     * @param array $moduleDataColumns
     * @param array $data
     * @return bool
     */
    protected function _isModuleColumn($column, $moduleDataColumns, $data)
    {
        foreach ($data[0] as $col => $val) {
            foreach ($moduleDataColumns as $module) {
                if ($column >= $module['start'] && $column <= $module['end']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns the data for the given respondent
     *
     * @param string $respondent
     * @param array $data
     * @return array
     */
    protected function _getRespondentRow($respondent, $data)
    {
        foreach ($data[0] as $i => $row) {
            if ($i === 0) continue;
            if ($row[$this->_getRespondentColumn($data)] === $respondent) {
                return $row;
            }
        }
        return array();
    }
}