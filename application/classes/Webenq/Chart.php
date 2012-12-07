<?php
/**
 * Class for building Google charts
 *
 * @author Bart Huttinga
 */
abstract class Webenq_Chart
{
    /** @var array Associative array of chart data (key will be used for the legend) */
    protected $_rawData = array();

    /** @var string String containing the encoded chart data */
    protected $_encData = array();

    /** @var string Cache directory */
    protected $_cacheDir = "./cache/";

    /** @var string Chart size */
    protected $_size = "600x300";

    /** @var string Chart scale */
    protected $_scale = "0,100";

    /** @var string Chart axis position */
    protected $_axisPosition = "y";

    /** @var string Chart axis range */
    protected $_axisRange;

    /** @var string Chart colors */
    protected $_colors;

    /** @var string Chart title */
    protected $_title = "";

    /** @var string Chart title color */
    protected $_titleColor = "000000";

    /** @var string Chart title font size */
    protected $_titleFontSize = "15";

    /** @var string Chart legend */
    protected $_legend;

    /** @var string Chart labels */
    protected $_labels;

    /** @var array Valid chart types */
    protected $_validTypes = array(
        "Line"                    => "lc",
        "SparkLines"            => "ls",
        "XYLine"                => "lxy",
        "HorizontalBar"            => "bhs",
        "VerticalBar"            => "bvs",
        "GroupedHorizontalBar"    => "bhg",
        "GroupedVerticalBar"    => "bvg",
        "Pie"                    => "p",
        "3dPie"                    => "p3",
        "ConcentricPie"            => "pc"
        // Nog verder uitbreiden met andere types
   );


    public function __construct()
    {
        //
    }


    public function setCacheDir($dir)
    {
        $this->_cacheDir = $dir;
        return $this;
    }


    /**
     * Sets the data
     *
     * @param array $data Chart data as nested array
     * @return object $this
     */
    public function setData($data)
    {
        if (!$this->_isValidData($data)) {
            throw new Exception("Data should be a nested array");
        }

        $this->_rawData = $data;
        $this->_encData = $this->_getEncodedData($data);
        $this->_scale = $this->_getScale($data);
        return $this;
    }


    /**
     * Set the axis position
     *
     * @param string Position ("left", "right", "top" or "bottom")
     * @return object $this
     */
    public function setAxisPositon($position)
    {
        switch (strtolower($position)) {
            case "left":
                $this->_axisPosition = "y";
                break;
            case "right":
                $this->_axisPosition = "r";
                break;
            case "top":
                $this->_axisPosition = "t";
                break;
            case "bottom":
                $this->_axisPosition = "x";
                break;
            default:
                throw new Exception("Only left, right, top and bottom are valid options");
                break;
        }
        return $this;
    }


    /**
     * Set the axis range
     *
     * @param array Array with range of axis
     * @return object $this
     */
    public function setAxisRange($start, $first, $last, $interval)
    {
        $this->_axisRange = "$start,$first,$last,$interval";
        return $this;
    }


    /**
     * Sets the size of the image
     *
     * @param array $size Array with the keys "width" and "height"
     * @return object $this
     */
    public function setSize($size)
    {
        if (!isset($size["width"]) || !isset($size["height"])) {
            throw new Exception("Size should be given in associative array width keys 'width' and 'height'");
        }

        if (!$this->_isValidSize($size)) {
            throw new Exception("Chart is too big; it may not exeed 300,000 pixels");
        }

        $this->_size = $size["width"] . "x" . $size["height"];
        return $this;
    }


    /**
     * Sets the chart title
     *
     * @param string Chart title
     * @return object $this
     */
    public function setTitle($title=null)
    {
        $this->_title = str_replace(" ", "+", $title);
        return $this;
    }


