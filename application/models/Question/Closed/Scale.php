<?php
/**
 * Class definition for the closed question data type scale
 */
class Webenq_Model_Question_Closed_Scale extends Webenq_Model_Question_Closed
{
    /**
     * Child classes
     *
     * @var array $children
     */
    public $children = array('Five', 'Six', 'Seven', 'Four', 'Three', 'Two');

    /**
     * Values indicating this might be a scale type
     */
    static protected $_groups = array();

    /**
     * Returns the defined scale values
     *
     * @return array Scale values
     */
    static public function getScaleValues()
    {
        if (!self::$_groups instanceof Doctrine_Collection) {
            self::$_groups = Doctrine_Query::create()
                ->from('Webenq_Model_AnswerPossibilityGroup')
                ->execute();
        }
        return self::$_groups;
    }

    /**
     * Checks if the given result set validates for this type
     *
     * @param Webenq_Model_Question $question Question containing the answervalues to test against
     * @param string $language
     * @return bool True if is this type, false otherwise
     */
    static public function isType(Webenq_Model_Question $question, $language)
    {
        /* are all values present in an answer-possibility-group? */
        $group = Webenq_Model_AnswerPossibilityGroup::findByUniqueValues($question->getUniqueValuesExcludingNullValues(), $language);
        if (!$group) {
            return false;
        }

        if ($group->measurement_level !== 'metric') {
            return false;
        }

        /* are values numeric only? */
//        if ($question->isNumeric()) {
//            return false;
//        }

        /* no repeating values? */
//        if ($question->count() === $question->countUnique()) {
//            return false;
//        }

        return true;
    }


    /**
     * Checks if no invalid answers are given
     *
     * @return Webenq_Model_AnswerPossibilityGroup
     */
//    public function getValidAnswerPossibilityGroup()
//    {
//        $query = Doctrine_Query::create()
//            ->from('Webenq_Model_AnswerPossibilityText t')
//            ->leftJoin('t.AnswerPossibilityTextSynonym s');
//
//        $uniqueValues = $this->getUniqueValues();
//        if ($key = array_search('', $uniqueValues)) {
//            unset($uniqueValues[$key]);
//        }
//        foreach ($uniqueValues as $value) {
//            $query->orWhere('t.text = ?', $value);
//            $query->orWhere('s.text = ?', $value);
//        }
//
//        $groupIds = array();
//        foreach ($query->execute() as $possibility) {
//            if ($possibility->text && $possibility->AnswerPossibilityTextSynonym->count() > 0) {
//            }
//            $groupIds[] = $possibility->AnswerPossibility->AnswerPossibilityGroup->id;
//        }
//
//        /* check if there's a group that has as many counts as unique values */
//        $countUnique = count($uniqueValues);
//        $validGroupIds = array();
//        foreach (array_count_values($groupIds) as $id => $count) {
//            if ($countUnique >= $count) {
//                $validGroupIds[] = $id;
//            }
//        }
//
//        if (count($validGroupIds) > 0) {
//            $group = Doctrine_Query::create()
//                ->from('Webenq_Model_AnswerPossibilityGroup apg')
//                ->innerJoin('apg.AnswerPossibility ap')
//                ->whereIn('apg.id', $validGroupIds)
//                ->orderBy('ap.value DESC')
//                ->execute()
//                ->getFirst();
//            return $group;
//        }
//    }

    public function otherValuesThanDefinedValid()
    {
//        $values = $this->_data;
//        $valids = $scaleValues[get_class($this)];
//        foreach ($values as $value) {
//            if ($value) {
//                if (!key_exists($value, $valids)) {
//                    return $value;
//                }
//            }
//        }
        return false;
    }


