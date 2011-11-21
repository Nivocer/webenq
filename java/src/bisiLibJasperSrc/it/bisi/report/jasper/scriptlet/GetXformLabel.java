package it.bisi.report.jasper.scriptlet;

import net.sf.jasperreports.engine.JRDefaultScriptlet;
import net.sf.jasperreports.engine.JRScriptletException;

public class GetXformLabel extends JRDefaultScriptlet {


	/**
	 *
	 */
	public void beforeReportInit() throws JRScriptletException
	{
	}


	/**
	 *
	 */
	public void afterReportInit() throws JRScriptletException
	{
	}


	/**
	 *
	 */
	public void beforePageInit() throws JRScriptletException
	{
	}


	/**
	 *
	 */
	public void afterPageInit() throws JRScriptletException
	{
	}


	/**
	 *
	 */
	public void beforeColumnInit() throws JRScriptletException
	{
	}


	/**
	 *
	 */
	public void afterColumnInit() throws JRScriptletException
	{
	}


	/**
	 *
	 */
	public void beforeGroupInit(String groupName) throws JRScriptletException
	{
	}


	/**
	 *
	 */
	public void afterGroupInit(String groupName) throws JRScriptletException
	{
	}


	/**
	 *
	 */
	public void beforeDetailEval() throws JRScriptletException
	{
	}


	/**
	 *
	 */
	public void afterDetailEval() throws JRScriptletException
	{
	}



	/**
	 *
	 */
	public String getXformLabel(String xformLocation, String formName, String searchQuestion, String searchValue) throws JRScriptletException
	{
		return it.bisi.Utils.getXformLabel(xformLocation, formName, searchQuestion, searchValue);
		               
		
	}
}