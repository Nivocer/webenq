<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class ToolController extends Zend_Controller_Action
{
    public function hvaAction()
    {
        $form = new Webenq_Form_Tool_Hva();
        $errors = array();
        $lastRespondentId = 1;
	$config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV);


        if ($this->_helper->form->isPostedAndValid($form)) {

            // make sure enough resources are assigned
            try {
		//add test if setMemory lower than 512M
                //Webenq::setMemoryLimit('512M');
                Webenq::setMaxExecutionTime(0);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }

            // receive the file
            if ($form->file->receive()) {
                $archiveInfo = $form->file->getFileInfo();
                $parts = explode('.', $archiveInfo['file']['name']);
                $extension = array_pop($parts);
            } else {
                $errors[] = 'Error receiving the file';
            }

            if (empty($errors)) {

                // extract archive to tmp dir
                $target = $archiveInfo['file']['destination']
                    . '/' . md5(microtime(true) . mt_rand(0, 10000));
                mkdir($target);
                $filter = new Zend_Filter_Decompress(array(
                	'adapter' => $extension,
                	'options' => array('target' => $target)));

                // process files
                $data = array();
                if ($filter->filter($archiveInfo['file']['tmp_name'])) {
                    $filenames = scandir($target);
                    foreach ($filenames as $filename) {

                        // skip non-xls, non-xlsx and non-ods files
                        if (!preg_match('/\.(xls|xlsx|ods)$/', strtolower($filename))) continue;

                        $tool = new Webenq_Tool_Hva("$target/$filename");
                        $tool->setFirstRespondentId($lastRespondentId);
                        $tool->process();
                        $data[] = $tool->getNewData();
                        $lastRespondentId += $tool->countRespondents();

                        unlink("$target/$filename");
                    }
                }
                rmdir($target);

                // calculate data for third working sheet
                foreach ($data as $i => $set) {

                    if (!isset($thirdWorkingSheet)) $thirdWorkingSheet = $set[2];

                    $value = $set[2][1][1];
                    if (isset($startDate)) {
                        if ($startDate > new Zend_Date($value)) {
                            $startDate = new Zend_Date($value);
                        }
                    } else {
                        $startDate = new Zend_Date($value);
                    }

                    $value = $set[2][2][1];
                    if (isset($endDate)) {
                        if ($endDate < new Zend_Date($value)) {
                            $endDate = new Zend_Date($value);
                        }
                    } else {
                        $endDate = new Zend_Date($value);
                    }

                    $value = $set[2][3][1];
                    if (isset($respondentCount)) {
                        $respondentCount += $value;
                    } else {
                        $respondentCount = $value;
                    }

                    $value = $set[2][4][1];
                    if (isset($emailInvitationCount)) {
                        $emailInvitationCount += $value;
                    } else {
                        $emailInvitationCount = $value;
                    }

                    $value = $set[2][5][1];
                    if (isset($emailResponseCount)) {
                        $emailResponseCount += $value;
                    } else {
                        $emailResponseCount = $value;
                    }

                    $value = $set[2][6][1];
                    if (isset($totalResponseCount)) {
                        $totalResponseCount += $value;
                    } else {
                        $totalResponseCount = $value;
                    }
                }
                $thirdWorkingSheet[0][1] = 'Module-evaluatie';
                $thirdWorkingSheet[1][1] = $startDate->get('Y-MM-dd HH:mm:ss');
                $thirdWorkingSheet[2][1] = $endDate->get('Y-MM-dd HH:mm:ss');
                $thirdWorkingSheet[3][1] = $respondentCount;
                $thirdWorkingSheet[4][1] = $emailInvitationCount;
                $thirdWorkingSheet[5][1] = $emailResponseCount;
                $thirdWorkingSheet[6][1] = $totalResponseCount;
                $thirdWorkingSheet[7][1] = $emailResponseCount / $emailInvitationCount * 100;
                $thirdWorkingSheet[8][1] = 'divers';


                
                //merge data:
                foreach ($data as $setId => $dataSheet){
                	if ($setId ===0) continue;
                 	$data[0]=$this->_mergeData($data[0],$dataSheet);
                 	unset ($data[$setId]);
                }
                
                // disabled layout and viewRenderer
                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender();

                // return file for download
                $download = new Webenq_Download_Xls();
                $download->setData($data[0][0])->init();
                $download->addWorkingSheet($data[0][1]);
                $download->addWorkingSheet($thirdWorkingSheet);
                $download->save($archiveInfo['file']['name']);
            }
        }

        $this->view->errors = $errors;
        $this->view->form = $form;
    }


    /**
     * Merges data array if the question texts are the same
     * 
     * Both input arrays are 'spreadsheet2array'
     * only the first sheet is merged
     *
     * @param array &$data
     * @param array &$newData
     * @return array
     */
    protected function _mergeData(array &$data, array &$newData)
    {
    	$questions = $data[0][0];
    	$questionsNew=$newData[0][0];
    	//test if new data has same questions as old data, if so, just add the data and we are done
    	if ($questions==$questionsNew){
    		//just add the values
    		foreach ($newData[0] as $key=>$answers){
    			//skip first 'row'
    			if ($key === 0) continue;
    			$data[0][]=$answers;
    		}
    	}else {
    		//newData has other questions than old data.
    		//create array with question as value and 'column' as key
    		foreach ($questions as $column => &$question) {
    			//questions start with number and :
    			$pattern="/^(\d*:\s*)*(.*)$/";
    			if (preg_match($pattern, $question, $matches)){
    				$questionsClean[$column]=$matches[2];
    			}else {
    				$questionsClean[$column]=$question;
    			}
       		}
       		if (count($questionsClean)<>count($questions)){
       			//questions are not unique!!!!
       			echo 'questions are not unique';
       		}
       		//create array with questionsNew as value and 'column' as key 
    		foreach ($questionsNew as $column => &$question) {
    			//questions start with number and :
    			$pattern="/^(\d*:\s*)*(.*)$/";
    			if (preg_match($pattern, $question, $matches)){
    				$questionsNewClean[$column]=$matches[2];
    			}else {
    				$questionsNewClean[$column]=$question;
    			}
       		}
       		if (count($questionsClean)<>count($questions)){
       			//questions are not unique!!!!
       			echo 'questions are not unique';
       			return false;
       		}
       		
       		
       		//add questions (headers) that are not in $data[0]
    		foreach ($questionsNew as $columnNew => &$questionNew) {
    			//questions start with number and :
    			$pattern="/^(\d*:\s*)*(.*)$/";
    			if (preg_match($pattern, $questionNew, $matches)){
    				//is it a new question, add it to questionsClean
    				if (!in_array($matches[2], $questionsClean)){
    					$questionsClean[]=$matches[2];
    					$data[0][0][]=$questionNew;
    				}
    			}else {
    				//question has no 'number:'
    				if (!in_array($questionNew, $questionsClean)){
    					$questionsClean[]=$questionNew;
    					$data[0][0][]=$questionNew;
    				}
    			}
       		}
       		//add answers to $data[0]
       		foreach ($newData[0] as $key=>$answers){
    			//skip first 'row'
    			if ($key === 0) continue;
    			$i=0;
    			foreach ($questionsNewClean as $questionNew){
    				
    				$targetColumn=array_search($questionNew, $questionsClean);
    				$row[$targetColumn]=$answers[$i];
    				$i++;	
    			}
    			
    			$data[0][]=$row;
    			//make certain no data from previous respondent with new respondent
    			unset ($row);
    		} 
	    }
    return $data;
    }

}