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
        $form = new Webenq_Form_Tool_Hva(array('xls', 'xlsx'));
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
                $filenames = $form->file->getFileName();
            } else {
                $errors[] = 'Error receiving the file';
            }

            if (empty($errors)) {

                // process data
                $data = array();
                foreach ($filenames as $filename) {
                    $tool = new Webenq_Tool_Hva($filename);
                    $tool->setFirstRespondentId($lastRespondentId);
                    $tool->process();
                    $data[] = $tool->getNewData();
                    $lastRespondentId += $tool->countRespondents();
                }

                // compare structures
                $equalStructure = true;
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
                $download->send($this->_response);
            }
        }

        $this->view->errors = $errors;
        $this->view->form = $form;
    }
}