    /**
     * Set title color and font size
     *
     * @param array Array with keys ["color"] and ["fontSize"]
     * @return object $this
     */
    public function setTitleFont($params)
    {
        if (isset($params[ "color" ])) {
            $this->_titleColor = $params[ "color" ];
        }

        if (isset($params[ "fontSize" ])) {
            $this->_titleFontSize = $params[ "fontSize" ];
        }

        return $this;
    }


    public function setLabels($labels)
    {
        if (!is_array($labels)) {
            throw new Exception("Nested array expected");
        }

        $this->_labels = null;
        foreach ($labels as $label) {
            $this->_labels .= $label . "|";
        }
        $this->_labels = substr($this->_labels, 0, -1);

        return $this;
    }


    public function setLegend($legend=null)
    {
        $this->_legend = null;

        if (null === $legend) {
            return $this;
        }

        if (!is_array($legend)) {
            throw new Exception("Array expected");
        }

        foreach ($legend as $value) {
            $this->_legend .= $value . "|";
        }
        $this->_legend = substr($this->_legend, 0, -1);

        return $this;
    }


    public function setColors($colors)
    {
        $this->_colors = null;
        if (is_array($colors)) {
            foreach ($colors as $color) {
                $this->_colors .= $color . ",";
            }
            $this->_colors = substr($this->_colors, 0, -1);
        }
        return $this;
    }


    /**
     * Sets the default values based on the supplied data
     *
     * @return unknown_type
     */
    public function _setDefaults()
    {
        $minVal = $this->_getMinValue();
        $maxVal = $this->_getMaxValue();

        if (!isset($this->_axisRange)) {
            $this->setAxisRange(0, $minVal, $maxVal, round(($maxVal/10)));
        }

        if (!isset($this->_legend)) {
            $legend = array();
            foreach ($this->_rawData as $dataset) {
                foreach ($dataset as $key=>$value) {
                    $legend[] = $key;
                }
            }
            $this->setLegend($legend);
        }

        if (!isset($this->_labels)) {
            $labels = array();
            foreach ($this->_rawData as $i=>$dataset) {
                foreach ($dataset as $value) {
                    $labels[] = $value;
                }
            }
            $this->setLabels($labels);
        }

        if (!isset($this->_colors)) {
            $this->_colors = "ffcc00|ff9933|ff6666|ff3399|ff00cc|cccc00";
        }

        if ($this instanceof Classes_Chart_Line && isset($this->_legend)) {
            $this->_swapLabelsLegend();
        }
    }


    /**
     * Builds the html code, checks for cached version and return html code
     *
     * @return string Html code
     */
    public function build()
    {
        if (!isset($this->_encData)) {
            throw new Exception("Cannot build chart without data");
        }

        $this->_setDefaults();

        $url = $this->_buildUrl();

        $cache = Zend_Cache::factory(
            "Output",
            "File",
            array(
                "lifetime" => null
           ),
            array(
                "cache_dir" => $this->_cacheDir
            )
        );

        if (!$chart = $cache->load(md5($url))) {
            $chart = file_get_contents($url);
            $cache->save($chart, md5($url));
        }

        return $chart;
    }


    /**
     * Gets the URL
     *
     * @return string Url to image
     */
    public function getUrl()
    {
        return $this->_buildUrl();
    }


    /**
     * Saves the image to the given destination
     *
     * @param string $destination Path and filename
     */
    public function save($destination)
    {
        $fp = fopen($destination, 'w');
        fwrite($fp, $this->build());
    }


