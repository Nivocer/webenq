package it.bisi.report.jasper;


import java.awt.Color;
import java.awt.Font;
import java.util.List;

import net.sf.jasperreports.engine.JRChart;
import net.sf.jasperreports.engine.JRChartCustomizer;
import net.sf.jasperreports.engine.JRChartPlot;

import org.jfree.chart.JFreeChart;
import org.jfree.chart.axis.CategoryAxis;
import org.jfree.chart.axis.ValueAxis;
import org.jfree.chart.labels.StandardCategoryItemLabelGenerator;
import org.jfree.chart.plot.CategoryPlot;
import org.jfree.chart.renderer.category.BarRenderer;
import org.jfree.chart.renderer.category.StackedBarRenderer;
import org.jfree.chart.renderer.category.StandardBarPainter;
import org.jfree.data.category.CategoryDataset;
import org.jfree.data.general.DatasetGroup;

public class StackedBarCustomizer implements JRChartCustomizer 
{

	public void customize(JFreeChart chart, JRChart jasperChart)
	{
		StackedBarRenderer renderer = (StackedBarRenderer) chart.getCategoryPlot().getRenderer();
		// renderer.setSeriesPaint(0, Color.green);
		// renderer.setSeriesPaint(1, Color.orange);
		
		boolean isPer = renderer.getRenderAsPercentages();
		// renderer.setRenderAsPercentages(true);
		renderer.setItemLabelFont( new Font("TimesRoman", Font.PLAIN,  8) );
		// List li1 = chart.getCategoryPlot().getCategories();

		CategoryPlot plot = chart.getCategoryPlot();
		
		/*
		CategoryDataset ds = plot.getDataset();
		int r = ds.getRowCount();
		int c = ds.getColumnCount();
		DatasetGroup dsg = ds.getGroup();
		String grpid = dsg.getID();
		*/
		renderer.setBaseItemLabelsVisible(true);
		// (( StackedBarRenderer ) plot.getRenderer(0)).setRenderAsPercentages(true);		
		renderer.setBaseItemLabelGenerator(new MyStandardCategoryItemLabelGenerator());
		// isPer = renderer.getRenderAsPercentages();
		// renderer.setRenderAsPercentages(true);
		

		
		ValueAxis vAxis = plot.getRangeAxis();
		vAxis.setTickLabelsVisible(false);
		vAxis.setTickMarksVisible(false);
		vAxis.setMinorTickMarksVisible(false);
		vAxis.setAxisLineVisible(false);
		vAxis.setAutoTickUnitSelection(false);
		vAxis.setVerticalTickLabels(false);
		vAxis.setVisible(false);
		
		
		CategoryAxis cAxis = plot.getDomainAxis();
		cAxis.setTickLabelsVisible(false);
		cAxis.setTickMarksVisible(false);
		cAxis.setMinorTickMarksVisible(false);
		cAxis.setAxisLineVisible(false);
		cAxis.setVisible(false);
		
		
		
		/*
	    StackedBarRenderer br = new StackedBarRenderer(true); //enable perc. display
	    br.setBarPainter(new StandardBarPainter());
	    br.setBaseItemLabelGenerator(new StandardCategoryItemLabelGenerator());
	    br.setBaseItemLabelsVisible(true);
	    br.setRenderAsPercentages(true);

	    ploet t.setRenderer(null);
	    plot.setRenderer(0, null);
	    plot.setRenderer(br);
	    plot.setRenderer(0, br);
	    */

		renderer.setSeriesPaint(0, Color.RED);
		renderer.setSeriesPaint(1, Color.GREEN);
		renderer.setSeriesPaint(2, Color.YELLOW);
		

		
		

	}


}