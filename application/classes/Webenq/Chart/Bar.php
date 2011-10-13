<?php
/**
 * Class for building Google charts, type Pie
 *
 * @author Bart Huttinga
 */
abstract class Webenq_Model_Chart_Bar extends Webenq_Model_Chart
{
    public static function factory($type=null)
    {
        if (isset($type)) {
            $class = "Webenq_Model_Chart_Bar_" . $type;
            if (class_exists($class)) {
                return new $class();
            } else {
                throw new Exception("Wrong type given");
            }
        } else {
            return new Webenq_Model_Chart_Bar_Vertical();
        }
    }


    /**
     * Gets the minimum value from the data
     *
     * @param array $data Data
     * @return string Minimum value
     */
    protected function _getMinValue()
    {
        $minValue = 0;
        $data = $this->_rawData;

        foreach ($data as $dataSet) {
            foreach ($dataSet as $value) {
                if ($value < $minValue) {
                    $minValue = $value;
                }
            }
        }

        return $minValue;
    }


    /**
     * Gets the maximum value from the data
     *
     * @param array $data Data
     * @return string Minimum value
     */
    protected function _getMaxValue()
    {
        $number = 0;
        $totals = array();
        $data = $this->_rawData;

        // Count the biggest array
        foreach ($data as $dataset) {
            if (count($dataset) > $number) {
                $number = count($dataset);
            }
        }

        // Define array and set totals to 0
        for ($i=0; $i<$number; $i++) {
            $totals[$i] = 0;
        }

        // Calculate totals
        $i = 0;
        foreach ($data as $dataset) {
            foreach ($dataset as $value) {
                $totals[$i] = $totals[$i] + $value;
                $i++;
            }
        }

        // Get maximum value from array with totals
        sort($totals);
        $max = array_pop($totals);

        return $max;
    }
}