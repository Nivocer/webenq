package it.bisi.report.jasper;

import it.bisi.Utils;

import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.sql.Statement;

import java.util.HashMap;
import java.util.Map;

import net.sf.jasperreports.engine.JasperFillManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.view.JasperViewer;
import net.sf.jasperreports.engine.JasperExportManager;
import net.sf.jasperreports.engine.export.*;


public class ExecuteReport {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub
		runReport(args[0],args[1],args[2],args[3]);
	}
	
	public static Connection connectDB(String databaseName, String userName, String password) {
		Connection jdbcConnection = null;
		try{
			Class.forName("com.mysql.jdbc.Driver");
			String host="jdbc:mysql://"+databaseName;
			//System.out.println("host="+host);
			jdbcConnection = DriverManager.getConnection("jdbc:mysql://"+databaseName,userName,password);
		}catch(Exception ex) {
			String connectMsg = "Could not connect to the database: " + ex.getMessage() + " " + ex.getLocalizedMessage();
	         
			ex.printStackTrace();
		}
		return jdbcConnection;
	}
	
	public static void runReport(String databaseName, String userName, String password, String report_identifier)
	{
		try {
			Connection conn = connectDB(databaseName, userName, password);
			InputStream inputStream = Utils.class.getResourceAsStream("/it/bisi/resources/report1.jasper");
			Map prms = new HashMap();
	        //minus group...
			//find out the group on rows and other report options
			Statement stmt_rows=conn.createStatement();
			stmt_rows.execute("select * from report_definitions where id='"+report_identifier+"'");

			ResultSet rs_repdef = stmt_rows.getResultSet();
			rs_repdef.next();
			String identifier=rs_repdef.getString("data_set_id");
			String group_rows=rs_repdef.getString("group_question_id");
			String split_by_question_id=rs_repdef.getString("split_by_question_id");
			String output_file_name=rs_repdef.getString("output_filename");
			String output_format=rs_repdef.getString("output_format");
			String report_type=rs_repdef.getString("report_type");
			
			//todo check if jrxml need split_by_question_id (don't think so)
			prms.put("GROUP_ROWS", group_rows);
			prms.put("REPORT_IDENTIFIER", report_identifier);
			prms.put("REPORT_TYPE", report_type);
			stmt_rows.close();
			//
			//returns result:
			//title 	srtdt 	enddt 	response 	percentage 	type 	group_id
			//MER jaar 2 - Vt blok 3 0910 	40276 	40306 	15 	0.1239669421487603 	AVG 	1
			//MER jaar 2 - Vt blok 3 0910 	40276 	40306 	15 	0.1239669421487603 	AVG 	3
			//MER jaar 2 - Vt blok 3 0910 	40276 	40306 	15 	0.1239669421487603 	AVG 	5
			// where group_id=theme_id
			// and type is type of crosstab needed.
			String query="SELECT a.value title,b.value srtdt, " +
         		"c.value enddt,d.value response,e.value percentage,type,group_id  " +
         		"FROM info_"+identifier+" a,info_"+identifier+" b," +
         				"info_"+identifier+" c, info_"+identifier+" d," +
         				"info_"+identifier+" e," +
         				"(SELECT distinct q.group_id," +
         				"	case type " +
         				"		when 'HVA_Model_Data_Question_Closed_Scale_Four' then 'AVG'" +	         				
         				"		when 'HVA_Model_Data_Question_Closed_Scale_Five' then 'AVG'" +
         				"		when 'HVA_Model_Data_Question_Closed_Scale_Six' then 'AVG'" +
         				"		when 'HVA_Model_Data_Question_Closed_Scale_Seven' then 'AVG'" +
         				"		when 'HVA_Model_Data_Question_Closed_Scale_Eight' then 'AVG'" +
         				"		when 'HVA_Model_Data_Question_Closed_Scale_Nine' then 'AVG'" +
         				"		when 'HVA_Model_Data_Question_Closed_Scale_Ten' then 'AVG'" +
         				"		when 'HVA_Model_Data_Question_Closed_Percentage' then 'PERC'" +
         				"       when 'HVA_Model_Data_Question_Open_Text' then 'OPEN'" +
         				"		when 'HVA_Model_Data_Question_Open_Email' then 'SKIP'" +
         				"		when 'HVA_Model_Data_Question_Open' then 'SKIP' " +
         				"		ELSE 'NOTYETIMPLEMENTED'" +
         				"	end type " +
         				"FROM questions_"+identifier+" q,meta_"+identifier+" m "+ 
         				"where m.question_id=q.id and parent_id=0 and q.id!='"+group_rows+"' and q.id!='"+split_by_question_id+"' and " +
         				"(type like '%Closed_Percentage%' or type like '%Closed_Scale%' or type like '%open%')) t "+
         		"where a.id='Titel vragenlijst' " +
         		"and b.id='Startdatum' " +
         		"and c.id='Einddatum' " +
         		"and d.id='unieke respondenten' " +
         		"and e.id='Respons percentage' ";
			prms.put("QUERY", query);
			
			//looping through possible split by values (multiple reports for subset of respondents)
			if (split_by_question_id.length()>0 ) {
				//get split_by_values
				Statement stmt_rows_values=conn.createStatement();
				stmt_rows_values.execute("select distinct "+split_by_question_id+" as split_by_values FROM values_"+identifier);
				ResultSet rs_rows_values = stmt_rows_values.getResultSet();
				while (rs_rows_values.next()) {
					String split_by_value=rs_rows_values.getString("split_by_values");
					//needed for displaying content
					prms.put("SPLIT_BY_VALUE", split_by_value);
					
					inputStream = Utils.class.getResourceAsStream("/it/bisi/resources/report1.jasper");
					JasperPrint print = JasperFillManager.fillReport(inputStream, prms, conn);

					// Create output in directory public/reports  
					if(output_format.equals("pdf")) {
						JasperExportManager.exportReportToPdfFile(print, "../public/reports/" + output_file_name + "_" + split_by_value + ".pdf");
					} else if(output_format.equals("odt")) {
						net.sf.jasperreports.engine.export.oasis.JROdtExporter exporter = new net.sf.jasperreports.engine.export.oasis.JROdtExporter();
						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, "../public/reports/" + output_file_name + "_" + split_by_value+ ".odt");
						exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
						exporter.exportReport();
					} else if(output_format.equals("html")) {
						JasperExportManager.exportReportToHtmlFile(print, "../public/reports/" + output_file_name +"_"+split_by_value+ ".html");
					} else if(output_format.equals("xml")) {
						JasperExportManager.exportReportToXmlFile(print, "../public/reports/" + output_file_name +"_"+split_by_value+ ".xml", false);
					} else { 
						JasperViewer.viewReport(print);
					}
				}
			}else{
				JasperPrint print = JasperFillManager.fillReport(inputStream, prms, conn);
				/* Create output in directory public/reports */ 
				if(output_format.equals("pdf")) {
					JasperExportManager.exportReportToPdfFile(print, "../public/reports/" + output_file_name + ".pdf");
				} else if(output_format.equals("odt")) {
					net.sf.jasperreports.engine.export.oasis.JROdtExporter exporter = new net.sf.jasperreports.engine.export.oasis.JROdtExporter();
					exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.OUTPUT_FILE_NAME, "../public/reports/" + output_file_name + ".odt");
					exporter.setParameter(net.sf.jasperreports.engine.JRExporterParameter.JASPER_PRINT, print);
					exporter.exportReport();
				} else if(output_format.equals("html")) {
					JasperExportManager.exportReportToHtmlFile(print, "../public/reports/" + output_file_name + ".html");
				} else if(output_format.equals("xml")) {
					JasperExportManager.exportReportToXmlFile(print, "../public/reports/" + output_file_name + ".xml", false);
				} else { 
					JasperViewer.viewReport(print);
				}
			}
		}
		
		catch(Exception ex) {
			String connectMsg = "Could not create the report " + ex.getMessage() + " " + ex.getLocalizedMessage();
			ex.printStackTrace();
		}
	}
}
