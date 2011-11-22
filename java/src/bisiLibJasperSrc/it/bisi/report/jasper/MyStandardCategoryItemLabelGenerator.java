package it.bisi.report.jasper;

import it.bisi.Utils;

import org.jfree.chart.labels.StandardCategoryItemLabelGenerator;
import org.jfree.data.category.CategoryDataset;

public class MyStandardCategoryItemLabelGenerator extends StandardCategoryItemLabelGenerator
{
	static int i = 0;
	public String generateLabel(CategoryDataset dataset, int series,int category) 
	{
		String result = null;
		String label = (String) Utils.alPercentages.get(i);
		//@todo use DecimalFormat;
		if ( ExecuteReport.prop.get("Report_Label_Format").equals(",") )
			label = label.replace('.', ',');
		
		i++;
		return label + "%";
		}
		
}
