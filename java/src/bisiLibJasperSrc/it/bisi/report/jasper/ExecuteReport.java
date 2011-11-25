package it.bisi.report.jasper;
//import net.sf.saxon.om.NamespaceConstant;  

import it.bisi.Utils;

import java.io.InputStream;

import java.sql.Connection;
import java.sql.DriverManager;
//import java.sql.PreparedStatement;
//import java.sql.ResultSet;
//import java.sql.ResultSetMetaData;
//import java.sql.Statement;
//import java.text.NumberFormat;

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

import org.apache.commons.lang.StringEscapeUtils;
import org.xml.sax.InputSource;

import net.sf.jasperreports.engine.JREmptyDataSource;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperCompileManager;
import net.sf.jasperreports.engine.JasperFillManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.JasperReport;
import net.sf.jasperreports.view.JasperViewer;
import net.sf.jasperreports.engine.JasperExportManager;
import net.sf.saxon.lib.NamespaceConstant;
import net.sf.saxon.om.NodeInfo;
import net.sf.saxon.xpath.XPathEvaluator;


/**
 * @todo better use of methods
 * @author jaapandre
 *
 */

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
			runReport(args[0],args[1],args[2],args[3],args[4]);
	}
	
	/** 
	 *
	 * @param databaseName
	 * @param userName
	 * @param password
	 * @return
	 */
	public static Connection connectDB(String databaseName, String userName, String password) {
		Connection jdbcConnection = null;
		try{
			Class.forName("com.mysql.jdbc.Driver");
			//String host="jdbc:mysql://"+databaseName;
			//System.out.println("host="+host);
			jdbcConnection = DriverManager.getConnection("jdbc:mysql://"+databaseName,userName,password);
		}catch(Exception ex) {
			@SuppressWarnings("unused")
			String connectMsg = "Could not connect to the database: " + ex.getMessage() + " " + ex.getLocalizedMessage();
	        System.err.println("connectMsg"); 
			ex.printStackTrace();
		}
		return jdbcConnection;
	}
	
	public static void runReport(String databaseName, String userName, String password, String report_identifier, String output_dir)
	{
		try{
			//hack location of files, needs to be interactive.
			String xformLocation="src/webenqResources/org/webenq/resources/3-hva-oo-simpleQuest.xml";
			String dataLocation="src/webenqResources/org/webenq/resources/5-hva-oo-simpleQuestCombined.xml";
			//String reportDefinitionLocation="/org/webenq/resources/simpleQuestBarchart.jasper";
			//more hardcoded config below, line 129 at this moment
	System.out.println(fileName("asdf/jkli!#%&*%a",true));				
			//String reportDefinitionLocation="/org/webenq/resources/simpleQuest.jasper";
			String reportDefinitionLocation="/org/webenq/resources/simpleQuest.jrxml";
			System.out.println(reportDefinitionLocation);
			//Connection conn = connectDB(databaseName, userName, password);
			Map<String,Object> prms = new HashMap<String,Object>();
			prms.put("OUTPUT_DIR", output_dir);
			prms.put("DATA_LOCATION", dataLocation);
			prms.put("XFORM_LOCATION", xformLocation);
		      	String split_question_id=null;
				String output_file_name=null;
				String output_format=null;
				String language=null;
				String customer=null;
				
			//PreparedStatement stmt_getReportDefinition = null;
			//String getReportDefinitionQuery="select * from report_definitions where id=?";
		    try {
			      //con.setAutoCommit(false);
//			      stmt_getReportDefinition = conn.prepareStatement(getReportDefinitionQuery);
//			      stmt_getReportDefinition.setString(1, report_identifier);
//			      stmt_getReportDefinition.execute();
//			      ResultSet rs_getReportDefinition = stmt_getReportDefinition.getResultSet();
//			      rs_getReportDefinition.next();
//			      split_question_id=rs_getReportDefinition.getString("split_question_id");
//			      customer = rs_getReportDefinition.getString("customer");
//			      language = rs_getReportDefinition.getString("language");
//			      output_file_name=output_dir + '/' + rs_getReportDefinition.getString("output_filename");
//			      output_format=rs_getReportDefinition.getString("output_format");
//	
		    	  //hack development, needs to get this information from somewhere
		    	  split_question_id="g3-Rapportcijfer";
			      customer="leeuwenburg";
			      language="nl";
			      output_file_name="test";
			      output_file_name=output_dir + '/' +fileName(output_file_name, false);
			      output_format="pdf";
			      
			      prms.put("REPORT_IDENTIFIER", report_identifier);//only needed for hacks in jrxml....
			      prms.put("CUSTOMER", customer); //resource bundle and needed for hacks in jrxml
			      prms.put("SPLIT_QUESTION_ID", split_question_id ); //not yet implemented #5395
			      HashMap<String,Map<String,Double>> color_range_map=it.bisi.Utils.getColorRangeMaps(customer);
			      prms.put("COLOR_RANGE_MAP",color_range_map);
			      		      		        
			      
		    }catch(Exception ex) {
				String connectMsg = "Could not report definition information: " + ex.getMessage();
		        System.err.println(connectMsg); 
				//ex.printStackTrace();
			} finally {
//		      stmt_getReportDefinition.close();
		      //conn.setAutoCommit(true);
		    }
			Locale locale = new Locale("nl", "NL");
			if (language.equals("nl")){
				locale = new Locale("nl", "NL");
			}else {
				locale = new Locale("en", "US");
			}
			prms.put(JRParameter.REPORT_LOCALE, locale);
			Locale.setDefault(locale);
			//@TODO nicer handling missing files
			// eg http://jasperforge.org/plugins/espforum/view.php?group_id=102&forumid=103&topicid=65623
			if (customer.equals("fraijlemaborg")) {
				ResourceBundle myresources = ResourceBundle.getBundle("org.webenq.resources.fraijlemaborg",locale);
				prms.put(JRParameter.REPORT_RESOURCE_BUNDLE, myresources);
			}else {
				ResourceBundle myresources = ResourceBundle.getBundle("org.webenq.resources.default",locale);
				prms.put(JRParameter.REPORT_RESOURCE_BUNDLE, myresources);
			}

			/* get key/value pairs for current language/customer-combination */
			// some values are overriden in resource bundle (eg fmb barchart introduction text)
			//default texts to resource bundles, report text in jrxml.
			
								 
			//looping through available split by values (seperate reports for subsets of respondents)
			if (split_question_id !=null && split_question_id.length()>0 ) {
				//get distinct split_values
				// use xpath2 (saxon)
				String searchSplitValues="distinct-values(//respondenten/respondent/*/"+split_question_id+")";

				XPathFactory factory = XPathFactory.newInstance(NamespaceConstant.OBJECT_MODEL_SAXON);
		        XPath xpath = factory.newXPath();
		        InputSource is = new InputSource(dataLocation);
		        SAXSource ss = new SAXSource(is);
		        NodeInfo doc = ((XPathEvaluator)xpath).setSource(ss);
				XPathExpression expr = xpath.compile(searchSplitValues);
				Object result=expr.evaluate(doc, XPathConstants.NODESET);

				List splitValuesList = (List) result;
				String split_question_value;
				for (Iterator iter = splitValuesList.iterator(); iter.hasNext();) {
					split_question_value = (String) iter.next();
					//needed for displaying content
					prms.put("SPLIT_QUESTION_VALUE", split_question_value);
					
					generateReport(reportDefinitionLocation, prms, split_question_value, output_file_name, output_format );
					
				}
			}else{
				//no split value
				prms.put("SPLIT_QUESTION_VALUE", "");
				generateReport(reportDefinitionLocation, prms, "", output_file_name, output_format );
			}
		}
		
		catch(Exception ex) {
			@SuppressWarnings("unused")
			String connectMsg = "Could not create the report " + ex.getMessage() + " " + ex.getLocalizedMessage();
			ex.printStackTrace();
		}
	}



	static void generateReport(String reportDefinitionLocation, Map<String,Object> prms, String split_question_value, String output_file_name, String output_format ) throws Exception{
		//clean fileName
		if (split_question_value.length()>0){
			// no slash in split part
			split_question_value=fileName(split_question_value, false);
			output_file_name=fileName(output_file_name+"-"+split_question_value, true);
		}else{
			output_file_name=fileName(output_file_name, true);
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
		//TODO 
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
}
