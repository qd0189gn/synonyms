<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles/main_style.css" type="text/css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles/custom_nav.css" type="text/css">
    <title>Play Synonyms</title>
</head>

<body>

<?php
require_once('myFunctions.php');
//require_once('word_processor.php');
require_once('language_processor_functions.php');

?>

<?php

  $nav_selected = "ADMIN"; 
  $left_buttons = "YES"; 
  $left_selected = "CREATENiN"; 

  include("./nav.php");
  global $db;

  ?>

<?php
    include 'db_configuration.php';
	//include 'puzzleGenerator.php';
?>

<?php 

if(isset($_POST['puzzleWord']))

            //$word = htmlspecialchars($_POST['puzzleWord']);
            $word = validate_input($_POST['puzzleWord']);
            //$parsedWord = getWordChars($word);


            //echo "Input Word:", $word . "<br>";

            $htmlTable = '<div class="container"><h1 style="color:red;">"' . $word . '"</h1><table class="table table-condensed main-tables" id="puzzle_table" ><thead><tr><th>Clue</th><th>Synonym</th><th>Word</th></tr></thead><tbody>';
            $htmlTableResult = '<div class="container"><h1 style="color:red;">"' . $word . '"</h1><table class="table table-condensed main-tables" id="puzzle_table" ><thead><tr><th>Clue</th><th>Synonym</th><th>Word</th></tr></thead><tbody>';
            //echo $htmlTable;
           // $wordCharacters = str_split($word);
            $wordCharacters = getWordChars($word);
            $wordCharacterSize = count($wordCharacters);
            //$showworsCharacters = implode(',', $wordCharacters);
          
            //echo "It contains ", $wordCharacterSize, " characters: ";
            //echo $showworsCharacters . "<br>";
            $usedWords = array();
    foreach ($wordCharacters as $char){

              //$tempselectedWord = null;
              //$masterWord = null;
              $pos = array_search($char, $wordCharacters) + 1;
              $query = "SELECT SynonymWord FROM synonyms WHERE SynonymWord LIKE '%$char%'";
                
              $stmt = $db->prepare($query);
              $stmt->execute();
              $stmt->store_result();
              //$numOfRows = $stmt->num_rows;
              $stmt->bind_result($wordsWithChar);
              $arrayWord = array();

              //echo "Words containing the letter ", $char, " in our database are:" ."<br>";


              while($stmt->fetch()){

                echo " This are the words with the char", $wordsWithChar. "<br>";
                $db_word_chars = getWordChars($wordsWithChar);
                if (in_array($char, $db_word_chars)){
                    array_push($arrayWord, $wordsWithChar);
                    
                    //echo $wordsWithChar. "<br>";
                }
            
            }
            echo " This is the trimmed collection";
              var_dump($arrayWord);
              $stmt->close();

           // echo "Out of these, the word randomly selected is: ";
            
            $arrayWordLen = count($arrayWord);

         while (true) {
                //echo 'Rows count: ' . $numRows . ' <br>';
                if ($arrayWordLen < 1) {
                    break;
                } elseif ($arrayWordLen > 1) {
                    $random = rand(0, $arrayWordLen - 1);
                } elseif ($arrayWordLen === 1) {
                    $random = 0;
                }
                //echo $random;
                try {
                    $tempWord = $arrayWord[$random];
                    if (!in_array($tempWord, $usedWords, true)) {
                        $tempselectedWord = $tempWord;//$arrayWord[$random];
                        //unset($arraWord[$random]);
                        break;
                    } else {
                        $arrayWordLen--;
                        array_splice($arrayWord, $random, 1);
                    }
                } catch (Exception $e) {
                    // try again
                    $arrayWordLen--;
                }
         }

        
            //echo $tempselectedWord, 'This is the word selected';


            $clueIDQuery = "SELECT ClueID FROM synonyms WHERE SynonymWord Like '$tempselectedWord'"; 
            $stmt = $db->prepare($clueIDQuery);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($clueID);
            $stmt->fetch();
            //echo  $clueID. "<br>";
            $stmt->close();


            $masterWordQuery = "SELECT SynonymWord FROM synonyms WHERE SynID = '$clueID' AND ClueID = '$clueID'"; 
            $stmt = $db->prepare($masterWordQuery);
            $stmt->execute();
            $stmt->store_result();
            //$numOfRows = $stmt->num_rows;
            $stmt->bind_result($masterWord);
            $stmt->fetch();
            
            /**
             * If the master word is different from the temp selected word then,
             *  the word has a master word.
             */
            if ($masterWord != $tempselectedWord){
                // select all words that have the same clueID as the selected word,
                // and add them to usedWords so, they can't be reused. Because, they
                // will all have the same master word.

                $masterWordQuerySet = "SELECT SynonymWord FROM synonyms WHERE ClueID = $clueID"; 
                $stmt = $db->prepare($masterWordQuerySet);
                $stmt->execute();
                $stmt->store_result();
                //$numOfRows = $stmt->num_rows;
                $stmt->bind_result($masterWordSynonyms);
                $stmt->fetch();
                while($stmt->fetch()){
                    array_push($usedWords, $masterWordSynonyms);
                    //echo $masterWordSynonyms. "<br>";
                 }

                $tempSelectedWordchars = getWordChars($tempselectedWord);
                $pos = array_search($char, $tempSelectedWordchars) + 1;
                $len = count($tempSelectedWordchars);
                $newpos = $pos -1;

                // storing value for the clue;


              $htmlTable .= "<tr><td align='center' style='vertical-align: middle;'>" . $pos . '/' . $len . "</td>";
              $htmlTableResult .= "<tr><td align='center' style='vertical-align: middle;'>" . $pos . '/' . $len . "</td>";

              //master word represents the synonym in the table
              $htmlTable .= "<td align='center' style='vertical-align: middle;'>" . $masterWord. "</td>";

              $htmlTableResult .= "<td align='center' style='vertical-align: middle;'>" . $masterWord. "</td>";

              /**
               * Now, printing out the the letter to be revealed in the word 
               * plus empty spaces to be filled by the user. 
               */
              $htmlTable .= "<td style='vertical-align: middle;'>";

              $htmlTableResult .= "<td style='vertical-align: middle;'>";
                      $flag = false;
                      for ($j = 0; $j < $len; $j++) {
                        $htmlTableResult .= '<input class="puzzleInput word_char active" type="text" maxLength="7" value="' .$tempSelectedWordchars[$j] . '" style="display:inline" readonly/>';
                          if (($j === $newpos) && !$flag) {
                              $htmlTable .= '<input class="puzzleInput word_char active" type="text" maxLength="7" value="' . $tempSelectedWordchars[$j] . '" style="display:inline" readonly/>';

                              $flag = true;
                          } else {
                              $htmlTable .= '<input class="puzzleInput word_char" type="text" maxLength="7" value="" style="display:inline"/>';

                              //$htmlTableResult .= '<input class="puzzleInput word_char" type="text" maxLength="7" value="" style="display:inline"/>';
                          }
                      }
                      $htmlTable .='</td>';
                      $htmlTableResult .='</td>';
                
                      
            }
            else{
          // else, the tempselected word above has no master word so, a new
          // random tempselectedWord most be chosen.
               
          $query = "SELECT SynonymWord FROM synonyms WHERE SynonymWord LIKE '%$char%'";
                
          $stmt = $db->prepare($query);
          $stmt->execute();
          $stmt->store_result();
          //$numOfRows = $stmt->num_rows;
          $stmt->bind_result($wordsWithChar);
          $arrayWord = array();

          //echo "Words containing the letter ", $char, " in our database are:" ."<br>";


          while($stmt->fetch()){

            echo " This are the words with the char", $wordsWithChar. "<br>";
            $db_word_chars = getWordChars($wordsWithChar);
            if (in_array($char, $db_word_chars)){
                array_push($arrayWord, $wordsWithChar);
                
                //echo $wordsWithChar. "<br>";
            }
        
        }
        echo " This is the trimmed collection";
          var_dump($arrayWord);
          $stmt->close();

       // echo "Out of these, the word randomly selected is: ";
        
        $arrayWordLen = count($arrayWord);


               // $tempselectedWord = null;
                while (true) {
                    if ($arrayWordLen < 1) {
                        break;
                    } elseif ($arrayWordLen > 1) {
                        $random = rand(0, $arrayWordLen - 1);
                    } elseif ($arrayWordLen === 1) {
                        $random = 0;
                    }
                    try {
                        $tempWord = $arrayWord[$random];
                        if (!in_array($tempWord, $usedWords, true)) {
                            $tempselectedWord = $arrayWord[$random];
                            //unset($arrayWord[$random]);
                            break;
                        } else {
                            $arrayWordLen--;
                            array_splice($arrayWord, $random, 1);
                        }
                    } catch (Exception $e) {
                        // try again
                        $arrayWordLen--;
                    }
                   // array_push($usedWords, $tempselectedWord);
                }


                // $random = array_rand($arrayWord);
                // $temp = $arrayWord[$random];
                // if(!in_array($usedWords, $temp)){
                //     $tempselectedWord = $temp;
                // }
            
                // gettin the clueID for the new tempselected word

                $clueIDQuery = "SELECT ClueID FROM synonyms WHERE SynonymWord Like '$tempselectedWord'"; 
                $stmt = $db->prepare($clueIDQuery);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($clueID);
                $stmt->fetch();
                $stmt->close();
              
                $masterWordQuerySet = "SELECT SynonymWord FROM synonyms WHERE ClueID = $clueID"; 
                $stmt = $db->prepare($masterWordQuerySet);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($masterWordSynon);
                $stmt->fetch();
                while($stmt->fetch()){
                    array_push($usedWords, $masterWordSynon);
                    //echo $masterWordSynon. "<br>";
                 }
                
                $tempSelectedWordchars = getWordChars($tempselectedWord);
                $pos = array_search($char, $tempSelectedWordchars) + 1;
                $len = count($tempSelectedWordchars);
                $newpos = $pos -1;

                $stmt->close();


               $masterWordQuery = "SELECT SynonymWord FROM synonyms WHERE SynID = '$clueID' AND ClueID = '$clueID'"; 
               $stmt = $db->prepare($masterWordQuery);
               $stmt->execute();
               $stmt->store_result();
               $stmt->bind_result($masterWord);
               $stmt->fetch();

               //array_push($usedWords, $masterWord);
               //echo $masterWord;

              $htmlTable .= "<tr><td align='center' style='vertical-align: middle;'>" . $pos . '/' . $len . "</td>";
              $htmlTableResult .= "<tr><td align='center' style='vertical-align: middle;'>" . $pos . '/' . $len . "</td>";
          
              $htmlTable .= "<td align='center' style='vertical-align: middle;'>" . $masterWord. "</td>";
              $htmlTable .= "<td style='vertical-align: middle;'>";

              $htmlTableResult .= "<td align='center' style='vertical-align: middle;'>" . $masterWord. "</td>";
              $htmlTableResult .= "<td style='vertical-align: middle;'>";

                      $flag = false;
                      for ($j = 0; $j < $len; $j++) {
                        $htmlTableResult .= '<input class="puzzleInput word_char active" type="text" maxLength="7" value="' . $tempSelectedWordchars[$j] . '" style="display:inline" readonly/>';
                          if (($j === $newpos) && !$flag) {
                              $htmlTable .= '<input class="puzzleInput word_char active" type="text" maxLength="7" value="' . $tempSelectedWordchars[$j] . '" style="display:inline" readonly/>';
                              $flag = true;
                          } else {
                              $htmlTable .= '<input class="puzzleInput word_char" type="text" maxLength="7" value="" style="display:inline"/>';

                              //$htmlTableResult .= '<input class="puzzleInput word_char" type="text" maxLength="7" value="" style="display:inline"/>';
                          }
                      }
                      $htmlTable .='</td>';
                      $htmlTableResult .='</td>';

            }  

        //array_push($usedWords, $masterWord);

       }

          $htmlTable .= "</div>";
          $htmlTable .= '</tbody></table><img id="success_photo" class="success" src="pic/thumbs_up.png" alt="Success!" style="display:none"></div>';

          $htmlTableResult .= "</div>";
          $htmlTableResult .= '</tbody></table><img id="success_photo" class="success" src="pic/thumbs_up.png" alt="Success!" style="display:none"></div>';

          echo $htmlTable;
          //echo $masterWord;
         //echo $htmlTableResult;

