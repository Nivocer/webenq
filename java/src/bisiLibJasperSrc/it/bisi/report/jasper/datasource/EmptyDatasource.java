package it.bisi.report.jasper.datasource;

import net.sf.jasperreports.engine.JRDataSource;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRField;

public class EmptyDatasource  implements JRDataSource {

	/**
	 *
	 */
	private Object[][] data =
		{
			{new Integer(1)}
			
		};

	private int index = -1;
	

	
	public EmptyDatasource() {
	}
	/**
	 *
	 */
	public boolean next() throws JRException
	{
		index++;

		return (index < data.length);
	}


	/**
	 *
	 */
	public Object getFieldValue(JRField field) throws JRException
	{
		Object value = null;
		
		String fieldName = field.getName();
		
		if ("id".equals(fieldName))
		{
			value = data[index][0];
		}
				return value;
	}
	
}
