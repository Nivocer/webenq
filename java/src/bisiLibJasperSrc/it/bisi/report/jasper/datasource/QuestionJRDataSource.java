package it.bisi.report.jasper.datasource;

import it.bisi.report.jasper.datasource.object.Record;
import it.bisi.BlankRemover;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.Collection;


//import java.util.HashMap;
//import java.util.Map;

import net.sf.jasperreports.engine.JRDataSource;
import net.sf.jasperreports.engine.data.JRBeanCollectionDataSource;

//import org.apache.commons.lang.StringEscapeUtils;

public class QuestionJRDataSource {
	private Connection conn;
	private String report_identifier;
	private String group;
	private String type;
	private String split_value;
	
	public QuestionJRDataSource(Connection conn, String report_identifier, String group, String type, String split_value) {
		//group=theme_id
		this.conn=conn;
		this.report_identifier=report_identifier;
		this.group=group;
		this.type=type;
		this.split_value=split_value;
	}
	
	public JRDataSource getRecords() {
		JRBeanCollectionDataSource dataSource;
		Collection<Record> reportRows=new ArrayList<Record>();
		String titlerows;
		try{
			
			//hack fraijlemaborg response
			String config_response_group="30_aantal_werkelijke_respondenten";
			String config_population_group="29_groepsgrootte";
			@SuppressWarnings("unused")
			String config_identifier="207";
			
			//@TODO adjust for new datamodel, step 2
			Statement stmt_rows=conn.createStatement();
			stmt_rows.execute("select * from report_definitions where id='"+report_identifier+"'");
			
			ResultSet rs_repdef =stmt_rows.getResultSet();
			rs_repdef.next();
			String identifier=rs_repdef.getString("data_set_id");			
			//group_rows is variable to group the data with (columns in cross tab)
			String group_rows=rs_repdef.getString("group_question_id");
			String split_question_id=rs_repdef.getString("split_question_id");
			//String output_file_name=rs_repdef.getString("output_filename");
			//String output_format=rs_repdef.getString("output_format");
			String report_type=rs_repdef.getString("report_type");
			String ignore_question_ids = rs_repdef.getString("ignore_question_ids");
			//String language = rs_repdef.getString("language");
			String customer = rs_repdef.getString("customer");
			//String page_orientation = rs_repdef.getString("page"); 
			String config_hack_identifier="";
									
			//find out the title of the group rows
			Statement stmt_titlerows=conn.createStatement();
			//group_rows may be empty, we don't have a title.
			if ( group_rows.length() != 0 ) {
				stmt_titlerows.execute("select title from questions_"+identifier+" where id='"+group_rows+"'");
				ResultSet rs_titlerows=stmt_titlerows.getResultSet();
				rs_titlerows.next();
				titlerows=BlankRemover.lrtrim(rs_titlerows.getString(1));
				rs_titlerows.close();
				stmt_titlerows.close();
			} else {
				titlerows="";
			}
			
			//determin text of the theme.
			//report2-mean.jrxml
			//report-bc.jrxml
			//report3(l)_groupinfo.jrxml
			String group_question_title="";
			if (type.equals("AVG") || type.equals("NUMBER")){
			//if (("AVG".equals(type) &&	"NUMBER".equals(type))){
				Statement stmt_title=conn.createStatement();
				stmt_title.execute("select g.title from groups_"+identifier+" g where g.id='"+group+"'");
				ResultSet rsh_title=stmt_title.getResultSet();
				rsh_title.next();
				try{
					group_question_title=BlankRemover.lrtrim(rsh_title.getString(1));
				}catch(Exception ex){
					//nop
				}
				rsh_title.close();
				stmt_title.close();
			}

			//find out the questions for a theme.
			//all.jrxml
			Statement stmt_questions = conn.createStatement();
			if (ignore_question_ids != null && ignore_question_ids.length() > 0) {
				stmt_questions.execute("select q.id, q.title from questions_"+config_hack_identifier+identifier+" q where group_id='"+group+"' " +
						" and q.id not in (" + ignore_question_ids + ") ");
			} else {
				stmt_questions.execute("select q.id,q.title from questions_"+config_hack_identifier+identifier+" q where group_id='"+group+"' ");
			}
			ResultSet rsh_questions=stmt_questions.getResultSet();

			while (rsh_questions.next()){
				String question_field=rsh_questions.getString(1); //question_id
				String question_title=BlankRemover.lrtrim(rsh_questions.getString(2)); //question_text
				//if perc crosstab....
				// report2-freq.jrxml
				if ("PERC".equals(type)){
					
					Statement stmt_valuep=conn.createStatement();
					//group_rows may be empty
					String query="";
					if ( group_rows.length() == 0 ){
						query="select "+question_field+" ,\"Totaal\" from values_"+identifier+" where "+question_field+" is not null";
					}else{
						query="select "+question_field+","+group_rows+" from values_"+identifier+" where "+question_field+" is not null ";
					}
					
					if  ((split_question_id!=null) && (split_question_id.length()>0)  ) {
						query=query+" and "+split_question_id+" like \""+split_value+"\"";
					}
						
					
					stmt_valuep.execute(query);
					ResultSet rsh_valuep=stmt_valuep.getResultSet();
					while (rsh_valuep.next()){
						//very ugly hack: sorting 
						String tempLabel=rsh_valuep.getString(1);
						if (tempLabel.toLowerCase().equals("veel te laag")){
							tempLabel="1) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("te laag"))){
							tempLabel="2) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("precies goed"))){
							tempLabel="3) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("te hoog"))){
							tempLabel="4) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("veel te hoog"))){
							tempLabel="5) "+tempLabel;
						}
						
						if (tempLabel.toLowerCase().equals("veel te weinig")){
							tempLabel="1) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("te weinig"))){
							tempLabel="2) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("te weing"))){
							tempLabel="2) te weinig";
						}else if ((tempLabel.toLowerCase().equals("precies goed"))){
							tempLabel="3) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("te veel"))){
							tempLabel="4) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("veel te veel"))){
							tempLabel="5) "+tempLabel;
						}
						
						if (tempLabel.toLowerCase().equals("veel te makkelijk")){
							tempLabel="1) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("te makkelijk"))){
							tempLabel="2) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("precies goed"))){
							tempLabel="3) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("te moeilijk"))){
							tempLabel="4) "+tempLabel;
						}else if ((tempLabel.toLowerCase().equals("veel te moeilijk"))){
							tempLabel="5) "+tempLabel;
						}
						Record rp=new Record(question_title,titlerows,question_field,tempLabel,rsh_valuep.getString(2),"1");
						reportRows.add(rp);
					}
					rsh_valuep.close();
					stmt_valuep.close();
				}else if(("AVG".equals(type) && "tables".equals(report_type)) ||  
						 ("NUMBER".equals(type) )){
					//report2-mean.jrxml
					//report3-groupinfo.jrxml
					Statement stmt_valuea=conn.createStatement();
					String query="";
					if ( group_rows.length() == 0 ){
						query="select "+question_field+",\"Totaal\" from values_"+identifier+" where "+question_field+">0";
					}else{
						query="select "+question_field+","+group_rows+" from values_"+identifier+" where "+question_field+">0";
					}
					//@todo ugly hack response fraijlemaborg
					if (customer.equals("fraijlemaborg") && question_field.equals(config_response_group)){
						if ( group_rows.length() == 0 ){
							query="SELECT  "+config_response_group+"/"+config_population_group+" as "+config_response_group+", \"Totaal\" FROM values_"+identifier+" where 1=1 ";
						}else{
							query="SELECT  "+config_response_group+"/"+config_population_group+" as "+config_response_group+","+group_rows+" FROM values_"+identifier+" where 1=1 ";
						}
					}	
										
					if  ( (split_question_id !=null) && (split_question_id.length()>0)   ) {
						query=query+" and "+split_question_id+" like \""+split_value+"\"";
					}
					if ( group_rows.length() != 0 ){
						query=query+" order by "+group_rows;
					}

					stmt_valuea.execute(query);
					ResultSet rsh_valuea=stmt_valuea.getResultSet();
					//@todo te weinig respondenten niet tonen HvA
					
					while (rsh_valuea.next()){
						//@todo this record differs in order of variables with previous record
						Record ra=new Record(group_question_title,question_field, titlerows,question_title,rsh_valuea.getString(2),rsh_valuea.getString(1));
						reportRows.add(ra);
					}
					rsh_valuea.close();
					stmt_valuea.close();
				}else if (("AVG".equals(type)) && ("barcharts".equals(report_type))){
					//report-bc.jrxml
					//based on identical to avg as type and tables as report_type, except group by (not possible with barchart).
					Statement stmt_valuea=conn.createStatement();
					//String query="select "+question_field+",\"Totaal\" from values_"+identifier+" where "+question_field+">0";
					String query="select "+question_field+",\"Totaal\" from values_"+identifier+" where "+question_field+">0";
					
					//@todo ugly hack response fraijlemaborg
					if (customer.equals("fraijlemaborg") && question_field.equals("30_respons")){
						if ( group_rows.length() == 0 ){
							query="SELECT  "+config_response_group+"/"+config_population_group+" as "+config_response_group+", \"Totaal\" FROM values_"+identifier+" where 1=1 ";
						}else{
							query="SELECT  "+config_response_group+"/"+config_population_group+" as "+config_response_group+","+group_rows+" FROM values_"+identifier+" where 1=1 ";
						}
					}	
										
					if  ( (split_question_id !=null) && (split_question_id.length()>0)   ) {
						query=query+" and "+split_question_id+" like \""+split_value+"\"";
					}
					
					stmt_valuea.execute(query);
					ResultSet rsh_valuea=stmt_valuea.getResultSet();
					while (rsh_valuea.next()){
						//@todo this record differs in order of variables with previous record
						Record ra=new Record(group_question_title,titlerows,question_field,question_title,rsh_valuea.getString(2),rsh_valuea.getString(1));
						reportRows.add(ra);
					}
					rsh_valuea.close();
					stmt_valuea.close();
				}else if("OPEN".equals(type) && "open".equals(report_type)){
					//report4-open.jrxml
					//open;
					Statement stmt_valueo=conn.createStatement();
					String query= "select id, "+question_field+" from values_"+identifier+"  " +
							"where "+question_field+" is not null " +
							"and length("+question_field+")>0 and " +
							question_field+" not in ('0', '-') ";
					// add split by statement if not null.
					if  ((split_question_id!=null) && (split_question_id.length()>0)   ) {
						//@todo escape sql (oa quotes) escapeutils.escapesql maakt van enkele quote dubbel quote en dat maakt het alleen erger....
						query=query+"and "+split_question_id+" like \""+split_value+"\"";
					}
					stmt_valueo.execute(query);
					ResultSet rsh_valueo=stmt_valueo.getResultSet();
					while (rsh_valueo.next()){
						//@todo order of variables in next line...?
						Record ro=new Record(group_question_title,titlerows,question_field,question_title,rsh_valueo.getString(1),rsh_valueo.getString(2));
						reportRows.add(ro);
					}
					rsh_valueo.close();
					stmt_valueo.close();
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
