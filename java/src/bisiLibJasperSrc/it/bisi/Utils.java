package it.bisi;

import java.io.File;
import java.io.IOException;
import java.net.URL;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathExpressionException;
import javax.xml.xpath.XPathFactory;

import org.w3c.dom.Document;
import org.xml.sax.SAXException;

public class Utils {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub
		System.out.println(getResource("/it/bisi/resources/logo.png"));

	}
	public static URL getResource(String path){
		return Utils.class.getResource(path);
	}
	/**
	 * @param xformLocation
	 * @param formName
	 * @param searchQuestion
	 * @param searchValue
	 * @return
	 */
	public static String getXformLabel(String xformLocation, String formName, String searchQuestion, String searchValue){
		
		//String xformLocation="/home/jaapandre/workspace/webenq4/java/src/webenqResources/org/webenq/resources/3-hva-oo-simpleQuest.xml";

		//read xform info and do something with it 
		// @TODO determine what to do with it
		//read file and put it in dom-object
		File fXmlFile = new File(xformLocation);
		DocumentBuilderFactory dbFactory = DocumentBuilderFactory.newInstance();
		DocumentBuilder dBuilder = null;
		try {
			dBuilder = dbFactory.newDocumentBuilder();
		} catch (ParserConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		Document doc = null;
		try {
			doc = dBuilder.parse(fXmlFile);
		} catch (SAXException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		//search the label of the 'question'		
		XPathFactory factory = XPathFactory.newInstance();
		XPath xpath = factory.newXPath();
		//labels are descendants (children,grandchildren of /html/body
		//attribute (ref) contains the searchInput (needs to have the 'name' of the instance defined under model) 
		//String  searchString="/html/body/descendant::*[@ref='/"+searchQuestion+"']/item[value='1.0']/label";
		String searchString=null;
		if (searchValue != null && !searchValue.equals("")){
			searchString="/html/body/descendant::*[@ref='/"+formName+"/"+searchQuestion+"']/item[value='"+searchValue+"']/label";	
		} else {
			searchString="/html/body/descendant::*[@ref='/"+formName+"/"+searchQuestion+"']/label";
		}

		XPathExpression expr = null;
		try {
			expr = xpath.compile(searchString);
		} catch (XPathExpressionException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		Object result=null;
		try {
			result = expr.evaluate(doc, XPathConstants.STRING);
			if (result.equals("")){
				result=searchQuestion;
				result=searchString;
			}
			} catch (XPathExpressionException e) {
			// TODO Auto-generated catch block
			result=null;
			e.printStackTrace();
		}
		return (String) result;
	}

}
