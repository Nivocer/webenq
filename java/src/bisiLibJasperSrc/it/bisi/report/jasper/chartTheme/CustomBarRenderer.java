package it.bisi.report.jasper.chartTheme;

import java.awt.Color;
import java.awt.Paint;

import org.jfree.chart.renderer.category.BarRenderer;
import org.jfree.data.category.CategoryDataset;

@SuppressWarnings("serial")
public class CustomBarRenderer extends BarRenderer {
	public CustomBarRenderer()
	  {
	    super();
	  }

	  public Paint getItemPaint(int row, int column)
	  {
	    CategoryDataset cd = getPlot().getDataset();
	    if(cd != null)
	    {
	      String l_rowKey = (String)cd.getRowKey(row);
	      Double d_rowKey=Double.valueOf(l_rowKey).doubleValue();
	      String l_colKey = (String)cd.getColumnKey(column);
	      double l_value  = cd.getValue(l_rowKey, l_colKey).doubleValue();
	      //System.out.println(row +" "+column +" "+ l_rowKey +" "+ l_colKey +" "+ l_value);
	      return d_rowKey < 3
	             ? Color.RED
	             : (d_rowKey > 3
	                ? Color.GREEN
	                : Color.ORANGE);
	    }
	    return null;
	  }
}
