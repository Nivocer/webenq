<?php

/**
 * Webenq_Model_Report
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    Webenq
 * @subpackage Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 * @version    SVN: $Id: Report.php,v 1.1 2011/12/02 09:40:37 bart Exp $
 */
class Webenq_Model_Report extends Webenq_Model_Base_Report
{
    /**
     * Fills record with data in array and fills related objects with
     * translations
     *
     * @param array $array
     * @param bool $deep
     * @see Doctrine_Record::fromArray()
     */
    public function fromArray(array $array, $deep = true)
    {
        parent::fromArray($array, $deep);

        if (isset($array['title'])) {
            foreach ($array['title'] as $language => $title) {
                if ($title) $this->addReportTitle($language, $title);
            }
        }
    }

    /**
     * Sets the report title for a given language
     *
     * @param string $language The language to set the title for
     * @param string $title The report title for the given language
     * @return self
     */
    public function addReportTitle($language, $title)
    {
        if ($this->id) {
            // get translation
            $translation = Doctrine_Core::getTable('Webenq_Model_ReportTitle')
                ->findOneByReportIdAndLanguage($this->id, $language);
            // or create new one
            if (!$translation) {
                $translation = new Webenq_Model_ReportTitle();
                $translation->report_id = $this->id;
                $translation->language = $language;
            }
            // save changes
            $translation->text = $title;
            $translation->save();
        } else {
            // create new and attatch translation
            $translation = new Webenq_Model_ReportTitle();
            $translation->language = $language;
            $translation->text = $title;
            $this->ReportTitle[] = $translation;
        }
        return $this;
    }

    public static function getReports($id = null)
    {
        $query = Doctrine_Query::create()->from('Webenq_Model_Report');

        if ($id) {
            $query->where('questionnaire_id = ?', $id);
        } else {
            $query->orderBy('questionnaire_id');
        }

        return $query->execute();
    }

    /**
     * Gets the report title in the given, current or preferred language. Creates
     * an empty translation if nothing was found and the report exists in the
     * database.
     *
     * @param string $language
     * @return Webenq_Model_ReportText
     */
    public function getReportTitle($language = null)
    {
        // get curren language if not given
        if (!$language) {
            $language = Zend_Registry::get('Zend_Locale')->getLanguage();
        }

        // build array with available languages
        $available = array();
        foreach ($this->ReportTitle as $title) {
            $available[$title->language] = $title;
        }

        // return current language if set
        if (key_exists($language, $available)) {
            return $available[$language];
        }

        // return the first preferred language that is set
        $preferredLanguages = Zend_Registry::get('preferredLanguages');
        foreach ($preferredLanguages as $preferredLanguage) {
            if (key_exists($preferredLanguage, $available)) {
                return $available[$preferredLanguage];
            }
        }

        // return any found language
        if (count($this->ReportTitle) > 0)
        return $this->ReportTitle[0];

        // create empty translation if nothing was found
        if ($this->id) {
            $title = new Webenq_Model_ReportTitle();
            $title->language = $language;
            $title->Report = $this;
            $title->save();
            return $title;
        }

        return new Webenq_Model_ReportTitle();
    }
}