<?php

/**
 * Webenq_Model_QuestionnaireElement
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    Webenq_Models
 * @subpackage ##SUBPACKAGE##
 * @author     Nivocer <webenq@nivocer.com>
 * @version    SVN: $Id: Builder.php,v 1.2 2011/07/12 13:39:03 bart Exp $
 */
class Webenq_Model_QuestionnaireElement extends Webenq_Model_Base_QuestionnaireElement
{
    /**
     * Fills array with data in record and fills related objects with
     * translations
     *
     * @param bool $deep
     * @param bool $prefixKey Not used
     * @return array
     * @see Doctrine_Record::fromArray()
     */
    public function toArray($deep = true, $prefixKey = false)
    {
        $result = parent::toArray($deep, $prefixKey);
        $result['Translation'] = $this->Translation->toArray();
        return $result;
    }
    /**
     * Imports data from a php array
     *
     * @param string $array  array of data, see link for documentation
     * @param bool   $deep   whether or not to act on relations
     * @return void
     * @see Doctrine_Record::fromArray()
     */
    public function fromArray(array $array, $deep = true)
    {
        if ($deep) {
            // @todo We should find a way to do this via the I18n behavior, of find out why 'deep=true' doesn't do this
            $this->setTranslationFromArray($array);
        }
        parent::fromArray($array, $deep);
    }

}