    /**
     * Generates a barchart
     *
     * @param string $file File
     * @param integer $width Width
     * @param integer $height Height
     * @return void
     */
    public function generateBarchart($file=null, $width=500, $height=30)
    {
        /* init vars */
        $border = array(0 => 0);
        $threshold = 5;
        $font = 'arial.ttf';
        $fontSize = 14;
        $margeLeft = 4;
        $margeTop = ceil(($height - $fontSize) / 2);
        $invalidText = 'Geen geldige score';

        /* get percentages */
        $percentages = $this->getNegativeNeutralPositivePercentages();

        /* init image */
        putenv('GDFONTPATH=' . '/usr/share/fonts/truetype/msttcorefonts/');
        $im = @imagecreatetruecolor($width, $height) or die('Cannot Initialize new GD image stream');

        /* set colors */
        $black = imagecolorallocate($im, 0, 0, 0);
        $white = imagecolorallocate($im, 255, 255, 255);
        $colors = array(
            imagecolorallocate($im, 200, 0, 0),
            imagecolorallocate($im, 255, 255, 0),
            imagecolorallocate($im, 0, 255, 0),
        );

        $subTotal = 0;
        $total = array_sum($percentages);

        if ($total >= 98 && $total <= 102) {

            foreach ($percentages as $i => $percentage) {

                $subTotal += $percentage;
                $border[$i+1] = round($subTotal * $width / $total);

                if (isset($border[$i]) && isset($colors[$i])) {
                    imagefilledrectangle($im, $border[$i], 0, $border[$i+1], $height, $colors[$i]);
                    if ($percentage > $threshold) {
                        $fontColor = ($i === 0) ? $white : $black;
                        imagettftext($im, $fontSize, 0, $margeLeft + $border[$i], $margeTop + $fontSize, $fontColor,
                            $font, round($percentage)."%");
                    }
                }
            }

        } else {
            imagefilledrectangle($im, 0, 0, 0, $height, $black);
            $invalidText .= ' (totaal = ' . $total . ')';
            imagettftext($im, $fontSize, 0, $margeLeft, $margeTop + $fontSize, $white, $font, $invalidText);
        }

        if ($file) {
            $file = preg_replace('#^'.realpath('.').'/#', '', $file);
            imagepng($im, $file);
        } else {
            header('Content-type: image/png');
            imagepng($im);
            die;
        }
        imagedestroy($im);
    }


    /**
     * Get an array with values/percentages pairs
     *
     * @return array Array with unique values as keys and percentages as values
     */
    public function getPercentages()
    {
        /* get values and remove invalid values and null values */
        $values = $this->getAnswerValues();
        foreach ($values as $key => $value) {
            if ($value <= 0) {
                unset($values[$key]);
            }
        }

        /* calculate percentages */
        $total = count($values);
        $countValues = array_count_values($values);
        $percentages = array();

        foreach ($countValues as $value => $count) {
            $percentages[$value] = $count / $total * 100;
        }

        ksort($percentages);
        return $percentages;
    }


    /**
     * Convert percentages into negative/neutral/positive percentages
     *
     * @return array Percentages
     */
    public function getNegativeNeutralPositivePercentages()
    {
        $scale = (int) substr(str_replace(array('Two', 'Three', 'Four', 'Five', 'Six', 'Seven'),
            array(2, 3, 4, 5, 6, 7),
            get_class($this)), -1);

        if ($scale == 0) {
            throw new Exception('Method ' . __FUNCTION__ . '() is only availbale for classes extending ' . __CLASS__);
        }

        $percentages = $this->getPercentages();
        $countPosAndNeg = (int) floor($scale / 2);
        $middle = $scale - $countPosAndNeg;

        /* get negative */
        $negative = 0;
        for ($i = 1; $i <= $countPosAndNeg; $i++) {
            $negative += @$percentages[$i];
        }

        /* get positive */
        $positive = 0;
        for ($i = $scale; $i > $middle; $i--) {
            $positive += @$percentages[$i];
        }

        /* get neutral */
        $neutral = 0;
        if (in_array($scale, array(3, 5, 7))) {
            $neutral = @$percentages[$middle];
        }

        $retVal = array($negative, $neutral, $positive);
        return $retVal;
    }
}