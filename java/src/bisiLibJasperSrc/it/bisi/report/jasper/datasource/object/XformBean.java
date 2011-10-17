package it.bisi.report.jasper.datasource.object;

public class XformBean {
	private String report_question_id;
	private String group_question_value;
	private String group_question_id;
	private String report_question_value;
	
	public XformBean(){
		
	}
	public XformBean(String report_question_id, String report_question_value, String group_question_id, String group_question_value ){
		this.report_question_id=report_question_id;
		this.group_question_id=group_question_id;
		this.group_question_value=group_question_value;
		this.report_question_value=report_question_value;
	}
	public String getReport_question_id() {
		return report_question_id;
	}
	public void setReport_question_id(String report_question_id) {
		this.report_question_id = report_question_id;
	}
	public String getGroup_question_value() {
		return group_question_value;
	}
	public void setGroup_question_value(String group_question_value) {
		this.group_question_value = group_question_value;
	}
	public String getGroup_question_id() {
		return group_question_id;
	}
	public void setGroup_question_id(String group_question_id) {
		this.group_question_id = group_question_id;
	}
	public String getReport_question_value() {
		return report_question_value;
	}
	public void setReport_question_value(String report_question_value) {
		this.report_question_value = report_question_value;
	}
		
	}
