package it.bisi.report.jasper;

import it.bisi.*;

import java.io.File;
import java.io.InputStream;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Locale;
import java.util.ResourceBundle;

import javax.xml.transform.sax.SAXSource;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathFactory;

import org.xml.sax.InputSource;
import net.sf.saxon.lib.NamespaceConstant;
import net.sf.saxon.om.NodeInfo;
import net.sf.saxon.xpath.XPathEvaluator;


import net.sf.jasperreports.engine.JREmptyDataSource;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperCompileManager;
import net.sf.jasperreports.engine.JasperFillManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.JasperReport;
import net.sf.jasperreports.view.JasperViewer;
import net.sf.jasperreports.engine.JasperExportManager;


public class ExecuteReport {

	/**
	 * @param args
	 *   
	 * webenq 4.3 args:
	 * 0 databaseName
	 * 1 userName, 
	 * 2 password,
	 * 3 reportId
	 * 4 output dir  
	 */
	public static void main(String[] args) {
		runReport(args[0]);
	}


	public static void runReport(String configFileName)
	{
		try{
			//get report config information (location, language, customer, etc)
			Map<String,String> reportConfig=getReportControlFile(configFileName);
			String xformLocation=reportConfig.get("xformLocation");
			String xformName=reportConfig.get("xformName");
			String dataLocation=reportConfig.get("dataLocation");
			String reportDefinitionLocation=reportConfig.get("reportDefinitionLocation");
			String splitQuestionId=reportConfig.get("splitQuestionId");
			String outputDir=reportConfig.get("outputDir");
			String outputFileName=reportConfig.get("outputFileName");
			String outputFormat=reportConfig.get("outputFormat");
			String language=reportConfig.get("language");
			String customer=reportConfig.get("customer");

			// create parameter map to send to jasper
			Map<String,Object> prms = new HashMap<String,Object>();
			prms.put("OUTPUT_DIR", outputDir);
			prms.put("DATA_LOCATION", dataLocation);
			prms.put("XFORM_LOCATION", xformLocation);
			prms.put("FORM_NAME", xformName);
			prms.put("CUSTOMER", customer); //resource bundle and needed for hacks in jrxml
			prms.put("SPLIT_QUESTION_ID", splitQuestionId ); //not yet implemented #5395

			//get map of colors of mean values
			HashMap<String,Map<String,Double>> color_range_map=it.bisi.Utils.getColorRangeMaps(customer);
			prms.put("COLOR_RANGE_MAP",color_range_map);

			//localization
			Locale locale = new Locale("nl", "NL");
			if (language.equals("nl")){
				locale = new Locale("nl", "NL");
			}else {
				locale = new Locale("en", "US");
			}
			prms.put(JRParameter.REPORT_LOCALE, locale);
			Locale.setDefault(locale);


			//Get resource bundles with general texts
			//TODO put general text in resource bundles 
			//TODO nicer handling missing files
			// eg http://jasperforge.org/plugins/espforum/view.php?group_id=102&forumid=103&topicid=65623
			if (customer.equals("fraijlemaborg")) {
				ResourceBundle myresources = ResourceBundle.getBundle("org.webenq.resources.fraijlemaborg",locale);
				prms.put(JRParameter.REPORT_RESOURCE_BUNDLE, myresources);
			}else {
				ResourceBundle myresources = ResourceBundle.getBundle("org.webenq.resources.default",locale);
				prms.put(JRParameter.REPORT_RESOURCE_BUNDLE, myresources);
			}

			//looping through available split by values (seperate reports for subsets of respondents)
			if (splitQuestionId !=null && splitQuestionId.length()>0 ) {
				//get distinct split_values, using xpath2 (saxon)
				String searchSplitValues="distinct-values(//respondenten/respondent/*/"+splitQuestionId+")";

				XPathFactory factory = XPathFactory.newInstance(NamespaceConstant.OBJECT_MODEL_SAXON);
				XPath xpath = factory.newXPath();
				InputSource is = new InputSource(dataLocation);
				SAXSource ss = new SAXSource(is);
				NodeInfo doc = ((XPathEvaluator)xpath).setSource(ss);
				XPathExpression expr = xpath.compile(searchSplitValues);
				List splitValuesList=(List) expr.evaluate(doc, XPathConstants.NODESET);

				// iterate through the split_question_values, and create report for each of them
				String splitQuestionValue;
				for (Iterator iter = splitValuesList.iterator(); iter.hasNext();) {
					splitQuestionValue = (String) iter.next();
					//needed for displaying content
					prms.put("SPLIT_QUESTION_VALUE", splitQuestionValue);
					System.out.println("split values");
					generateReport(reportDefinitionLocation, prms, splitQuestionValue, outputDir, outputFileName, outputFormat );

				}
			}else{
				//no split value
				prms.put("SPLIT_QUESTION_VALUE", "");
				System.out.println("no split values");
				generateReport(reportDefinitionLocation, prms, "", outputDir, outputFileName, outputFormat );
			}
		}

		catch(Exception ex) {
			ex.printStackTrace();
		}
	}

