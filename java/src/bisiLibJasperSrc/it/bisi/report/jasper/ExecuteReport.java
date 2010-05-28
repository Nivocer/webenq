package it.bisi.report.jasper;

import it.bisi.Utils;

import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSetMetaData;
import java.sql.Statement;
import java.util.HashMap;
import java.util.Map;

import net.sf.jasperreports.engine.JasperFillManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.view.JasperViewer;

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
	
	public static void runReport(String databaseName, String userName, String password,String identifier) {
	      try{
	         Connection conn = connectDB(databaseName, userName, password);
	         InputStream inputStream = Utils.class.getResourceAsStream("/it/bisi/resources/report1.jasper");
	         Map prms = new HashMap();
	         prms.put("IDENTIFIER", identifier);
	         //minus group...
	       //try to find out the group on rows
				Statement stmt_rows=conn.createStatement();
				stmt_rows.execute("select * from values_"+identifier+" where 1=0");
				ResultSetMetaData rsmd =stmt_rows.getResultSet().getMetaData();
				String group_rows=rsmd.getColumnName(4);
				
				stmt_rows.close();
	         //
	         
	         String query="SELECT a.value title,b.value srtdt, " +
	         		"c.value enddt,d.value response,e.value percentage,type,group_id  " +
	         		"FROM info_"+identifier+" a,info_"+identifier+" b," +
	         				"info_"+identifier+" c, info_"+identifier+" d," +
	         				"info_"+identifier+" e," +
	         				"(SELECT distinct q.group_id,case when instr(type,'Closed_Percentage')=0 then " +
	         				"case when instr(type,'Closed_Scale')=0 then 'N' else 'AVG' end " +
	         				"else 'PERC' end type FROM questions_"+identifier+" q,meta_"+identifier+" m " +
	         				"where m.question_id=q.id and parent_id=0 and q.id!='"+group_rows+"' and " +
	         				"(type like '%Closed_Percentage%' or type like '%Closed_Scale%')) t "+
	         		"where a.id='Titel vragenlijst' " +
	         		"and b.id='Startdatum' " +
	         		"and c.id='Einddatum' " +
	         		"and d.id='unieke respondenten' " +
	         		"and e.id='Respons percentage' ";
	         
	         prms.put("QUERY", query);
	         JasperPrint print = JasperFillManager.fillReport(inputStream, prms, conn);
	         JasperViewer.viewReport(print);
	      }catch(Exception ex) {
	         String connectMsg = "Could not create the report " + ex.getMessage() + " " + ex.getLocalizedMessage();
	         
	         ex.printStackTrace();
	      }
	   }

}
