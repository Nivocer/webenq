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

        if ($this->_helper->form->isPostedAndValid($form)) {

            // make sure enough resources are assigned
            try {
                Webenq::setMemoryLimit('512M');
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

                        // skip non-xls and non-xlsx files
                        if (!preg_match('/\.(xls|xlsx)$/', strtolower($filename))) continue;

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

                // compare structures
                $equalStructure = true;
                reset($data);
                while ($current = current($data)) {
                    for ($i = 0; $i < count($data); $i++) {
                        if ($current[0][0] !== $data[$i][0][0]) {
                            $equalStructure = false;
                            break 2;
                        }
                    }
                    next($data);
                }

                foreach ($data as $i => $set) {
                    // ignore first set of data, because this is where the
                    // other data will be merged into
                    if ($i === 0) continue;

                    //combine data
                    $sheet = $set[0];
                    foreach ($sheet as $j => $values) {
                        if ($equalStructure) {
                            // ignore headers
                            if ($j === 0) continue;
                            // add to first set of data
                            $data[0][0][] = $values;
                        } else {
                            if ($j === 0) {
                                $firstEmptyColumn = count($data[0][0][$j]);
                                $data[0][0][$j] = array_merge($data[0][0][$j], $values);
                            } else {
                                $newRow = array();
                                for ($k = 0; $k < $firstEmptyColumn; $k++)
                                    $newRow[$k] = null;
                                $newRow = array_merge($newRow, $values);
                                $data[0][0][] = $newRow;
                            }
                        }
                    }

                    // remove after processing
                    unset($data[$i]);
                }

                // disabled layout and viewRenderer
                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender();

                // return file for download
                $download = new Webenq_Download_Xls();
                $download->setData($data[0][0])->init();
                $download->addWorkingSheet($data[0][1]);
                $download->addWorkingSheet($thirdWorkingSheet);
                $download->send($this->_response);
            }
        }

        $this->view->errors = $errors;
        $this->view->form = $form;
    }
}