    /**
     * Builds the URL based on the provided data
     *
     *
     */
    protected function _buildUrl()
    {
        $url  = "http://chart.apis.google.com/chart";

        if (isset($this->_type)) $url .= "?cht=" . $this->_type;
        if (isset($this->_size)) $url .= "&chs=" . $this->_size;
        if (isset($this->_encData)) $url .= "&chd=" . $this->_encData;
        if (isset($this->_scale)) $url .= "&chds=" . $this->_scale;
        if (isset($this->_title)) $url .= "&chtt=" . $this->_title;
        if (isset($this->_titleColor)) $url .= "&chts=" . $this->_titleColor . "," . $this->_titleFontSize;
        if (isset($this->_labels)) $url .= "&chl=" . $this->_labels;
        if (isset($this->_legend)) $url .= "&chdl=" . $this->_legend;
        if (isset($this->_colors)) $url .= "&chco=" . $this->_colors;
        if (isset($this->_axisPosition)) $url .= "&chxt=" . $this->_axisPosition;
        if (isset($this->_axisRange)) $url .= "&chxr=" . $this->_axisRange;

        $url = str_replace(" ", "+", $url);

        return $url;
    }


    /**
     * Outputs the html code
     *
     * @return string Html code
     */
    public function __toString()
    {
//        header("Content-type: image/png");
        return $this->build();
    }


    /**
     * Checks if the given type is a valid type
     *
     * @param string $type Type of chart
     * @return boolean True is type is ok, false if not
     */
    protected function _isValidType($type)
    {
        if (key_exists($type, $this->_validTypes)) {
            return true;
        } else {
            throw new Exception("Not a valid chart type");
        }
    }


    /**
     * Checks if the size does not exeed the maximum number of pixels (300,000)
     *
     * @param array $size Size of the chart as associative array (array("width"=>100,"height"=>200));
     * @return boolean True is size is ok, false if not
     */
    protected function _isValidSize($size)
    {
        $pixels = $size["width"] * $size["height"];
        if ($pixels <= 300000) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if the data are valid
     *
     * @param array $data Array of datasets (each dataset is an array)
     * @return boolean Return true if ok, throws exception otherwise
     */
    protected function _isValidData($data)
    {
        if (!is_array($data)) {
            throw new Exception("Data must be an array");
        }

        foreach ($data as $dataset) {
            if (!is_array($dataset)) {
                throw new Exception("Each dataset must be an array");
            }
        }

        return true;
    }


    /**
     * Gets the encoded data-string (using text encoding)
     *
     * @param array $data Array of datasets (each dataset is an array)
     * @return string
     */
    protected function _getEncodedData($data)
    {
        $retVal = "t:";
        foreach ($data as $dataset) {
            foreach ($dataset as $value) {
                $retVal .= $value . ",";
            }
            $retVal = substr($retVal, 0, -1);
            $retVal .= "|";
        }

        // Delete ",|" at the end of string and return the string
        $retVal = substr($retVal, 0, -1);

        return $retVal;
    }


    /**
     * Gets the range from an array of datasets
     *
     * @param array $data Array of datasets (each dataset is an array)
     * @return string "$min,$max"
     */
    protected function _getScale($data)
    {
        $max = $this->_getMaxValue($data);
        $min = $this->_getMinValue($data);
        return "$min,$max";
    }


    /**
     * Gets the minimum value from the provided data
     *
     * @return integer Minimum value in data array
     */
    protected function _getMinValue()
    {
        $minVal = 0;
        foreach ($this->_rawData as $dataset) {
            foreach ($dataset as $value) {
                if ($value < $minVal) $minVal = $value;
            }
        }
        return $minVal;
    }


    /**
     * Gets the maximum value from the provided data
     *
     * @return integer Maximum value in data array
     */
    protected function _getMaxValue()
    {
        $maxVal = 0;
        foreach ($this->_rawData as $dataset) {
            foreach ($dataset as $value) {
                if ($value > $maxVal) $maxVal = $value;
            }
        }
        return $maxVal;
    }


    /**
     * Gets the legend from the associative array of datasets
     *
     * @param array $data Array of datasets (each dataset is an array)
     * @return string
     */
    protected function _getLegend($data)
    {
        $retVal = null;

        foreach ($data as $label => $dataset) {
            $retVal .= $label . ",";
        }
        return substr($retVal, 0, -1);
    }
}