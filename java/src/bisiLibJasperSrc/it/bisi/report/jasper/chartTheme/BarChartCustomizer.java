package it.bisi.report.jasper.chartTheme;


import java.awt.Color;

import net.sf.jasperreports.engine.JRAbstractChartCustomizer;
import net.sf.jasperreports.engine.JRChart;
import net.sf.jasperreports.engine.JRChartCustomizer;
import net.sf.jasperreports.engine.JRPropertiesMap;

import org.jfree.chart.JFreeChart;
import org.jfree.chart.plot.CategoryPlot;
import org.jfree.chart.plot.PlotOrientation;
import org.jfree.chart.renderer.category.BarRenderer;

public class BarChartCustomizer extends JRAbstractChartCustomizer implements JRChartCustomizer
{
	

	public void customize(JFreeChart chart, JRChart jasperChart)
	{
		JRPropertiesMap pm = jasperChart.getPropertiesMap();
		System.out.println(pm);
		BarRenderer renderer = new CustomBarRenderer();
		 //Removes gray line around the bar
	      renderer.setDrawBarOutline(false); 
	      renderer.setShadowVisible(false);
	      
	      //no space between bars
	      renderer.setItemMargin(0.0);
	      
		CategoryPlot categoryplot = (CategoryPlot) chart.getCategoryPlot();
		
		
		 //set background grid color//remove grid
        categoryplot.setRangeGridlinePaint(Color.white);
        categoryplot.setRenderer(renderer);

        categoryplot.setOrientation(PlotOrientation.HORIZONTAL);
        categoryplot.setOrientation(PlotOrientation.VERTICAL);
		
		//BarRenderer renderer = (BarRenderer) chart.getCategoryPlot().getRenderer();
		//renderer.setSeriesPaint(0, Color.green);
		//renderer.setSeriesPaint(1, Color.orange);
	}
}
