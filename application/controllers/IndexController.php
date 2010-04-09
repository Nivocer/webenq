<?php

class IndexController extends Zend_Controller_Action
{
	/*
	 * Configuration object
	 */
	protected $_config;
	
	/*
	 * Name of the file to read the data from
	 */
	protected $_filename;
	
	/*
	 * DbTable Model for columns table
	 */
	protected $_columnsNl;
	
	/*
	 * DbTable Model for english columns table
	 */
	protected $_columnsEn;
	
	/*
	 * DbTable Model for rows table
	 */
	protected $_rows;
	
	/*
	 * Number of columns that should be read from data file
	 */
	protected $_noCols;
	
	/*
	 * Number of rows that should be read from data file (excluding header row)
	 */
	protected $_noRows;
	
	/*
	 * Id's of the columns that should be fetched
	 */
	protected $_columnsToFetch = array();
	
	/*
	 * Logo used on pdf pages
	 */
	protected $_logo;
	
	/*
	 * Default pdf font
	 */
	protected $_font;
	
	/*
	 * Long names of the courses
	 */
	protected $_courseNames = array();
	
	
	/**
	 * Configuration
	 */
    public function init()
    {
    	// set exec time
    	ini_set("max_execution_time", 0);

    	// read config file
    	$this->_config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/config.ini");
    	
    	// get number of cols in data file
    	$this->_noCols = $this->_getNoCols();
    	
    	// get number of rows in data file
    	$this->_noRows = $this->_getNoRows();
    	
    	// get columns model    	
    	$this->_columnsNl = new HVA_Model_DbTable_columnsNl();
    	
    	// get columns model    	
    	$this->_columnsEn = new HVA_Model_DbTable_columnsEn();
    	
    	// get rows model    	
    	$this->_rows = new HVA_Model_DbTable_Rows();
    	
    	// set columns to fetch
    	$this->_columnsToFetch = explode(",", $this->_config->output->columnsToFetch);
    	
    	// pdf defaults
    	$this->_font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		$this->_logo = Zend_Pdf_Image::imageWithPath($this->_config->input->path . "logo.jpg");
		
		// set course names
		$this->_courseNames = array(
			'AC'=>'Accountancy',
			'BE'=>'Bedrijfseconomie',
			'BI'=>'Bedrijfskundige Informatica',
			'CE'=>'Commerciële Economie',
			'CO'=>'Communicatie',
			'FE'=>'Fiscale Economie',
			'FSM'=>'Financial Services Management',
			'IBL'=>'International Business and Languages',
			'IBMS'=>'International Business and Management Studies',
			'IFM'=>'International Financial Management',
			'IM'=>'International Management',
			'LE'=>'Logistiek en Economie',
			'MER'=>'Management, Economie en Recht',
			'SPM'=>'Sport Marketing',
			'TMA'=>'Trade Management Asia'); 		
    }

    
    public function indexAction()
    {
    	// Are the column headers allready stored to db?    	
    	$noColumns = $this->_columnsNl->fetchAll()->count();
    	if ($noColumns === 0 || $noColumns < $this->_noCols) {
    		$this->_storeColumnsNl();
    	}
    	
    	// Are the english column headers allready stored to db?    	
    	$noColumns = $this->_columnsEn->fetchAll()->count();
    	if ($noColumns === 0 || $noColumns < $this->_noCols) {
    		$this->_storeColumnsEn();
    	}
    	
    	// Have all rows been stored to db?    	
    	$noRows = $this->_rows->fetchAll()->count();
    	if ($noRows !== $this->_noRows) {
    		$this->_storeRows();
    	}

    	$this->createSingleReports("Nl");
    	$this->createSingleReports("En");
    	$this->createReportsCollection("Nl");
    	$this->createReportsCollection("En");
    }
    
    
    /*
     * Gets the number of rows in the data source file
     */
    protected function _getNoCols()
    {
    	// file to read the data from
    	$filename = $this->_config->input->path . $this->_config->input->datafile;
    	
    	$fp = fopen($filename, "r");
    	$headers = fgetcsv($fp, 0, ";");
    	return count($headers);
    }
    
    
    /*
     * Gets the number of rows in the data source file
     */
    protected function _getNoRows()
    {
    	// file to read the data from
    	$filename = $this->_config->input->path . $this->_config->input->datafile;
    	
    	$fp = fopen($filename, "r");
    	$n = 0;
    	while (fgetcsv($fp, 0, ";")) {
    		$n++;
    	}
    	return $n-1;
    }
    
    
    protected function _storeColumnsNl()
    {    	
    	// file to read the data from
    	$filename = $this->_config->input->path . $this->_config->input->datafile;
    	
    	// check file
    	if (!file_exists($filename)) {
    		throw new Exception("De data-file $filename bevind zich niet op de opgegeven locatie. Pas de instellingen aan in application/configs/config.ini");
    	}
    	
    	// open file
    	$fp = fopen($filename, "r");
    	
    	// get headers (first line)
    	$headers = fgetcsv($fp, 0, ";");
    	
    	// loop through headers and store to database
    	foreach ($headers as $id => $header) {
    		$this->_columnsNl->insert(array(
    			"column_id" => $id,
    			"value" => utf8_decode($header)));
    	}
    }
    	
    	
    protected function _storeColumnsEn()
    {    	
    	// file to read the data from
    	$filename = $this->_config->input->path . $this->_config->input->en;
    	
    	// check file
    	if (!file_exists($filename)) {
    		throw new Exception("De data-file $filename bevind zich niet op de opgegeven locatie. Pas de instellingen aan in application/configs/config.ini");
    	}
    	
    	// open file
    	$fp = fopen($filename, "r");
    	
    	// get headers (first line)
    	$headers = fgetcsv($fp, 0, ";");
    	
    	// loop through headers and store to database
    	foreach ($headers as $id => $header) {
    		$this->_columnsEn->insert(array(
    			"column_id" => $id,
    			"value" => utf8_decode($header)));
    	}
    }
    
    
    protected function _storeRows()
    {    	
    	// file to read the data from
    	$filename = $this->_config->input->path . $this->_config->input->datafile;
    	
    	// check file
    	if (!file_exists($filename)) {
    		throw new Exception("De data-file $filename bevind zich niet op de opgegeven locatie. Pas de instellingen aan in application/configs/config.ini");
    	}
    	
    	// open file
    	$fp = fopen($filename, "r");
    	
    	$data = array();    	
    		
    	// ignore first line (headers)
    	$cols = fgetcsv($fp, 0, ";");
    	$numC = count($cols);
    	
//    	// build table
//    	$q = "DROP TABLE IF EXISTS rows; CREATE TABLE rows (";
//    	for ($i=0; $i<=$numC; $i++) {
//    		$q .= "col$i TEXT,";
//    	}
//    	$q = substr($q, 0, -1) . ");";
//    	echo $q;
//    	die;
    	
    	// loop trough lines and store to db
    	$insert = array();
    	while ($data = fgetcsv($fp, 0, ";")) {
	    	for ($i=0; $i<=$numC; $i++) {
	    		$insert["col$i"] = $data[$i];
	    	}
	    	$this->_rows->insert($insert);
    	}
    }
    
    
    public function createReportsCollection($lang="Nl")
    {
    	$columns = $this->{_columns.ucfirst(strtolower($lang))};
    	
		// get courses and loop through
		$courses = $this->_rows->getCourses();
		foreach ($courses as $course) {
			
			// load pdf template
		    $template = $this->_config->input->path . $this->_config->input->{front . strtolower($lang)};
			$pdf = Zend_Pdf::load($template);
		
			// print course name on front-page
			$courseName = $this->_courseNames[$course->col29];
			$pdf->pages[0]->setFont($this->_font, 20)->setFillColor(Zend_Pdf_Color_Html::color('#ffffff'));
			$pdf->pages[0]->drawText(
				$courseName,
				($pdf->pages[0]->getWidth() - strlen($courseName) * 11) / 2,
				350);
				
			// get reports and loop through
			$reports = $this->_rows->getReportsByCourse($course->col29);
			foreach ($reports as $report) {
		    	// get page(s) and attach to document
				$pages = $this->_createReport($report, $columns, $lang);
				if ($pages !== null) {
					foreach ($pages as $page) {
						$pdf->pages[] = $page;
					}
				}
			}
			// save document
			$save = $this->_config->output->path . $course->col29 . "-" . strtolower($lang) . ".pdf";
			$save = str_replace(" ", "", $save);
			$pdf->save($save);
		}
    }


