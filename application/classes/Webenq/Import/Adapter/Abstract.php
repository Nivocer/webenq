<?php
/**
 * Common parts for import adapters
 *
 * @package		Webenq
 * @author		Bart Huttinga <b.huttinga@nivocer.com>
 */
abstract class Webenq_Import_Adapter_Abstract implements Webenq_Import_Adapter_Interface
{
    /**
     * Supported input formats
     *
     * For every entry in this array there must be an adapter class.
     *
     * @var array
     */
    static public $supportedFormats = array('ods', 'xls', 'xlsx', 'csv');

    /**
     * Factory for an import-adapter
     *
     * @param string $filename Name of the file
    */
    static public function factory($filename)
    {
        $filenameParts = preg_split('#\.#', $filename);
        $extension = array_pop($filenameParts);

        if (!in_array($extension, self::$supportedFormats)) {
            throw new Webenq_Import_Adapter_Exception('Invalid file format');
        }

        $class = 'Webenq_Import_Adapter_' . ucfirst($extension);
        return new $class($filename);
    }

    /**
     * Returns the filename of the uploaded file
     *
     * @return string Filename
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * Returns the value for the given cell
     *
     * The first parameter can be in the form of 'A1'. In that case the
     * second paramter can be an integer indicating the working-sheet to
     * use (defaults to 0). Alternatively the first and second parameter
     * together can represent the cell, in the form 0, 0 (equals to A1).
     * In that case the working-sheet integer is the third parameter.
     *
     * @param mixed See function description
     * @return string The value of the requested cell
     */
    public function getCell()
    {
        $args = func_get_args();

        switch (count($args)) {
            case 1:
                return $this->_getCellFromMatrix($args[0]);
                break;
            case 2:
                if (is_int($args[0])) {
                    return $this->_getCellFromArray($args[0], $args[1]);
                } else {
                    return $this->_getCellFromMatrix($args[0], $args[1]);
                }
                break;
            case 3:
                return $this->_getCellFromArray($args[0], $args[1], $args[2]);
                break;
            default:
                throw new Exception('Invalid parameters given to method ' . get_class($this) . '::getCell()');
        }
    }

    /**
     * Helper function for getCell()
     *
     * @param string $cell
     * @param int $sheet
     * @return string The value of the requested cell
     */
    protected function _getCellFromMatrix($cell, $sheet = 0)
    {
        preg_match('#^([a-z]*)([0-9]*)$#', strtolower($cell), $matches);
        $row = $matches[2] - 1;
        $col = ord($matches[1]) - 97;

        return $this->_getCellFromArray($row, $col, $sheet = 0);
    }

    /**
     * Helper function for getCell()
     *
     * @param string $row
     * @param string $col
     * @param int $sheet
     * @return string The value of the requested cell
     */
    protected function _getCellFromArray($row, $col, $sheet = 0)
    {
        $data = $this->getData();
        return $data[$sheet][$row][$col];
    }

    /**
     * Translates some field labels to Dutch
     *
     * @param string Non-Dutch field label
     * @return string Dutch field label
     */
    protected function _translate($value)
    {
        $from = array(
                'Quest title', 'Start date', 'End date',
                'Unique respondents', 'E-mail invitations', 'E-mail responses',
                'Total responses', 'Response rate', 'Export date',
        );

        $to = array(
                'Titel vragenlijst', 'Startdatum', 'Einddatum',
                'unieke respondenten', 'E-mail uitnodigingen', 'E-mail antwoorden',
                'Totaal respons', 'Respons percentage', 'Export datum',
        );

        return str_replace($from, $to, $value);
    }

    /**
     * Decodes the given string
     *
     * @param encoded string
     * @return decoded string
     */
    public function UTF8Decode($value)
    {
        if (mb_detect_encoding($value) === "UTF-8") {
            if (mb_detect_encoding(utf8_decode($value)) === "UTF-8") {
                $value = utf8_decode($value);
            } else {
                $value = utf8_decode(iconv("UTF-8", "CP1252", $value));
            }
        }
        return $value;
    }
}