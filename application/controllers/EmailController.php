<?php
/**
 * WebEnq4
 *
 *  LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    Webenq
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Email Controller class
 *
 * Batch sends report to end users, tailored for a specific client, used in webenq < 4.4
 *
 * @package     Webenq
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 * @deprecated
 */
class EmailController extends Zend_Controller_Action
{
    const CSV_DELIMITER = ',';
    const CSV_ENCLOSURE = '"';

    protected $_email;

    protected $_filePatterns = array(
        'departmentA' => '#^departmentA-open-(docent|opleiding)-#',
    );

    public function init()
    {
        $this->_email = new Webenq_Model_DbTable_Email();
    }

    protected function _findDirs($dir)
    {
        $dirs = array();
        $files = scandir($dir);

        foreach ($files as $file) {
            if (is_dir($dir . '/' . $file) && $file !== '.' && $file !== '..' && $file !== 'CVS') {
                $dirs[$file] = $file;
            }
        }

        return $dirs;
    }

    protected function _findFiles($dir)
    {
        $files = scandir($dir);
        $foundReports = array();

        foreach ($this->_filePatterns as $filePatternName => $filePattern) {
            foreach ($files as $file) {
                if (preg_match($filePattern, $file)) {
                    $foundReports[$filePatternName][] = $file;
                }
            }
        }

        /* add new reports to db */
        foreach ($foundReports as $customer => $reports) {
            foreach ($reports as $report) {
                $filename = preg_replace('#^'.realpath('.').'/#', '', $dir.'/'.$report);
                try {
                    $this->_email->insert(array('filename' => $filename, 'customer' => $customer, ));
                } catch (Exception $e) {
                    if ($e->getCode() !== "23000") {
                        throw $e;
                    }
                }
            }
        }

        /* remove old reports from db */
        $relativeDir = preg_replace('#^'.realpath('.').'/#', '', $dir);
        $where = 'filename LIKE "' . $relativeDir . '/%"';
        foreach ($this->_email->fetchAll($where) as $report) {
            $filename = preg_replace('#^'.$relativeDir.'/#', '', $report->filename);
            if (!in_array($filename, $files)) {
                $this->_email->delete("filename = '" . $report->filename . "'");
            }
        }
    }

    public function indexAction()
    {
        $subDirs = $this->_findDirs(realpath('reports'));
        $form = new Webenq_Form_Email_Index($subDirs);

        if ($this->_helper->form->isPostedAndValid($form)) {

            $dir = realpath('reports/' . $this->_request->selectDir);
            $relativeDir = preg_replace('#^'.realpath('.').'/#', '', $dir);
            $this->_findFiles($dir);

            try {
                $this->view->reports = $this->_email->fetchAll(
                    $this->_email->select()
                    ->where('filename LIKE ?', $relativeDir.'/%')
                    ->order(array("customer", "filename"))
                );
            } catch (Exception $e) {
                if ($e->getCode() === "42S02") {
                    $this->_email->createTable();
                } else {
                    throw $e;
                }
            }
        } else {
            $this->view->form = $form;
        }
    }

    public function sendAllAction()
    {
        $reports = $this->_email->fetchAll(
            $this->_email->select()
            ->order(array("customer", "filename"))
            ->where("sent = 0 AND email IS NOT NULL")
        );

        foreach ($reports as $report) {
            $this->_send($report);
        }

        $this->_redirect("email");
    }