createTableFooter();
//var_dump($usedWords);

function get_randomWord($array_of_words, $usedWords){

    $chosenWord = null;
    $array_of_Words = array();
    $usedWords = array();
    $array_of_WordLen = count($array_of_Words);
    while (true) {
        //echo 'Rows count: ' . $numRows . ' <br>';
        if ($array_of_WordLen < 1) {
            break;
        } elseif ($array_of_WordLen > 1) {
            $random = rand(0, $array_of_WordLen - 1);
        } elseif ($array_of_WordLen === 1) {
            $random = 0;
        }
        //echo $random;
        try {
            $tempWord = $array_of_Words[$random];
            if (!in_array($tempWord, $usedWords, true)) {
                $chosenWord = $tempWord;//$arrayWord[$random];
                //unset($arraWord[$random]);
                break;
            } else {
                $array_of_WordLen--;
                array_splice($array_of_Words, $random, 1);
            }
        } catch (Exception $e) {
            // try again
            $array_of_WordLen--;
        }

        return $chosenWord;
    }
}

function createTableFooter()
    {
        $buttons = '<div class="container" ><input class="main-buttons" type="button" name="submitSolution" 
                value="Submit Solution" onclick="main_buttons(\'submit\');";>
                <input class="main-buttons" type="button" name="showSolution" 
                value="Show Solution" onclick="main_buttons(\'showSolution\')";>
                <input class="main-buttons" type="button" name="changeInputMode" 
                value="Change Input Mode" onclick="change_puzzle_input()"> </div>';
        echo $buttons;
    }

