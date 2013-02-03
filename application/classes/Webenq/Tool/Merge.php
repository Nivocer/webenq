<?php
/**
 * Webenq
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
 * @package    Webenq
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Tool class for converting ClientA data
 *
 * @package     Webenq
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Tool_Merge extends Webenq_Tool
{
    protected $_filename;

    protected $_adapter;

    /**
     * @var array
     */
    protected $_data = array();


    /**
     * The converted data
     *
     * @var array
    */
    protected $_newData = array();

    protected $_firstRespondentId = 0;
    protected $_lastRespondentId = 1;

    public function __construct($filename)
    {
        $this->_filename = preg_replace('#(.*/)*#', null, $filename);
        $this->_adapter = Webenq_Import_Adapter_Abstract::factory($filename);
    }

    public function process()
    {
        $this->_data = $this->getData();
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
     * Returns the new data
     *
     * @return array
     */
    protected function _getNewData()
    {
        // get processed data
        $data = $this->_data;

        //only for questback
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


        foreach ($data[0]  as $t_key=> $row) {
            if ($t_key==0) {
                continue;
            }
            // add some extra data (titel questionnaire, startdate, end date response)
            $row[] = $this->_firstRespondentId + $t_key;
            $this->_lastRespondentId=$t_key;
            //check: only questback?
            foreach ($extraData as $key => $value) {
                $row[] = $value;
            }

            $row[] = $this->_filename;

            $new[0][] = $row;
        }

        // add headers for extra data
        $new[0][0][] = '9999: Respondent ID';
        //check only for questback
        foreach ($extraData as $key => $value) $new[0][0][] = "9999: $key";

        $new[0][0][] = '9999: Filename';

        // add group numbers to questions, only for questback
        foreach ($new[0][0] as $key => $header) {
            if (!preg_match('/^\d+:\s/', $header)) {
                $new[0][0][$key] = (1 + $key) . ': ' . $header;
            }
        }
        //start only questback

        //copy second working sheet and add meta
        $new[1]=$this->_data[1];
        $new[1][][0] = '9999:  = Meta';

        // simply copy third working sheet
        $new[2] = $this->_data[2];
        //end only for questback

        return $new;
    }
    public function setFirstRespondentId($id)
    {
        $this->_firstRespondentId = (int) $id;
    }
    public function countRespondents()
    {
        return $this->_lastRespondentId;
    }
}