    public function createSingleReports($lang="Nl")
    {
    	$columns = $this->{_columns.ucfirst(strtolower($lang))};
		
		// get all reporting categories
		$reports = $this->_rows->getReports();
		
		// loop through reports			
		foreach ($reports as $report) {
	    	
			// create pdf
	    	$pdf = new Zend_Pdf();

	    	// get page(s)
			$pages = $this->_createReport($report, $columns, $lang);
			
			// attach pages to document
			if ($pages !== null) {
				foreach ($pages as $page) {
					$pdf->pages[] = $page;
				}
				
				// save document
				$save = $this->_config->output->path . $report->col37 . "-" . $report->col27 . "-" . strtolower($lang) . ".pdf";
				$save = str_replace(" ", "", $save);
				$pdf->save($save);
			}
		}
    }
    
    
    protected function _createReport($report, $columns, $lang="Nl")
    {
    	$pages = array();
    	$pn = 0;
    	$anyOutput = false;
    	
    	// set offset
    	$newX = $x = 60;
    	$newY = $y = 700;
    	$lh = 13;
    
		// get general report data			
		$groep = $report->col25;
		$docent = $report->col37;
		$boecode = $report->col26;
		$opleiding = utf8_decode($report->col36);
		$oplCode = $report->col29;
		
    	// create page
    	$pages[$pn] = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
        $pages[$pn]->drawImage($this->_logo, 320, 760, 550, 800);
			
		// Apply font and draw text
		$pages[$pn]->setFont($this->_font, 12)->setFillColor(Zend_Pdf_Color_Html::color('#9999cc'));
		$pages[$pn]->drawText('Onderwijsevaluaties ' . $oplCode, 60, 780);
		$pages[$pn]->drawText('Semester 1 - 2009-2010', 60, 760);
		$pages[$pn]->drawText('Open Antwoorden', 60, 740);
		
		$pages[$pn]->setFont($this->_font, 12)->setFillColor(Zend_Pdf_Color_Html::color('#9999cc'));
		if (strtolower($lang) === "en") {
			$pages[$pn]->drawText("group", $x, $y);
			$pages[$pn]->drawText("lecturer", $x+60, $y);
			$pages[$pn]->drawText("boecode", $x+120, $y);
			$pages[$pn]->drawText("module/project", $x+220, $y);
		} else {
			$pages[$pn]->drawText("groep", $x, $y);
			$pages[$pn]->drawText("docent", $x+60, $y);
			$pages[$pn]->drawText("boecode", $x+120, $y);
			$pages[$pn]->drawText("module/project", $x+220, $y);
		}
		$y = $y - $lh;
		
		$pages[$pn]->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
		$pages[$pn]->drawText($groep, $x, $y);
		$pages[$pn]->drawText($docent, $x+60, $y);
		$pages[$pn]->drawText($boecode, $x+120, $y);
		$pages[$pn]->drawText($opleiding, $x+220, $y);
		$y = $y - $lh;
		$y = $y - $lh;
		
		// get all students in current reporting category
		$students = $this->_rows->getRowsByReport($report->col32);
		
		// create segment for all answers to include
		foreach ($this->_columnsToFetch as $columnId) {
			
	    	// print question
	    	$questionText = $columns->getHeaderById($columnId);
	    	$questionText = preg_replace("/(\d+): /", "", $questionText);
			$y = $y - $lh;
			$pages[$pn]->setFont($this->_font, 10)->setFillColor(Zend_Pdf_Color_Html::color('#000000'));
			$pages[$pn]->drawText($questionText, $x, $y);
			$y = $y - $lh;
				
			$answersGiven = false;
			$n = 1;
			
			// loop through students
			foreach ($students as $student) {
				
				// get answer				
				$answer = $student->{"col".$columnId};
				if ($answer !== "") {
					
					// start new page if almost at bottom
					if ($y < 100) {
						$pn++;
    					$pages[$pn] = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
						$pages[$pn]->setFont($this->_font, 10)->setFillColor(Zend_Pdf_Color_Html::color('#9999cc'));
						$y = 750;
					}
					
					// at least one answer given
					$answersGiven = true;
					$anyOutput = true;
					
					// do things with answer
					$answer = utf8_decode($answer);
					$answer = str_replace("\n", " ", $answer);
					
					// cut into pieces for right placement
					$wordwrap = wordwrap($answer, 80, "###");
					$split = split("###", $wordwrap);
					
					foreach ($split as $key => $text) {
						$pages[$pn]->setFillColor(Zend_Pdf_Color_Html::color('#9999cc'));
						// print student number
						if ($key === 0) $pages[$pn]->drawText("- $n -", $x, $y);
						// print answer (part)
						$pages[$pn]->drawText($text, $x+30, $y);
						$y = $y - $lh;
					}
					// increase iterator
					$n++;
				}
			}
		}
		if ($anyOutput === true) {
			return $pages;
		} else {
			return null;
		}
    }
}