    public function mergeTeacherAction()
    {
        $form = $this->view->form = new Webenq_Form_Email_Merge(array('csv'));
        $form->file->setLabel('Select the file:');
        $errors = array();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                if (!$form->file->receive()) {
                    $errors[] = 'Error receiving the file';
                } else {
                    $this->_filename = $form->file->getFileName();
                    $filenameParts = preg_split('#\.#', $this->_filename);
                    $extension = array_pop($filenameParts);
                    try {
                        $action = "_process" . ucfirst($extension);
                        $this->{$action}();
                    } catch (Exception $e) {
                        $errors[] = 'Error processing the files';
                    }
                }
                if ($errors) {
                    $this->view->errors = $errors;
                } else {
                    $this->_redirect('email');
                }
            }
        }
    }

    protected function _processCsv()
    {
        /* open file */
        $fp = fopen($this->_filename, "r");

        /* get teachers from file */
        $teachers = $this->_getTeachers($fp);

        /* get groups and codes for teacher */
        foreach ($teachers as $teacher => $email) {
            $codes = $this->_getGroupsAndCodesForTeacher($fp, $teacher);
            $teachers[$teacher]['courses'] = $codes;
        }

        /* find and merge reports for teacher */
        foreach ($teachers as $teacher) {
            $reports = $this->_getReportsForTeacher($teacher);
            if (count($reports) > 0) {
                $this->_mergeReportsForTeacher($reports, $teacher['name']);
            }
        }

        /* update db */
        foreach ($teachers as $teacher) {
            /* fetch matching row from db */
            $rowset = $this->_email->fetchAll("filename LIKE '%-open-docent-" . $teacher['name'] . ".%'");
            if ($rowset instanceof Zend_Db_Table_Rowset && $rowset->count() > 0) {
                /* loop through rows */
                foreach ($rowset as $row) {
                    /* any changes? update and reset "sent" */
                    if ($row->teacher !== $teacher['name'] || $row->email !== $teacher['email']) {
                        $this->_email->update(
                            array(
                                "teacher"    => $teacher['name'],
                                "email"        => $teacher['email'],
                                "sent"        => 0
                            ),
                            "id = $row->id"
                        );
                    }
                }
            }
        }

        /* close file */
        fclose($fp);

        $this->_redirect("email");
    }

    public function mergeCourseAction()
    {
        $courses = array();
        $foundReports = array();
        $output = "departmentA-open-opleiding-";

        /* get all files */
        $files = scandir('reports');

        /* get courses from file names */
        $filePattern = "#^departmentA_open_.*_(.*)-.*-.*-.*-.*\.pdf$#";
        foreach ($files as $file) {
            if (preg_match($filePattern, $file, $matches)) {
                $courses[] = $matches[1];
            }
        }
        $courses = array_unique($courses);

        /* get reports for courses */
        foreach ($courses as $course) {
            $filePattern = "#^departmentA_open_.*_" . $course . "-.*-.*-.*\.pdf$#";
            foreach ($files as $file) {
                if (preg_match($filePattern, $file)) {
                    $foundReports[$course][] = $file;
                }
            }
        }

        /* merge reports */
        if (count($foundReports) > 0) {
            foreach ($foundReports as $course => $reports) {
                sort($reports);
                /* determin language of report */
                $language=$this->_getLanguageFromReports($reports);

                /*todo opleiding voorblad */

                $cmd = "pdftk ";
                if ($language=='nl') {
                    $cmd .= " reports/departmentA_open_nl_voorblad_".$course.".pdf ";
                    //$cmd .= " reports/departmentA_open_nl_voorblad.pdf ";
                } elseif ($language=='en') {
                    $cmd .= " reports/departmentA_open_en_voorblad_".$course.".pdf ";
                    //$cmd .= " reports/departmentA_open_en_voorblad.pdf ";
                }
                foreach ($reports as $report) {

                    $cmd .= "reports/" . $report . " ";
                }
                $cmd .= "cat output reports/" . $output . $course . ".pdf";

                system($cmd);
            }
        }

        $this->_redirect("email");
    }

    public function delAction()
    {
        /* get form */
        $form = new Zend_Form();
        $confirm = new Zend_Form_Element_Submit('confirm');
        $confirm->setLabel("yes, delete")->setValue("yes");
        $form->addElement($confirm);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_processDel();
                $this->_redirect("email");
            }
        }

        $this->view->form = $form;
    }


    protected function _processDel()
    {
        /* get report by id*/
        (int) $id = $this->getRequest()->getParam('id');
        $report = $this->_email->fetchRow("id = $id");

        /* remove file */
        $file = getcwd() . '/reports/' . $report->filename;
        if (file_exists($file)) {
            unlink($file);
        }
    }


    public function sendAction()
    {
        /* get report by id */
        (int) $id = $this->_request->getParam('id');
        $report = $this->_email->fetchRow("id = $id");

        /* send it */
        $this->_send($report);

        /* go to email overview page */

        $this->_redirect("email");
    }


    protected function _send(Zend_Db_Table_Row $report)
    {
        $this->view->report = $report;

        /* get mail config options */
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        $test = $config->{APPLICATION_ENV}->email->test;
        $smtp = $config->{APPLICATION_ENV}->email->smtp;

        /* set mail transport */
        $transport = new Zend_Mail_Transport_Smtp(
            $smtp->host, array(
                'ssl'        => $smtp->ssl,
                'port'        => $smtp->port,
                'auth'        => $smtp->auth,
                'username'    => $smtp->username,
                'password'    => $smtp->password,
                )
        );
        Zend_Mail::setDefaultTransport($transport);

        /* build messages */
        $messageText = $this->view->render('email/' . $report->customer . '/message-text.phtml');
        $messageHtml = $this->view->render('email/' . $report->customer . '/message-html.phtml');

        /* build attachment */
        $content = file_get_contents($report->filename);
        $attachment = new Zend_Mime_Part($content);
        $attachment->type = "application/pdf";
        $attachment->encoding = Zend_Mime::ENCODING_BASE64;
        $attachment->filename = $report->filename;
        $attachment->disposition = "attachment; filename=\"" . $report->filename . "\"";

        /* instantiate mail object */
        $mail = new Zend_Mail();
        $mail->setFrom("pietje@example.com", "Pietje Example")
            ->setBodyText($messageText)
            ->setBodyHtml($messageHtml)
            ->setSubject("subject")
            ->addAttachment($attachment);

        /* add test address or real address, and send mail */
        if ($test->use == "1") {
            $mail->addTo($test->address, $test->address);
        } else {
            /* cannot send without email address */
            if (!$report->email) return false;

            $mail->addTo($report->email, $report->teacher)
                ->addBcc($test->address, $test->address);
        }
        $mail->send();

        /* update db */
        $this->_email->update(array("sent" => "1"), "id = $report->id");
    }

    protected function _getTeachers($fp)
    {
        $teachers = array();

        /* ignore first line (headers) */
        fgetcsv($fp, 0, self::CSV_DELIMITER, self::CSV_ENCLOSURE);

        /* get data */
        while ($data = fgetcsv($fp, 0, self::CSV_DELIMITER, self::CSV_ENCLOSURE)) {
            $teachers[$data[0]]['name'] = $data[0];
            $teachers[$data[0]]['email'] = $data[4];
        }

        /* reset file pointer */
        rewind($fp);

        return $teachers;
    }

    protected function _getGroupsAndCodesForTeacher($fp, $teacher)
    {
        $codes = array();

        /* ignore first line (headers) */
        fgetcsv($fp, 0, self::CSV_DELIMITER, self::CSV_ENCLOSURE);

        /* get data */
        while ($data = fgetcsv($fp, 0, self::CSV_DELIMITER, self::CSV_ENCLOSURE)) {
            if ($data[0] === $teacher) {
                $tmp = array(
                    'group'    => $data[1],
                    'code'    => $data[2],
                );
                $codes[] = $tmp;
            }
        }
        return $codes;
    }

    protected function _getReportsForTeacher($teacher)
    {
        $files = scandir('reports');
        $reports = array();

        /* search for "own" reports */
         $pattern = "#^departmentA_open_.*_.*-.*-.*-.*-" . $teacher['name'] . "\.pdf$#";

        foreach ($files as $file) {
            if (preg_match($pattern, $file)) {
                $reports[] = $file;
            }
        }

        /* search for "extra" files */
        foreach ($teacher['courses'] as $course) {
             $pattern = "#^departmentA_open_.*_.*-.*-" . $course['code'] . "-".$course['group']."-.*\.pdf$#";

            foreach ($files as $file) {
                if (preg_match($pattern, $file)) {
                    $reports[] = $file;
                }
            }
        }
        $reports=array_unique($reports);
        sort($reports);
        return $reports;
    }

    protected function _mergeReportsForTeacher($reports, $teacher)
    {
        $output = "departmentA-open-docent-";
        $language=$this->_getLanguageFromReports($reports);
        echo "language: $language<br/>";
        $cmd = "pdftk ";
        if ($language=='nl') {
            $cmd .= " reports/departmentA_open_nl_voorblad.pdf ";
        } elseif ($language=='en') {
            $cmd .= " reports/departmentA_open_en_voorblad.pdf ";
        }
        foreach ($reports as $report) {
            $cmd .= "reports/" . $report . " ";
        }
        $cmd .= "cat output reports/" . $output . $teacher . ".pdf";

        system($cmd);
    }
    protected function _getLanguageFromReports($reports)
    {
        /* determin language of report */
        $filePattern = "#^departmentA_open_(.*)_.*-.*-.*-.*-.*\.pdf$#";
        if (preg_match($filePattern, $reports[0], $matches)) {
            $language= $matches[1];
        }
        return $language;
    }
}