function main_buttons($button_name) {
       
      // for submit_solution
      if ($button_name == "submit") {
         
          echo "thanks for your submission";

      } else if ($button_name == "showSolution") { // for show solution
          // call show_solution handler method for the show solution button
          //showSolution();
           echo $htmlTableResult;
      }
}



?>

<!---button class='main-buttons' onclick="submitSolution()">Submit Solution</button>
	<button class='main-buttons' onclick="showSolution()">Show Solution</button>
  <button class='sub' onclick="showSolution()">Show Solution</button>
	<button class='main-buttons' onclick="changeInput()">Change Input Mode</button>
	<br />
	<br /--->


  <script type="text/javascript">
    /**
     *     main function for the buttons when they're clicked.
     */
    function main_buttons(button_name) {
       
            // for submit_solution
            if (button_name == "submit") {
               
                var msg = "thanks for your submission";

            } else if (button_name == "show") { // for show solution
                // call show_solution handler method for the show solution button
                var msg = showSolution();
            }
            return msg;
    }


    function showSolution(){

      var showSoln = "<?php echo $htmlTableResult ?>";
      return showSoln;
    }

    function toggle_display(el) {
        if (el.style.display == "inline") {
            el.style.display = "none";
        } else {
            el.style.display = "inline";
        }
    }

    function change_puzzle_input() {
        var alt = document.getElementsByClassName("altPuzzleInput");
        var i;
        for (i = 0; i < alt.length; i++) {
            toggle_display(alt[i]);
        }
        var norm = document.getElementsByClassName("puzzleInput");
        var i;
        for (i = 0; i < norm.length; i++) {
            toggle_display(norm[i]);
        }
    }

    </script>
</body>

</html>
