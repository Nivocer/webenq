<?php
/**
 * Class definition for the open question data type date
 */
class Webenq_Model_Question_Open_Date extends Webenq_Model_Question_Open
{
    /**
     * Child classes
     *
     * @var array $children
     */
    public $children = array();


    /**
     * Question types (in testing order)
     */
    static protected $_questionTypes = array();

    /**
     * Valid formats for a date
     */
    static protected $_validFormats = array(
        'dd-MM-yyyy',
        'dd.MM.yyyy',
        'dd.MM.yy',
        'dd-MM-yy',
        'dd.MM.yy hh:mm',
        'dd.MM.yy hh:mm:ss',
        'yyyy-MM-dd hh:mm:ss',
        'yyyy-MM-dd hh:mm',
    );


    /**
     * Valid formats for a date
     */
    static protected $_validPatterns = array(
        /* xxxx-xx-xx */
        '#^(\d{4}).(\d{2}).(\d{2})$#',
        /* xx-xx-xx */
        '#^(\d{2}).(\d{2}).(\d{2})$#',
        /* xx-xx-xxxx */
        '#^(\d{2}).(\d{2}).(\d{4})$#',
        /* xxxx-xx-xx xx-xx-xx */
        '#^(\d{4}).(\d{2}).(\d{2}) (\d{2}).(\d{2}).(\d{2})$#',
        /* xxxx-xx-xx xx-xx */
        '#^(\d{4}).(\d{2}).(\d{2}) (\d{2}).(\d{2})$#',
        /* xx-xx-xx xx-xx-xx */
        '#^(\d{2}).(\d{2}).(\d{2}) (\d{2}).(\d{2}).(\d{2})$#',
        /* xx-xx-xx xx-xx */
        '#^(\d{2}).(\d{2}).(\d{2}) (\d{2}).(\d{2})$#',
        /* xx-xx-xxxx xx-xx-xx */
        '#^(\d{2}).(\d{2}).(\d{4}) (\d{2}).(\d{2}).(\d{2})$#',
        /* xx-xx-xxxx xx-xx */
        '#^(\d{2}).(\d{2}).(\d{4}) (\d{2}).(\d{2})$#',
    );


    /**
     * Checks if the given result set validates for this type
     *
     * @param Webenq_Model_Question $question Question containing the answervalues to test against
     * @param string $language
     * @return bool True if is this type, false otherwise
     */
    static public function isType(Webenq_Model_Question $question, $language)
    {
        $values = $question->getAnswerValues();

        /* do a quick scan to save time */
        if ($question->maxLen() > 19) {
            return false;
        }

        /* iterate over values */
        foreach (array_unique($values) as $value) {

            $value = trim($value);

            /* ignore empty value */
            if (!$value || $value === '#N/B') {
                continue;
            }

            /* iterate over valid patterns */
            if (self::toTimestamp($value)) {
                return true;
            } else {
                return false;
            }
        }

//        /* build array of validators with different formats */
//        $validators = array();
//        foreach (self::$_validFormats as $format) {
//            $validators[$format] = new Zend_Validate_Date($format);
//        }
//
//        /* loop through unique values */
//        foreach (array_unique($values) as $value) {
//
//            /* ignore empty value */
//            if (!$value || $value === '#N/B') {
//                continue;
//            }
//
//            /* test value against all validators */
//            $date = null;
//            foreach ($validators as $format => $validator) {
//                if ($validator->isValid($value)) {
//                    $date = new Zend_Date($value, $format);
//                    break;
//                }
//            }
//
//            if (!$date instanceof Zend_Date) {
//                return false;
//            }
//        }

        return true;
    }

    static public function toTimestamp($date)
    {
        foreach (self::$_validPatterns as $pattern) {

            if (preg_match($pattern, $date, $parts) && count($parts) > 3) {

                if ($parts[1] >= 1970 && $parts[2] <= 12 && $parts[3] <= 31) {
                    $year = $parts[1];
                    $month = $parts[2];
                    $day = $parts[3];
                    $hour = isset($parts[4]) ? $parts[4] : '00';
                    $minute = isset($parts[5]) ? $parts[5] : '00';
                    $second = isset($parts[6]) ? $parts[6] : '00';
                    break;
                }

                if ($parts[1] >= 1970 && $parts[2] <= 31 && $parts[3] <= 12) {
                    $year = $parts[1];
                    $day = $parts[2];
                    $month = $parts[3];
                    $hour = isset($parts[4]) ? $parts[4] : '00';
                    $minute = isset($parts[5]) ? $parts[5] : '00';
                    $second = isset($parts[6]) ? $parts[6] : '00';
                    break;
                }

                if ($parts[1] <= 31 && $parts[2] <= 12 && $parts[3] >= 1970) {
                    $day = $parts[1];
                    $month = $parts[2];
                    $year = $parts[3];
                    $hour = isset($parts[4]) ? $parts[4] : '00';
                    $minute = isset($parts[5]) ? $parts[5] : '00';
                    $second = isset($parts[6]) ? $parts[6] : '00';
                    break;
                }

                if ($parts[1] <= 12 && $parts[2] <= 31 && $parts[3] >= 1970) {
                    $month = $parts[1];
                    $day = $parts[2];
                    $year = $parts[3];
                    $hour = isset($parts[4]) ? $parts[4] : '00';
                    $minute = isset($parts[5]) ? $parts[5] : '00';
                    $second = isset($parts[6]) ? $parts[6] : '00';
                    break;
                }

                if ($parts[2] <= 12 && $parts[3] <= 31) {
                    $year = ($parts[1] >= 70) ? '19' . $parts[1] : '20' . $parts[1];
                    $month = $parts[2];
                    $day = $parts[3];
                    $hour = isset($parts[4]) ? $parts[4] : '00';
                    $minute = isset($parts[5]) ? $parts[5] : '00';
                    $second = isset($parts[6]) ? $parts[6] : '00';
                    break;
                }
            }
        }

        if (count($parts) > 0) {
            return strtotime("$year-$month-$day $hour:$minute:$second");
        }

        return false;
    }


    static public function toFormat($date, $format)
    {
        $timestamp = self::toTimestamp($date);

        if ($timestamp) {
            return date($format, $timestamp);
        }

        return false;
    }


    public static function getValidFormats()
    {
        return self::$_validFormats;
    }
}