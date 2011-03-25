package it.bisi.report.jasper;

import it.bisi.Utils;

import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.Statement;


import java.util.HashMap;
import java.util.Map;

import net.sf.jasperreports.engine.JasperFillManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.view.JasperViewer;
import net.sf.jasperreports.engine.JasperExportManager;
import net.sf.jasperreports.engine.export.*;

import org.apache.commons.lang.StringEscapeUtils;

public class ExecuteIntroduction {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub
		runReport(args[0],args[1],args[2],args[3],args[4], args[5]);
	}
	
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
	         
			ex.printStackTrace();
		}
		return jdbcConnection;
	}
	
	public static void runReport(String databaseName, String userName, String password, String period_identifier, String report_type, String output_dir)
	{
		try {
			Connection conn = connectDB(databaseName, userName, password);
			InputStream inputStream = Utils.class.getResourceAsStream("/it/bisi/resources/report-introduction.jasper");
			Map<String,Object> prms = new HashMap<String,Object>();
			prms.put("OUTPUT_DIR", output_dir);
			
			//hardcoded....
			//some defaults
			String page_orientation = "portrait"; 
			String output_format="pdf";
			//String output_format="odt";
			
			//language
			String language;
			if (period_identifier.equals("3") || period_identifier.equals("5") || period_identifier.equals("7") || period_identifier.equals("15")){
					language="en";
			}else{
				language = "nl";
			}
			
			//customer
			String customer = "fraijlemaborg";
			if (period_identifier.equals("8") || period_identifier.equals("9")) {
				customer ="hvaoo";
			}
			if (period_identifier.equals("10")) {
				customer="hvaoo";
			}
			if (period_identifier.equals("11")) {
				customer="hvaoo";
			}
			
			//output file name adjustments
			String output_file_name=output_dir + "/introduction_" + report_type;			
			if (period_identifier.equals("5") || period_identifier.equals("4")){
				output_file_name+="2";
			}
			if (period_identifier.equals("6") || period_identifier.equals("7") || period_identifier.equals("13") || period_identifier.equals("15") ){
				output_file_name+="_"+language;
			}
			//end hardcoded config
			
			prms.put("REPORT_TYPE", report_type);
			prms.put("CUSTOMER", customer);
			prms.put("PERIOD_IDENTIFIER",period_identifier);
			prms.put("LANGUAGE", language);
			
				
			/* get key/value pairs for current language/customer-combination */
			String key = "";
			String val = "";
			Map<String,String> texts = new HashMap<String,String>();
			Statement stmt_texts = conn.createStatement();
			stmt_texts.execute("SELECT `key`, `value` FROM `text` WHERE `language` = '" + language + "' AND `customer` = '" + customer + "';");
			ResultSet rs_keyValPairs = stmt_texts.getResultSet();
			while (rs_keyValPairs.next()) {
				key = rs_keyValPairs.getString("key");
				val = rs_keyValPairs.getString("value");
				texts.put(key, val);
			}
			stmt_texts.close();
			prms.put("TEXTS", texts);
			
			//updaten reponse
			//get all rows for this period_identifier/report_identifier
			
			/*
			 * @todo need to change this for new datamodel
			 */
			String update_query1="SELECT * from population where period_id="+period_identifier;
			Statement stmt_update1 = conn.createStatement();
			stmt_update1.execute(update_query1);
			ResultSet rs_update1 = stmt_update1.getResultSet();
						
			Statement stmt_update2 = conn.createStatement();
				while (rs_update1.next()) {
				String dataset_id=rs_update1.getString("dataset_id");
				String split_question_id=rs_update1.getString("split_question_id");
				String split_value=rs_update1.getString("split_value");
				String update_query2;
				/*
				 * @todo need to change this for new datamodel
				 * @todo check next if statement
				 */
				if (!split_question_id.equals(null) && !split_question_id.equals("")){
					update_query2=update_query2="UPDATE population set response=" +
					"(select count(*) from values_"+dataset_id+" where "+split_question_id+"='"+split_value+"')" +
					" where period_id="+period_identifier +
					" and dataset_id="+dataset_id+
					" and split_question_id='"+split_question_id+"'" +
					" and split_value='"+split_value+"'";
					
					//System.out.println(update_query2);
				} else {
					update_query2="UPDATE population set response=" +
					"(select count(*) from values_"+dataset_id+") " +
						" where period_id="+period_identifier +
						" and dataset_id="+dataset_id+
						" and (split_question_id=null or split_question_id='')" +
						" and (split_value=null or split_value='')";
				}
				stmt_update2.execute(update_query2);
			}
			stmt_update2.close();
			
			//looping through possible split by values (multiple reports for subset of respondents)
			//get split_values
			/*
			 * @todo need to change this for new datamodel
			 */
			Statement stmt_rows_values=conn.createStatement();
			stmt_rows_values.execute("select distinct split_value as split_values FROM population where period_id="+period_identifier);
			ResultSet rs_rows_values = stmt_rows_values.getResultSet();
			rs_rows_values.last();
			int split_row_count = rs_rows_values.getRow();
			rs_rows_values.beforeFirst();
			
			
			if (split_row_count>0 ) {
				//we have at least one row.... but for now we want to use this one, the no split value gives an error...
				/*
				 * @todo need to change this for new datamodel
				 */
				while (rs_rows_values.next()) {
					String split_value=rs_rows_values.getString("split_values");
					//needed for displaying content
					prms.put("SPLIT_VALUE", split_value);
					String query="select * from population" +
						" where period_id="+period_identifier +
						" and split_value='"+split_value+"'"+
						" order by dataset_id ASC";
					prms.put("QUERY", query);
					
					if (page_orientation != null && page_orientation.equals("landscape")) {
						inputStream = Utils.class.getResourceAsStream("/it/bisi/resources/report-introductionl.jasper");
					}else{
						inputStream = Utils.class.getResourceAsStream("/it/bisi/resources/report-introduction.jasper");
					}
					
					JasperPrint print = JasperFillManager.fillReport(inputStream, prms, conn);

					// first step better path handling: cleaning of split_value (is data input).
					String split_value_clean=split_value.replace("/","_");
					split_value_clean=split_value_clean.replace(" ","_");
					split_value_clean=split_value_clean.replace(",","");
					split_value_clean=split_value_clean.replace("*","");
					split_value_clean=split_value_clean.toLowerCase();
					split_value_clean=StringEscapeUtils.escapeJava(split_value_clean);
					
					// Create output in directory public/reports  
					if(output_format.equals("pdf")) {
						//JasperExportManager.exportReportToPdfFile(print, output_file_name + "_" + split_value_clean + ".pdf");
						net.sf.jasperreports.engine.export.JRPdfExporter exporter = new net.sf.jasperreports.engine.export.JRPdfExporter(); 
						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name+ "-" + split_value_clean + ".pdf");
						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
						exporter.setParameter(JRPdfExporterParameter.FORCE_LINEBREAK_POLICY, Boolean.TRUE);
						exporter.exportReport();
					} else if(output_format.equals("odt")) {
						net.sf.jasperreports.engine.export.oasis.JROdtExporter exporter = new net.sf.jasperreports.engine.export.oasis.JROdtExporter();
						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name + "_" + split_value_clean+ ".odt");
						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
						exporter.exportReport();
					} else if(output_format.equals("html")) {
						JasperExportManager.exportReportToHtmlFile(print, output_file_name +"_"+split_value_clean+ ".html");
					} else if(output_format.equals("xml")) {
						JasperExportManager.exportReportToXmlFile(print, output_file_name +"_"+split_value_clean+ ".xml", false);
					} else { 
						JasperViewer.viewReport(print);
					}
				}
			}else{
				//no split value
				// throws error unkown 'column name title' by generation of introduction (6,7) 
				//response (not percentage, but number of respondents in this report)
				/*
				 * @todo need to change this for new datamodel
				 */
				String query="select * from population" +
					" where period_id="+period_identifier;
				prms.put("QUERY", query);
				

				if (page_orientation != null && page_orientation.equals("landscape")) {
					inputStream = Utils.class.getResourceAsStream("/it/bisi/resources/report1l.jasper");
				}else{
					inputStream = Utils.class.getResourceAsStream("/it/bisi/resources/report1.jasper");
				}	
				JasperPrint print = JasperFillManager.fillReport(inputStream, prms, conn);
				
				/* Create output in directory public/reports */
				if(output_format.equals("pdf")) {
					JasperExportManager.exportReportToPdfFile(print, output_file_name + ".pdf");
				} else if(output_format.equals("odt")) {
					net.sf.jasperreports.engine.export.oasis.JROdtExporter exporter = new net.sf.jasperreports.engine.export.oasis.JROdtExporter();
					exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, output_file_name + ".odt");
					exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
					exporter.exportReport();
				} else if(output_format.equals("html")) {
					JasperExportManager.exportReportToHtmlFile(print, output_file_name + ".html");
				} else if(output_format.equals("xml")) {
					JasperExportManager.exportReportToXmlFile(print, output_file_name + ".xml", false);
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
