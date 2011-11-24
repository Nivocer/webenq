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
      String l_rowKey = (String) cd.getRowKey(row);
      return l_rowKey.equals("1.0")  ? Color.RED
             : (l_rowKey.equals("2.0") ? Color.YELLOW
             : (l_rowKey.equals("3.0")? Color.GREEN
             : Color.ORANGE));
    }
    
    return Color.WHITE;
  }
} 