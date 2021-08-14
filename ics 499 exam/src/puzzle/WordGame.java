package puzzle;

import java.util.*;
import java.io.*;

public class WordGame {

    public static void main(String[] args)
    {
        // read file into program
        ArrayList<String> lines = new ArrayList<>();
        try {
            String line = "";
            BufferedReader br = new BufferedReader(new FileReader("words.txt"));

            while((line = br.readLine()) != null) {
                lines.add(line);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

        boolean[] used = new boolean[lines.size()];

        // user input programming
        Scanner scnr = new Scanner(System.in);
        System.out.print("Input word: ");
        String inputWord = scnr.next();

        for (int i = 0; i < inputWord.length(); i++) {
            char ch = inputWord.charAt(i);

            // check each line in list
            for(int j = 0; j < lines.size(); j++) {
                // if this line is already used, skip it
                if(used[j] == true) {
                    continue; // skips this iteration of loop
                }

                String line = lines.get(j);

                // cw = clue/words addressed 0/1 in array per line
                String [] cw = line.split(",");
                String clue = cw[0];
                String word = cw[1];
                if(word.contains(ch + "")) {
                	
                	// start with empty string
                  	String before = ""; 
                  	
                  	// find where ch occurs in the word
                    int indexOfCh = word.indexOf(ch);
                    
                    // add beginning underscores	
                  	for (int currentIndex = 1; currentIndex < indexOfCh; currentIndex++){ 
                      before+=" _ ";
                    }
                  
                  	// start with empty string
                    String after = ""; 
                    
                    // add ending underscores
                  	for (int currentIndex2 = indexOfCh + 1; currentIndex2 < word.length(); currentIndex2++){ 
                        after+=" _ ";
                     }
                    
                    System.out.println(clue + ": " + before + ch + after);
                    
                    // set line used to true
                    used[j] = true; 
                    break;    

                }
            }
        }
    }
}
