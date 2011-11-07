package it.bisi;

import java.net.URL;

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

}
