package puzzle;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Scanner;

public class MovieCollection {

   public static void main(String[] args) throws IOException {
      
	   ArrayList<Movie> movieList = new ArrayList<Movie>();
	   
       try
       {
           Scanner file = new Scanner(new File("movies.txt"));
           while(file.hasNext())
           {
               String line=file.nextLine();
               String delim=", ";
               String[] token=line.split(delim);
               String name2=token[0];
               String year_made2=(token[1]);
               Movie m = new Movie(name2, year_made2);
               movieList.add(m);
           }
       }catch (FileNotFoundException e) {
           e.printStackTrace();
       }
       Scanner input = new Scanner(System.in);
       
       String input1;

       
       System.out.println("Enter a puzzle *Word*: ");
       input1=input.nextLine();
       

       boolean exist=false;
	   for(Movie m1 : movieList)
	   {
	       if((m1.getName().equalsIgnoreCase(input1)))
	       {
	           System.out.println("Puzzle Word: " + input1);

	           String a = input1;
	           
	           // Creating array of string length
	           char[] ch = new char[a.length()];
	     
	           // Copy character by character into array
	           for (int i = 0; i < a.length(); i++) {
	               ch[i] = a.charAt(i);
	               
	           }
	     
	           // Printing content of array
	           for (char c : ch) {
	               System.out.println(c);
	           }
	           exist=true;
	           break;
	       }
	  // }
	   //if(!exist)
	  // {
	       System.out.println("This puzzle word doesn't exit in movies.txt");
	   }
	   searchForName();
   }
   private static void searchForName() throws IOException {
	    System.out.println("Please enter the name you would like to search for: ");
	    Scanner kb = new Scanner(System.in);
	    String name = kb.nextLine();


	    List<String> lines = Files.readAllLines(Paths.get("movies.txt"));
	    for (String line : lines) {
	        if (line.contains(name)) {
	            System.out.println(line);
	        }
	    }
	}
}