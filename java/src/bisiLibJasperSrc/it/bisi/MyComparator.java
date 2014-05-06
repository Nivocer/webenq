package it.bisi;
import java.util.Comparator;

public class MyComparator implements Comparator<String> {


	@Override
	public int compare(String arg0, String arg1) {
		// TODO Auto-generated method stub
		return arg0.compareToIgnoreCase(arg1);
		
	}}
