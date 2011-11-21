package it.bisi;

import java.io.File;
import java.io.IOException;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

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
	public static HashMap hmPercentages = new HashMap();
	public static ArrayList alPercentages = new ArrayList();
	
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
				if (searchValue != null && !searchValue.equals("")){
					result=searchValue;
				}else{
					result=searchQuestion;
				}
			}
		} catch (XPathExpressionException e) {
			// TODO Auto-generated catch block
			result=null;
			e.printStackTrace();
		}
		return (String) result;
	}
	

	/**
	 * @param customer
	 * @return
	 * ranges are from x to y (incl x, excl y)
	 */
	public static HashMap<String, Map<String, Double>> getColorRangeMaps(String customer) {
		HashMap<String, Map<String,Double>> returnMap=new HashMap<String,Map<String,Double>>();
		
		//Map<String, Object> mpMaps=new HashMap<String, Object>();
		String map_id=null;
		if (customer.equals("fraijlemaborg")){
			map_id="white";
			Map<String,Double> color_range_white=new HashMap<String,Double>();
			color_range_white.put("lowRed",new Double(0.0));
			color_range_white.put("highRed",new Double(0.0));
			color_range_white.put("lowYellow",new Double(0.0));
			color_range_white.put("highYellow",new Double(0.0));
			color_range_white.put("lowGreen",new Double(0.0));
			color_range_white.put("highGreen", new Double(0.0));
			returnMap.put(map_id,color_range_white);
						
			map_id="mean5";
			Map<String,Double> color_range_mean5=new HashMap<String,Double>();
			color_range_mean5.put("lowRed",new Double(1.0));
			color_range_mean5.put("highRed",new Double(2.95));
			color_range_mean5.put("lowYellow",new Double(0.0));
			color_range_mean5.put("highYellow",new Double(0.0));
			color_range_mean5.put("lowGreen",new Double(3.95));
			color_range_mean5.put("highGreen", new Double(5.0));
			returnMap.put(map_id,color_range_mean5);
			
			map_id="mean10";
			Map<String,Double> color_range_mean10=new HashMap<String,Double>();
			color_range_mean10.put("lowRed",new Double(1.0));
			color_range_mean10.put("highRed",new Double(5.45));
			color_range_mean10.put("lowYellow",new Double(5.45));
			color_range_mean10.put("highYellow",new Double(6.45));
			color_range_mean10.put("lowGreen",new Double(6.45));
			color_range_mean10.put("highGreen", new Double(10.0));
			returnMap.put(map_id,color_range_mean10);
			
		}else if (customer.equals("hvaoo")) {
			map_id="white";
			Map<String,Double> color_range_white=new HashMap<String,Double>();
			color_range_white.put("lowRed",new Double(0.0));
			color_range_white.put("highRed",new Double(0.0));
			color_range_white.put("lowYellow",new Double(0.0));
			color_range_white.put("highYellow",new Double(0.0));
			color_range_white.put("lowGreen",new Double(0.0));
			color_range_white.put("highGreen", new Double(0.0));
			returnMap.put(map_id,color_range_white);
			
			map_id="mean5";
			Map<String,Double> color_range_mean5=new HashMap<String,Double>();
			color_range_mean5.put("lowRed",new Double(1.0));
			color_range_mean5.put("highRed",new Double(2.95));
			color_range_mean5.put("lowYellow",new Double(2.95));
			color_range_mean5.put("highYellow",new Double(3.95));
			color_range_mean5.put("lowGreen",new Double(3.95));
			color_range_mean5.put("highGreen", new Double(5.0));
			returnMap.put(map_id,color_range_mean5);
			
			map_id="mean10";
			Map<String,Double> color_range_mean10=new HashMap<String,Double>();
			color_range_mean10.put("lowRed",new Double(1.0));
			color_range_mean10.put("highRed",new Double(5.45));
			color_range_mean10.put("lowYellow",new Double(5.45));
			color_range_mean10.put("highYellow",new Double(6.45));
			color_range_mean10.put("lowGreen",new Double(6.45));
			color_range_mean10.put("highGreen", new Double(10.0));
			returnMap.put(map_id,color_range_mean10);
			
		}else if (customer.equals("leeuwenburg")){
			map_id="mean5";
			//rood < 3 (dus exclusief 3.0
			//geel 3 tot 4 (dus exclusief 4.0)
			//groen 4 en hoger
			Map<String,Double> color_range_mean5=new HashMap<String,Double>();
			color_range_mean5.put("lowRed",new Double(1.0));
			color_range_mean5.put("highRed",new Double(2.95));
			color_range_mean5.put("lowYellow",new Double(2.95));
			color_range_mean5.put("highYellow",new Double(3.95));
			color_range_mean5.put("lowGreen",new Double(3.95));
			color_range_mean5.put("highGreen", new Double(5.0));
			returnMap.put(map_id,color_range_mean5);
						
			map_id="mean10";
			Map<String,Double> color_range_mean10=new HashMap<String,Double>();
			color_range_mean10.put("lowRed",new Double(1.0));
			color_range_mean10.put("highRed",new Double(5.45));
			color_range_mean10.put("lowYellow",new Double(5.45));
			color_range_mean10.put("highYellow",new Double(6.45));
			color_range_mean10.put("lowGreen",new Double(6.45));
			color_range_mean10.put("highGreen", new Double(10.0));
			returnMap.put(map_id,color_range_mean10);
			
		}else {
			//default
			map_id="mean5";
			Map<String,Double> color_range_mean5=new HashMap<String,Double>();
			color_range_mean5.put("lowRed",new Double(1.0));
			color_range_mean5.put("highRed",new Double(2.5));
			color_range_mean5.put("lowYellow",new Double(2.5));
			color_range_mean5.put("highYellow",new Double(3.5));
			color_range_mean5.put("lowGreen",new Double(3.5));
			color_range_mean5.put("highGreen", new Double(5.0));
			returnMap.put(map_id,color_range_mean5);
			
			
			map_id="mean10";
			Map<String,Double> color_range_mean10=new HashMap<String,Double>();
			color_range_mean10.put("lowRed",new Double(1.0));
			color_range_mean10.put("highRed",new Double(5.45));
			color_range_mean10.put("lowYellow",new Double(5.45));
			color_range_mean10.put("highYellow",new Double(6.45));
			color_range_mean10.put("lowGreen",new Double(6.45));
			color_range_mean10.put("highGreen", new Double(10.0));
			returnMap.put(map_id,color_range_mean10);
			
		}
			
		return returnMap;
		
	}
	
}
