<?php
/**
 * Tool class for converting HVA data
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Tool_Hva extends Webenq_Tool
{
    protected $_filename;

    protected $_adapter;

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var array
     */
    protected $_modules = array();

    /**
     * The column number in which the respondent are stored
     *
     * @var int
     */
    protected $_respondentColumn;


    /**
     * @var array
     */
    protected $_respondents = array();

    /**
     * The attended modules per respondent
     *
     * @var array
     */
    protected $_respondentsModules = array();

    /**
     * The columns containing the module-related data
     *
     * @var array
     */
    protected $_moduleDataColumns = array();

    /**
     * The converted data
     *
     * @var array
     */
    protected $_newData = array();

    protected $_firstRespondentId = 1;

    public function __construct($filename)
    {
        $this->_filename = preg_replace('#(.*/)*#', null, $filename);
        $this->_adapter = Webenq_Import_Adapter_Abstract::factory($filename);
    }

    public function process()
    {
        $this->_data = $this->getData();
        $this->_modules = $this->_getModules();
        $this->_respondentColumn = $this->_getRespondentColumn();
        $this->_respondents = $this->_getRespondents();
        $this->_respondentsModules = $this->_getRespondentsModules();
        $this->_moduleDataColumns = $this->_getModuleDataColumns();
        $this->_newData = $this->_getNewData();
    }

    public function getNewData()
    {
        return $this->_getNewData();
    }

    /**
     * Returns a downloadable object
     *
     * @return Webenq_Download
     */
    public function getDownload()
    {
        // get first working sheet of new data
        $data = $this->_newData[0];

        $download = new Webenq_Download_Xls();
        $download->setData($data)->init();
        return $download;
    }

    public function getAdapter()
    {
        return $this->_adapter;
    }

    public function getData()
    {
        if (empty($this->_data)) {
            $this->_data = $this->_adapter->getData();
        }
        return $this->_data;
    }

    /**
     * Returns the modules found in the data
     *
     * @return array Modules
     */
    protected function _getModules()
    {
        $modules = array();
        $moduleNamePattern = '/^1:\s(.*)/';
        foreach ($this->_data[0][0] as $column => $header) {
            if (preg_match($moduleNamePattern, $header, $matches)) {
                $modules[$column] = $matches[1];
            }
        }
        return $modules;
    }

    /**
     * Returns the respondents found in the data
     *
     * @return array Respondents
     */
    protected function _getRespondents()
    {
        $respondents = array();
        foreach ($this->_data[0] as $i => $row) {
            if ($i == 0) continue;
            $respondents[] = $row[$this->_respondentColumn];
        }
        return $respondents;
    }

    /**
     * Returns the column index that contains the respondents
     *
     * @return int
     */
    protected function _getRespondentColumn()
    {
        $respondentHeaderPattern = '/^Respondent$/';
        foreach ($this->_data[0][0] as $respondentColumn => $header) {
            if (preg_match($respondentHeaderPattern, $header)) {
                return $respondentColumn;
            }
        }
        return false;
    }

    /**
     * Returns the modules attended by respondents found in the data
     *
     * @return array RespondentsModules
     */
    protected function _getRespondentsModules()
    {
        $respondentsModules = array();
        foreach ($this->_data[0] as $i => $row) {
            if ($i === 0) continue;
            foreach ($this->_modules as $col => $val) {
                if ((bool) $row[$col]) {
                    $respondent = $row[$this->_respondentColumn];
                    $respondentsModules[$respondent][] = $val;
                }
            }
        }
        return $respondentsModules;
    }

    /**
     * Returns the new data
     *
     * @return array
     */
    protected function _getNewData()
    {
        // get processed data
        $data = $this->_data;
        $respondents = $this->_respondents;
        $modules = $this->_modules;
        $respondentsModules = $this->_respondentsModules;
        $moduleDataColumns = $this->_moduleDataColumns;

        // get extra data from last working sheet
        $extraData = array();
        foreach ($data[2] as $row) {
            if (isset($row[0])) {
                $extraData[$row[0]] = $row[1];
            }
        }

        // copy headers from original data
        $new = array();
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

                // add some extra data
                $newRow[] = $module;
                $newRow[] = $this->_firstRespondentId + array_search($respondent, $this->_respondents);
                foreach ($extraData as $key => $value) $newRow[] = $value;
                $newRow[] = $this->_filename;
                $new[0][] = $newRow;
            }
        }

        // merge modules (if they have the same questions)
        $this->_mergeModules($new, $moduleDataColumns);

        // add headers for extra data
        $new[0][0][] = '1: Module';
        $new[0][0][] = '1: Respondent ID';
        foreach ($extraData as $key => $value) $new[0][0][] = "1: $key";
        $new[0][0][] = '1: Filename';

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
     * @return array
     */
    protected function _getModuleDataColumns()
    {
        $groups = array();
        foreach ($this->_data[1] as $row) {
            $definition = $row[0];
            foreach ($this->_modules as $name) {
                $pattern = "/^(\d*):.*($name)/";
                if (preg_match($pattern, $definition, $matches)) {
                    $groups[$matches[1]] = $matches[2];
                }
            }
        }

        $columns = array();
        foreach ($this->_data[0][0] as $column => $header) {
            foreach ($groups as $key => $name) {
                // search by group number
                if (preg_match("/^$key:.*$/", $header, $matches)) {
                    if (!isset($columns[$name]))
                    $columns[$name] = array('start' => $column);
                    $columns[$name]['end'] = $column;
                }
                // search by group number
                elseif (preg_match("/\d*:\s$name:\s.+/", $header, $matches)) {
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

    public function countRespondents()
    {
        return count($this->_respondents);
    }

    public function setFirstRespondentId($id)
    {
        $this->_firstRespondentId = (int) $id;
    }
}