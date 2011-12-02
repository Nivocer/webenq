package it.bisi.report.jasper;

import java.awt.Color;
import java.awt.Paint;

//import org.jfree.chart.renderer.category.BarRenderer;
import org.jfree.chart.renderer.category.StackedBarRenderer;
import org.jfree.data.category.CategoryDataset;

public class CustomBarRenderer extends StackedBarRenderer
{
  /**
	 * 
	 */
	private static final long serialVersionUID = 1L;

public CustomBarRenderer()
  {
    super();
  }

  public Paint getItemPaint(int row, int column)
  {
    CategoryDataset cd = getPlot().getDataset();
    if(cd != null)
    {
      String s_rowKey = (String) cd.getRowKey(row);
      double rowKey = Double.parseDouble(s_rowKey);
      return rowKey == 1.0  ? Color.RED
             : (rowKey==2.0 ? Color.YELLOW
             : (rowKey==3.0 ? Color.GREEN
             : Color.PINK));
    }
    
    return Color.WHITE;
  }
} 