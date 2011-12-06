package it.bisi.report.jasper;

import it.bisi.Utils;

import java.awt.Color;
import java.awt.Font;
import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.HashMap;

import net.sf.jasperreports.engine.JRAbstractChartCustomizer;
import net.sf.jasperreports.engine.JRChart;
import net.sf.jasperreports.engine.JRChartCustomizer;

import org.jfree.chart.JFreeChart;
import org.jfree.chart.axis.CategoryAxis;
import org.jfree.chart.axis.ValueAxis;
import org.jfree.chart.plot.CategoryPlot;
import org.jfree.chart.renderer.category.StackedBarRenderer;
import org.jfree.data.category.CategoryDataset;



//public class StackedBarCustomizer implements JRChartCustomizer 
//public class StackedBarCustomizer extends JRAbstractChartCustomizer
public class StackedBarCustomizer extends JRAbstractChartCustomizer implements JRChartCustomizer
{

	public void customize(JFreeChart chart, JRChart jasperChart)
	{
		StackedBarRenderer renderer = new CustomBarRenderer();
		chart.getCategoryPlot().setRenderer(renderer);
		renderer.setBaseItemLabelFont( new Font("Arial", Font.PLAIN,  7) );
					    	      
		CategoryPlot plot = chart.getCategoryPlot();
		plot.setRangeGridlinePaint(Color.white);
		CategoryDataset cd = (CategoryDataset) plot.getDataset();
		
		
		//TODO ugly place, calculate percentages, needs better approach
		HashMap hmPercentages = new HashMap();
		ArrayList alPercentages = new ArrayList();
		ArrayList hm = new ArrayList();
		double total=0;
		for (int col = 0; col < cd.getColumnCount(); col++) {
             //Determine total number of response for one bar
            for (int row = 0; row < cd.getRowCount(); row++) {
            	//String l_rowKey = (String)String.valueOf(cd.getRowKey(row));
            	double s_value = cd.getValue(row,col).doubleValue();
            	total=total+s_value;
            }	
           
            //calculate percentages
            ArrayList al = Utils.alPercentages ;
            DecimalFormat df = new java.text.DecimalFormat("#.0%");
            for (int row = 0; row < cd.getRowCount(); row++) {
               	//String l_rowKey = (String)String.valueOf(cd.getRowKey(row));
            	double s_value = cd.getValue(row,col).doubleValue();
            	al.add(  df.format (  s_value/total )  );
            }
 		}
		
		
		renderer.setBaseItemLabelsVisible(true);
				
		//volgende regel wel.
		renderer.setBaseItemLabelGenerator(new MyStandardCategoryItemLabelGenerator());
		//Removes gray line around the bar
	    renderer.setDrawBarOutline(false); 
	    renderer.setShadowVisible(false);
		
		ValueAxis vAxis = plot.getRangeAxis();
		vAxis.setTickLabelsVisible(false);
		vAxis.setTickMarksVisible(false);
		vAxis.setMinorTickMarksVisible(false);
		vAxis.setAxisLineVisible(false);
		vAxis.setAutoTickUnitSelection(false);
		vAxis.setVerticalTickLabels(false);
		vAxis.setVisible(false);
		vAxis.setUpperMargin(0.0);
		vAxis.setLowerMargin(0.0);
		
				
		CategoryAxis cAxis = plot.getDomainAxis();
		cAxis.setTickLabelsVisible(false);
		cAxis.setTickMarksVisible(false);
		cAxis.setMinorTickMarksVisible(false);
		cAxis.setAxisLineVisible(false);
		cAxis.setVisible(false);
		cAxis.setUpperMargin(0.0);
		cAxis.setLowerMargin(0.0);
		
		vAxis.setLowerMargin(0.0001);
		

		
		

	}


}