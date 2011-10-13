<?php
/**
 * Class for building Concentric Pie (Google Chart)
 *
 * @author Bart Huttinga
 */
class Webenq_Chart_Bar_GroupedVertical extends Webenq_Chart_Bar
{
    /** @var string Chart type */
    protected $_type = "bvg";


    /**
     * Gets the maximum value from the data
     *
     * @param array $data Data
     * @return string Minimum value
     */
    protected function _getMaxValue()
    {
        $data = $this->_rawData;
        $maxValue = 0;

        foreach ($data as $dataSet) {
            foreach ($dataSet as $value) {
                if ($value > $maxValue) {
                    $maxValue = $value;
                }
            }
        }

        return $maxValue;
    }
}