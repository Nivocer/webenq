<?php
/**
 * Class for building Google charts, type Pie
 *
 * @author Bart Huttinga
 */
abstract class Webenq_Chart_Line extends Webenq_Chart
{
    public static function factory($type=null)
    {
        $chartTypes = array(
            "Normal",
            "Spark",
            "XY");

        if (isset($type)) {
            if (in_array($type, $chartTypes)) {
                $class = "Webenq_Chart_Line_" . $type;
                return new $class();
            } else {
                throw new Exception("Wrong type given");
            }
        } else {
            return new Webenq_Chart_Line_Normal();
        }
    }


    /**
     * Swaps values of labels and legend
     */
    public function _swapLabelsLegend()
    {
        $oldLegend = $this->_legend;
        $oldLabels = $this->_labels;

        $this->_legend = $oldLabels;
        $this->_labels = $oldLegend;

        $this->setColors(array("ffcc00"));
    }
}