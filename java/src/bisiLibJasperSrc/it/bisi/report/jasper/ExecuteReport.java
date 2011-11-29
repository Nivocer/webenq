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
			String dataLocation=reportConfig.get("dataLocation");
			String reportDefinitionLocation=reportConfig.get("reportDefinitionLocation");
			String split_question_id=reportConfig.get("split_question_id");
			String output_dir=reportConfig.get("output_dir");
			String output_file_name=reportConfig.get("output_file_name");
			String output_format=reportConfig.get("output_format");
			String language=reportConfig.get("language");
			String customer=reportConfig.get("customer");

			// create parameter map to send to jasper
			Map<String,Object> prms = new HashMap<String,Object>();
			prms.put("OUTPUT_DIR", output_dir);
			prms.put("DATA_LOCATION", dataLocation);
			prms.put("XFORM_LOCATION", xformLocation);
			prms.put("CUSTOMER", customer); //resource bundle and needed for hacks in jrxml
			prms.put("SPLIT_QUESTION_ID", split_question_id ); //not yet implemented #5395

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
			if (split_question_id !=null && split_question_id.length()>0 ) {
				//get distinct split_values, using xpath2 (saxon)
				String searchSplitValues="distinct-values(//respondenten/respondent/*/"+split_question_id+")";

				XPathFactory factory = XPathFactory.newInstance(NamespaceConstant.OBJECT_MODEL_SAXON);
				XPath xpath = factory.newXPath();
				InputSource is = new InputSource(dataLocation);
				SAXSource ss = new SAXSource(is);
				NodeInfo doc = ((XPathEvaluator)xpath).setSource(ss);
				XPathExpression expr = xpath.compile(searchSplitValues);
				List splitValuesList=(List) expr.evaluate(doc, XPathConstants.NODESET);

				// iterate through the split_question_values, and create report for each of them
				String split_question_value;
				for (Iterator iter = splitValuesList.iterator(); iter.hasNext();) {
					split_question_value = (String) iter.next();
					//needed for displaying content
					prms.put("SPLIT_QUESTION_VALUE", split_question_value);

					generateReport(reportDefinitionLocation, prms, split_question_value, output_dir, output_file_name, output_format );

				}
			}else{
				//no split value
				prms.put("SPLIT_QUESTION_VALUE", "");
				generateReport(reportDefinitionLocation, prms, "", output_dir,output_file_name, output_format );
			}
		}

		catch(Exception ex) {
			String connectMsg = "Could not create the report " + ex.getMessage() + " " + ex.getLocalizedMessage();
			ex.printStackTrace();
		}
	}

	static void generateReport(String reportDefinitionLocation, Map<String,Object> prms, String split_question_value, String output_dir, String output_file_name, String output_format ) throws Exception{
		//clean fileName
		if (split_question_value.length()>0){
			// no slash in split part
			//split_question_value=fileName(split_question_value, false);
			//output_file_name=fileName(output_file_name+"-"+split_question_value, true);
			output_file_name=fileName(output_dir,true)+"/"+fileName(output_file_name,false)+"-"+fileName(split_question_value,false);
		}else{
			//output_file_name=fileName(output_file_name, true);
			output_file_name=fileName(output_dir,true)+"/"+fileName(output_file_name,false);
		}
		InputStream inputStream = Utils.class.getResourceAsStream(reportDefinitionLocation);
		JasperPrint print;
		if (reportDefinitionLocation.endsWith(".jrxml")){
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
		if(output_format.equals("pdf")) {
			net.sf.jasperreports.engine.export.JRPdfExporter exporter = new net.sf.jasperreports.engine.export.JRPdfExporter(); 
			exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name + ".pdf");
			exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
			exporter.exportReport();
		} else if(output_format.equals("odt")) {
			net.sf.jasperreports.engine.export.oasis.JROdtExporter exporter = new net.sf.jasperreports.engine.export.oasis.JROdtExporter();
			exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name + "-" + split_question_value+ ".odt");
			exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
			exporter.exportReport();
		} else if(output_format.equals("html")) {
			JasperExportManager.exportReportToHtmlFile(print, output_file_name +"-"+split_question_value+ ".html");
		} else if(output_format.equals("xml")) {
			JasperExportManager.exportReportToXmlFile(print, output_file_name +"_"+split_question_value+ ".xml", false);
		} else if(output_format.equals("xls")) {
			net.sf.jasperreports.engine.export.JRXlsExporter exporter = new net.sf.jasperreports.engine.export.JRXlsExporter();
			exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name +"_"+split_question_value+".xls");
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

