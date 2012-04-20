<?php
/* 
 script to fix the yaml output of doctrine dump-data, so we can load it with doctrine load-data

*/

class FixYaml
{
    protected $_directory;

    public function __construct($directory)
    {
        $this->_directory = $directory;
    }

    public function run()
    {
        $yamlFiles = array();
        if (is_dir($this->_directory)) {
            foreach (scandir($this->_directory) as $file) {
                if (preg_match('/\.yml$/', $file)) {
                    $yamlFiles[] = $file;
                }
            }
        }

        foreach ($yamlFiles as $file) {
            $yaml = file_get_contents($this->_directory . '/' . $file);
            //fix foreign key reference
            $yaml = preg_replace("/    answerPossibilityGroup_id: '(\d*)'/",
                "    AnswerPossibilityGroup: Webenq_Model_AnswerPossibilityGroup_$1", $yaml);

            $yaml = preg_replace("/    answerPossibilityGroup_id: null\n/",
                null, $yaml);

            //fix foreign key reference
            $yaml = preg_replace("/    answerPossibility_id: '(\d*)'\n/",
                    "    AnswerPossibility: Webenq_Model_AnswerPossibility_$1\n", $yaml);

            //fix foreign key reference
            $yaml = preg_replace("/    answerPossibilityText_id: '(\d*)'\n/",
                    "    AnswerPossibilityText: Webenq_Model_AnswerPossibilityText_$1\n", $yaml);

            //fix one-many relationship
            $yaml = preg_replace("/    Children: Webenq_Model_CollectionPresentation_(\d*)\n/",
                    "    Children: [Webenq_Model_CollectionPresentation_$1]\n", $yaml);

            file_put_contents($this->_directory . '/' . $file, $yaml);
        }
    }
}
