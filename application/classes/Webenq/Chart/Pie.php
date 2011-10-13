<?php
/**
 * Class for building Google charts, type Pie
 *
 * @author Bart Huttinga
 */
abstract class Webenq_Chart_Pie extends Webenq_Chart
{
    public static function factory($type=null)
    {
        $chartTypes = array(
            "TwoDimensional",
            "ThreeDimensional",
            "Concentric");

        if (isset($type)) {
            $class = "Webenq_Chart_Pie_" . $type;
            if (class_exists($class)) {
                return new $class();
            } else {
                throw new Exception("Wrong type given");
            }
        } else {
            return new Webenq_Chart_Pie_ThreeDimensional();
        }
    }


}