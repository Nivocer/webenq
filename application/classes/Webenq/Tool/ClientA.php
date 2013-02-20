<?php
/**
 * Tool class for converting ClientA data
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Tool_ClientA extends Webenq_Tool
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
     * the multiple response column at the beginning of the data are used to determin the modules, they start with '1: '
     *
     *
     * @return array Modules
     */
    protected function _getModules()
    {
        $modules = array();
        //first questback group (1: ), than module code
        $moduleNamePattern = '/^1:\s(.*)/';
        //alternative:
        //1: aan het begin, vervolgens 4x: alles mag behalve _, afgesloten door _
        //vervolgens nog een groep, waarbij alles mag behalve _
        //$moduleNamePattern ='/^1:\s([^_]+_[^_]+_[^_]+_[^_]+_[^_]+)/';
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
     * TODO function is processed twice, is that correct?
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
        if (isset($data[2])) {
            foreach ($data[2] as $row) {
                if (isset($row[0])) {
                    $extraData[$row[0]] = $row[1];
                }
            }
        } else {
            //@todo throw error
            var_dump($this->_filename);
            exit;
        }
        // copy headers from original data (all columns)
        $new = array();
        $new[0][0] = $data[0][0];

        //duplicate respondent rows for each module and keep per row only data for one module
        //(and the non-module variables), other module columns are set to null
        //TODO check what happens when duplicate respondent (now respondent is emailadress (first column)
        foreach ($respondentsModules as $respondent => $respondentModules) {
            foreach ($respondentModules as $key => $module) {
                $newRow = array();
                //if we don't have a module, we also don't have start/end column of that module
                if (isset($moduleDataColumns[$module]['start'])) {
                    $startColumn = $moduleDataColumns[$module]['start'];
                } else {
                    $startColumn=0;
                }
                if (isset($moduleDataColumns[$module]['end'])) {
                    $endColumn = $moduleDataColumns[$module]['end'];
                } else {
                    $endColumn=999999999;
                }

                $row = $this->_getRespondentRow($respondent, $data);
                foreach ($row as $column => $value) {
                    //is the current column a module column and:
                    // is it part of the current module: keep value,
                    //if not part of current module, set to null,
                    //if not part of a module at all: keep value
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

                // add some extra data (titel questionnaire, startdate, end date response)
                //module column=last columnin orginal data
                $moduleColumn=count($data[0][0]);

                $newRow[$moduleColumn] = $module;
                $newRow[] = $this->_firstRespondentId + array_search($respondent, $this->_respondents);
                foreach ($extraData as $key => $value) $newRow[] = $value;
                $newRow[] = $this->_filename;
                $new[0][] = $newRow;
            }
        }
        // merge modules (if they have the same questions)
        $this->_mergeModules($new, $moduleDataColumns);

        // add headers for extra data
        $new[0][0][$moduleColumn] = '9999: Module';
        $new[0][0][] = '9999: Respondent ID';
        foreach ($extraData as $key => $value) $new[0][0][] = "9999: $key";
        $new[0][0][] = '9999: Filename';

        // remove all module atendency columns and reset keys for all rows
        foreach ($new[0] as $row => $void) {
            foreach ($modules as $column => $module) {
                unset($new[0][$row][$column]);
            }
            $new[0][$row] = array_merge($new[0][$row], array());
        }

        // add group numbers to questions
        foreach ($new[0][0] as $key => $header) {
            if (!preg_match('/^\d+:\s/', $header)) {
                $new[0][0][$key] = (1 + $key) . ': ' . $header;
            }
        }

        // build the second working sheet, containing group information
        $groups = array();
        foreach ($new[0][0] as $header) {
            if (preg_match('/^(\d+):\s(.+)$/', $header, $matches)) {
                if ($matches[1] != 9999 && !key_exists($matches[1], $groups)) {
                    $groups[$matches[1]] = $matches[2];
                }
            }
        }
        foreach ($groups as $key => $group) {
            $row = $key - 1;
            $new[1][$row][0] = "$key:  = $group";
        }
        $new[1][$row++][0] = '9999:  = Meta';

        // simply copy third working sheet
        $new[2] = $this->_data[2];

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
        //remove modulename from questiontext if it exist.
        foreach ($moduleDataColumns as $module => &$columns) {
            foreach ($questions as $column => &$question) {
                //search al questions which has the current module(Name)
                $moduleQuote=preg_quote($module, '/');
                $pattern="/^(\d*:\s*)$moduleQuote:\s*(.*)$/";
                if (preg_match($pattern, $question, $matches)) {
                    $question = $matches[1] . $matches[2];
                    //todo document next if statement
                    if ($column === 1 + $columns['end']) $columns['end']++;
                }
            }
        }

        // find double questions
        $uniqueModuleQuestions = array();
        //if we use $question in stead of $question2 in the next line,
        //the last question per file is wrong (variable by reference side effect?) #6059
        foreach ($questions as $column => $question2) {
            if ($this->_isModuleColumn($column, $moduleDataColumns, $data)) {
                if (preg_match('/d*:\s*(.*)$/', $question2, $matches)) {
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
                        if (!$row[$first] && isset($row[$column]) &&
                                $row[$column]) {
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
                $nameQuote=preg_quote($name, '/');
                $pattern = "/^(\d*):.*($nameQuote)/i";
                if (preg_match($pattern, $definition, $matches)) {
                    $groups[$matches[1]] = $matches[2];
                }
            }
        }

        $columns = array();
        foreach ($this->_data[0][0] as $column => $header) {
            foreach ($groups as $key => $name) {
                // search by group number
                $keyQuote=preg_quote($key, '/');
                $nameQuote=preg_quote($name, '/');
                if (preg_match("/^$keyQuote:.*$/", $header, $matches)) {
                    if (!isset($columns[$name]))
                        $columns[$name] = array('start' => $column);
                    $columns[$name]['end'] = $column;
                } elseif (preg_match("/\d*:\s$nameQuote:\s.+/", $header, $matches)) {
                    // search by group number
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