	static void generateReport(String reportDefinitionLocation, Map<String,Object> prms, String splitQuestionValue, String outputDir, String outputFileName, String outputFormat ) throws Exception{
		//clean fileName
		if (splitQuestionValue.length()>0){
			// no slash in split part
			outputFileName=fileName(outputDir,true)+"/"+fileName(outputFileName,false)+"-"+fileName(splitQuestionValue,false);
		}else{
			//output_file_name=fileName(output_file_name, true);
			outputFileName=fileName(outputDir,true)+"/"+fileName(outputFileName,false);
		}
		InputStream inputStream = Utils.class.getResourceAsStream(reportDefinitionLocation);
		JasperPrint print;
		
		if (reportDefinitionLocation.endsWith("jrxml")){
			//need to compile the report
			JasperReport jasperReport = JasperCompileManager.compileReport(inputStream);
			// we need an empty datasource to display the report...
			//we can extend this to encapsulated subreports into reports (i think)
			print = JasperFillManager.fillReport(jasperReport, prms, new JREmptyDataSource());
		}else{
			//report is already compiled
			print = JasperFillManager.fillReport(inputStream, prms, new JREmptyDataSource());
		}

		// Create output in directory public/reports  
		if(outputFormat.equals("pdf")) {
			net.sf.jasperreports.engine.export.JRPdfExporter exporter = new net.sf.jasperreports.engine.export.JRPdfExporter(); 
			exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, outputFileName + ".pdf");
			exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
			exporter.exportReport();
		} else if(outputFormat.equals("odt")) {
			net.sf.jasperreports.engine.export.oasis.JROdtExporter exporter = new net.sf.jasperreports.engine.export.oasis.JROdtExporter();
			exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, outputFileName + "-" + splitQuestionValue+ ".odt");
			exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
			exporter.exportReport();
		} else if(outputFormat.equals("html")) {
			JasperExportManager.exportReportToHtmlFile(print, outputFileName +"-"+splitQuestionValue+ ".html");
		} else if(outputFormat.equals("xml")) {
			JasperExportManager.exportReportToXmlFile(print, outputFileName +"_"+splitQuestionValue+ ".xml", false);
		} else if(outputFormat.equals("xls")) {
			net.sf.jasperreports.engine.export.JRXlsExporter exporter = new net.sf.jasperreports.engine.export.JRXlsExporter();
			exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, outputFileName +"_"+splitQuestionValue+".xls");
			exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
			exporter.exportReport();
		} else { 
			JasperViewer.viewReport(print);
		}	
	}
	static String fileName(String fileName, Boolean keepPath){
		//replace whitespaces with underscore
		fileName=fileName.replaceAll("\\p{javaWhitespace}","_");
		//replace some other character to underscore
		fileName=fileName.replaceAll("[^A-Za-z0-9_=\\/\\-\\+\\.]", "_");
		if (!keepPath){
			fileName=fileName.replaceAll("/", "_");
		}
		//remove duplicate underscores
		fileName=fileName.replaceAll("_+", "_");
		return fileName;

	}
	
	/**
	 * @param configFilename
	 * @return	file with the reportconfiguration (one at this moment)
	 * @throws Exception
	 */
	public static Map<String,String> getReportControlFile(String configFilename) throws Exception {
		//define return variable
		Map<String,String> reportConfig = new HashMap<String,String>();

		//create saxon stuff
		XPathFactory xpf = XPathFactory.newInstance(NamespaceConstant.OBJECT_MODEL_SAXON);
		XPath xpe = xpf.newXPath();
		InputSource is = new InputSource(new File(configFilename).toURI().toURL().toString());
		SAXSource ss = new SAXSource(is);
		NodeInfo doc = ((XPathEvaluator)xpe).setSource(ss);

		String searchConfig="/reportConfig/*";
		XPathExpression expr = xpe.compile(searchConfig);

		List reportConfigResult = (List)expr.evaluate(doc, XPathConstants.NODESET);
		if (reportConfigResult != null) {
			for (Iterator iter = reportConfigResult.iterator(); iter.hasNext();) {
				// put the next control parameter in the return variable
				NodeInfo line = (NodeInfo)iter.next();
				reportConfig.put(line.getDisplayName(), line.getStringValue());
			}
		}
		return reportConfig;
	}
}