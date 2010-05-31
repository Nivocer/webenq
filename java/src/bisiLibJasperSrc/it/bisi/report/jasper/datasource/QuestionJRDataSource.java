package it.bisi.report.jasper.datasource;

import it.bisi.report.jasper.datasource.object.Record;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.Collection;

import net.sf.jasperreports.engine.JRDataSource;
import net.sf.jasperreports.engine.data.JRBeanCollectionDataSource;

public class QuestionJRDataSource {
	private Connection conn;
	private String report_identifier;
	private String group;
	private String type;
	public QuestionJRDataSource(Connection conn,String report_identifier,String group,String type){
		this.conn=conn;
		this.report_identifier=report_identifier;
		this.group=group;
		this.type=type;
	}
	public JRDataSource getRecords(){
		JRBeanCollectionDataSource dataSource;
		Collection<Record> reportRows=new ArrayList<Record>();
		
		try{
			
			Statement stmt_rows=conn.createStatement();
			stmt_rows.execute("select * from report_definitions where id='"+report_identifier+"'");

			ResultSet rs_repdef =stmt_rows.getResultSet();
			rs_repdef.next();
			String identifier=rs_repdef.getString("data_set_id");
			String group_rows=rs_repdef.getString("group_question_id");
			String output_file_name=rs_repdef.getString("output_filename");
			String output_format=rs_repdef.getString("output_format");
			String report_type=rs_repdef.getString("report_type");
						
			
			//find out the title of the group rows
			Statement stmt_titlerows=conn.createStatement();
			stmt_titlerows.execute("select title from questions_"+identifier+" where id='"+group_rows+"'");
			ResultSet rs_titlerows=stmt_titlerows.getResultSet();
			rs_titlerows.next();
			String titlerows=rs_titlerows.getString(1);
			rs_titlerows.close();
			stmt_titlerows.close();
			//find out the question..
			Statement stmt_questions = conn.createStatement();
			stmt_questions.execute("select q.id,q.title from questions_"+identifier+" q where group_id='"+group+"'");
			ResultSet rsh_questions=stmt_questions.getResultSet();
			String group_question_title="";
			if ("AVG".equals(type)){
				Statement stmt_title=conn.createStatement();
				stmt_title.execute("select g.title from groups_"+identifier+" g where g.id='"+group+"'");
				ResultSet rsh_title=stmt_title.getResultSet();
				rsh_title.next();
				try{
					group_question_title=rsh_title.getString(1);
				}catch(Exception ex){
					//nop
				}
				rsh_title.close();
				stmt_title.close();
			}
			
			while (rsh_questions.next()){
				String question_field=rsh_questions.getString(1);
				String question_title=rsh_questions.getString(2);
				
				//if perc crosstab....
				if ("PERC".equals(type)){
					
					Statement stmt_valuep=conn.createStatement();
					stmt_valuep.execute("select "+question_field+","+group_rows+" from values_"+identifier);
					ResultSet rsh_valuep=stmt_valuep.getResultSet();
					while (rsh_valuep.next()){
						Record rp=new Record(question_title,titlerows,rsh_valuep.getString(1),rsh_valuep.getString(2),"1");
						reportRows.add(rp);
					}
					rsh_valuep.close();
					stmt_valuep.close();
					
				}else{
					Statement stmt_valuea=conn.createStatement();
//					System.out.println("Query:"+"select "+question_field+","+group_rows+" from values_"+identifier+" where "+question_field+">0");
					stmt_valuea.execute("select "+question_field+","+group_rows+" from values_"+identifier+" where "+question_field+">0");
					ResultSet rsh_valuea=stmt_valuea.getResultSet();
					while (rsh_valuea.next()){
						Record ra=new Record(group_question_title,titlerows,question_title,rsh_valuea.getString(2),rsh_valuea.getString(1));
						reportRows.add(ra);
					}
					rsh_valuea.close();
					stmt_valuea.close();
					
				}
			
		}
		rsh_questions.close();
		stmt_questions.close();
		}catch(Exception ex){
			ex.printStackTrace();
		}
		dataSource = new JRBeanCollectionDataSource(reportRows);
		return dataSource;
	}

}
