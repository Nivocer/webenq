package it.bisi.report.jasper.datasource.object;

public class Record {
	
	private String title_row;
	private String title_col;
	private String question_field;
	private String row;
	private String col;
	private String val;
	
	public Record(){
		
	}
	public Record(String title_row,String title_col,String question_field, String row,String col,String val){
		this.title_row=title_row;
		this.title_col=title_col;
		this.question_field=question_field;
		this.row=row;
		this.col=col;
		this.val=val;
	}
	public String getTitle_row() {
		return title_row;
	}
	public void setTitle_row(String titleRow) {
		title_row = titleRow;
	}
	public String getTitle_col() {
		return title_col;
	}
	public void setTitle_col(String titleCol) {
		title_col = titleCol;
	}
	public String getQuestion_field() {
		return question_field;
	}
	public void setQuestion_field(String questionField) {
		question_field = questionField;
	}
	public String getRow() {
		return row;
	}
	public void setRow(String row) {
		this.row = row;
	}
	public String getCol() {
		return col;
	}
	public void setCol(String col) {
		this.col = col;
	}
	public String getVal() {
		return val;
	}
	public void setVal(String val) {
		this.val = val;
	}
	
}
