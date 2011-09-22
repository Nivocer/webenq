package it.bisi.report.jasper;

import it.bisi.Utils;


import java.io.InputStream;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
//import java.sql.ResultSetMetaData;
import java.sql.Statement;
import java.text.NumberFormat;

import java.util.HashMap;
import java.util.Map;
import java.util.Locale;
import java.util.ResourceBundle;


import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperFillManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.view.JasperViewer;
import net.sf.jasperreports.engine.JasperExportManager;
import net.sf.jasperreports.engine.export.*;


import org.apache.commons.lang.StringEscapeUtils;

import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.transform.Source;
import javax.xml.transform.dom.DOMSource;
import javax.xml.xpath.*;

import org.w3c.dom.Document;
import org.w3c.dom.NodeList;
import org.w3c.dom.Node;
import org.w3c.dom.Element;

import java.io.File;


/**
 * @todo better use of functions
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
			String reportId= args[0];
			String outputDir=args[1];
			//runReport(reportId, outputDir);
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
			String xformLocation="/home/jaapandre/workspace/webenq4/java/src/webenqResources/org/webenq/resources/3-hva-oo-simpleQuest.xml";
			String dataLocation="/home/jaapandre/workspace/webenq4/java/src/webenqResources/org/webenq/resources/5-hva-oo-simpleQuestCombined.xml";
			String reportDefinitionLocation="/home/jaapandre/workspace/webenq4/java/src/webenqResources/org/webenq/resources/simpleQuest.jrxml";
			
			Connection conn = connectDB(databaseName, userName, password);
			InputStream inputStream = Utils.class.getResourceAsStream(reportDefinitionLocation);
			Map<String,Object> prms = new HashMap<String,Object>();
			prms.put("OUTPUT_DIR", output_dir);
			
			//@todo dit moeten we nog aanpassen aan nieuw datamodel versie in webenq_4_5 #4986
		      	String split_question_id=null;
				String output_file_name=null;
				String output_format=null;
				String language=null;
				String customer=null;
				
			PreparedStatement stmt_getReportDefinition = null;
			String getReportDefinitionQuery="select * from report_definitions where id=?";
		    try {
			      //con.setAutoCommit(false);
			      stmt_getReportDefinition = conn.prepareStatement(getReportDefinitionQuery);
			      stmt_getReportDefinition.setString(1, report_identifier);
			      stmt_getReportDefinition.execute();
			      ResultSet rs_getReportDefinition = stmt_getReportDefinition.getResultSet();
			      rs_getReportDefinition.next();
			      split_question_id=rs_getReportDefinition.getString("split_question_id");
			      customer = rs_getReportDefinition.getString("customer");
			      language = rs_getReportDefinition.getString("language");
			      output_file_name=output_dir + '/' + rs_getReportDefinition.getString("output_filename");
			      output_format=rs_getReportDefinition.getString("output_format");
			      			
			      prms.put("REPORT_IDENTIFIER", report_identifier);//only needed for hacks in jrxml....
			      prms.put("CUSTOMER", customer); //resource bundle and needed for hacks in jrxml
			      prms.put("SPLIT_QUESTION_ID", split_question_id ); //not yet implemented
	      
		    }catch(Exception ex) {
				String connectMsg = "Could not report definition information: " + ex.getMessage();
		        System.err.println(connectMsg); 
				//ex.printStackTrace();
			} finally {
		      stmt_getReportDefinition.close();
		      //conn.setAutoCommit(true);
		    }
			Locale locale = new Locale("nl", "NL");
			if (language.equals("nl")){
				locale = new Locale("nl", "NL");
			}else {
				locale = new Locale("en", "US");
			}
			prms.put(JRParameter.REPORT_LOCALE, locale);
			
			//@TODO nicer handling missing files
			// eg http://jasperforge.org/plugins/espforum/view.php?group_id=102&forumid=103&topicid=65623
			if (customer.equals("fraijlemaborg")) {
				ResourceBundle myresources = ResourceBundle.getBundle("it.bisi.resources.fraijlemaborg",locale);
				prms.put(JRParameter.REPORT_RESOURCE_BUNDLE, myresources);
			}else {
				ResourceBundle myresources = ResourceBundle.getBundle("it.bisi.resources.default",locale);
				prms.put(JRParameter.REPORT_RESOURCE_BUNDLE, myresources);
			}

			/* get key/value pairs for current language/customer-combination */
			// some values are overriden in resource bundle (eg fmb barchart introduction text)
			//default texts to resource bundles, report text in jrxml.
			
			//read xform info and do something with it 
			// @TODO determine what to do with it
			//read file and put it in dom-object
			File fXmlFile = new File(xformLocation);
			DocumentBuilderFactory dbFactory = DocumentBuilderFactory.newInstance();
			DocumentBuilder dBuilder = dbFactory.newDocumentBuilder();
			Document doc = dBuilder.parse(fXmlFile);
			// do something with it
			XPathFactory factory = XPathFactory.newInstance();
		    XPath xpath = factory.newXPath();
		    XPathExpression expr 
		    //= xpath.compile("/html/body/*");
		    = xpath.compile("/html/body/*[ref='*/HVA-oo-simpleQuest/g6-Rapportcijfer']");
		    					//				/HVA-oo-simpleQuest/g6-Rapportcijfer

		    Object result = expr.evaluate(doc, XPathConstants.NODESET);
		    NodeList nodes = (NodeList) result;
		    for (int i = 0; i < nodes.getLength(); i++) {
		        System.out.println(nodes.item(i).getNodeName()); 
		    }
			
											 
			//looping through possible split by values (multiple reports for subset of respondents)
			/* split question_id is from report_definition*/
			/*
			 * @todo need to change this for new datamodel
			 */
			if (split_question_id !=null && split_question_id.length()>0 ) {
				String temp="temp";
				//get split_values
				//uit xpath.
//				Statement stmt_rows_values=conn.createStatement();
//				stmt_rows_values.execute("select distinct "+split_question_id+" as split_values FROM values_"+identifier);
//				ResultSet rs_rows_values = stmt_rows_values.getResultSet();
//				while (rs_rows_values.next()) {
//					String split_value=rs_rows_values.getString("split_values");
//					//needed for displaying content
//					prms.put("SPLIT_VALUE", split_value);
//					
//					JasperPrint print = JasperFillManager.fillReport(inputStream, prms, conn);
//					
//					// first step better path handling: cleaning of split_value (is data input).
//					String split_value_clean=split_value.replace("/","_");
//					split_value_clean=split_value_clean.replace(" ","_");
//					split_value_clean=split_value_clean.replace(",","");
//					split_value_clean=split_value_clean.replace("*","");
//					split_value_clean=split_value_clean.replace("'","_");
//					split_value_clean=split_value_clean.toLowerCase();
//					split_value_clean=StringEscapeUtils.escapeJava(split_value_clean);
//				
//					// Create output in directory public/reports  
//					if(output_format.equals("pdf")) {
//						//JasperExportManager.exportReportToPdfFile(print, output_file_name + "-" + split_value_clean + ".pdf");
//						//JasperExportManager.exportReportToPdfFile(print, output_file_name + ".pdf");
//						net.sf.jasperreports.engine.export.JRPdfExporter exporter = new net.sf.jasperreports.engine.export.JRPdfExporter(); 
//						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name+ "-" + split_value_clean + ".pdf");
//						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
//						exporter.setParameter(JRPdfExporterParameter.FORCE_LINEBREAK_POLICY, Boolean.TRUE);
//						exporter.exportReport();
//					} else if(output_format.equals("odt")) {
//						net.sf.jasperreports.engine.export.oasis.JROdtExporter exporter = new net.sf.jasperreports.engine.export.oasis.JROdtExporter();
//						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name + "-" + split_value_clean+ ".odt");
//						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
//						exporter.exportReport();
//					} else if(output_format.equals("html")) {
//						JasperExportManager.exportReportToHtmlFile(print, output_file_name +"-"+split_value_clean+ ".html");
//					} else if(output_format.equals("xml")) {
//						JasperExportManager.exportReportToXmlFile(print, output_file_name +"_"+split_value_clean+ ".xml", false);
//					} else if(output_format.equals("xls")) {
//						net.sf.jasperreports.engine.export.JRXlsExporter exporter = new net.sf.jasperreports.engine.export.JRXlsExporter();
//						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name +"_"+split_value_clean+".xls");
//						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
//						exporter.exportReport();
//					} else { 
//						JasperViewer.viewReport(print);
//					}
			}else{
				//no split value
				prms.put("SPLIT_VALUE", "");
							
				JasperPrint print = JasperFillManager.fillReport(inputStream, prms, conn);
				/* Create output in directory public/reports */
				
				if(output_format.equals("pdf")) {
					//JasperExportManager.exportReportToPdfFile(print, output_file_name + ".pdf");
					net.sf.jasperreports.engine.export.JRPdfExporter exporter = new net.sf.jasperreports.engine.export.JRPdfExporter(); 
					exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name + ".pdf");
					exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
					exporter.setParameter(JRPdfExporterParameter.FORCE_LINEBREAK_POLICY, Boolean.TRUE);
					exporter.exportReport();
				} else if(output_format.equals("odt")) {
					net.sf.jasperreports.engine.export.oasis.JROdtExporter exporter = new net.sf.jasperreports.engine.export.oasis.JROdtExporter();
					exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name + ".odt");
					exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
					exporter.exportReport();
				} else if(output_format.equals("html")) {
					JasperExportManager.exportReportToHtmlFile(print, output_file_name + ".html");
				} else if(output_format.equals("xml")) {
					JasperExportManager.exportReportToXmlFile(print, output_file_name + ".xml", false);
				} else if(output_format.equals("xls")) {
					net.sf.jasperreports.engine.export.JRXlsExporter exporter = new net.sf.jasperreports.engine.export.JRXlsExporter();
					exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name + ".xls");
					exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
					exporter.exportReport();
					
				} else { 
					JasperViewer.viewReport(print);
				}
			}
		}
		
		catch(Exception ex) {
			@SuppressWarnings("unused")
			String connectMsg = "Could not create the report " + ex.getMessage() + " " + ex.getLocalizedMessage();
			ex.printStackTrace();
		}
	}
}
