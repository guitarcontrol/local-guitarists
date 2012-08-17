<?php
    /*
        functions_chords.php
        
        Our collection of functions to use inside of the chord pages.
    */
    
    // our function to loop through and find our notes
    function build_note_array($note, $steps, $arrNotes) {
        // create our array of notes
        $arrSetup = array($note);
        
        // set the starting position in our strings array
        $pos = array_search($note, $arrNotes);
        
        // loop through our formulas to build our notes
        foreach ($steps as $step) {
            // add this number to our current location
            $pos = $pos + $step;
            
            // if the position goes beyond our array length, start over
            if ($pos >= 12) {
                $pos = $pos - 12;
            }
            
            // append this note to our array
            $arrSetup[] = $arrNotes[$pos];
        }
        
        // return the array
        return $arrSetup;
    }
    
    // our function to loop through and find our notes
    function build_tab_array($arrNotes, $arrStrings, $arrSetup, $pos = 0) {
        // create our chord info
        $arrChord = array("1" => "", "2" => "", "3" => "", "4" => "", "5" => "", "6" => "");
        
        // loop through our strings to start processing
        for ($i = 1; $i <= 6; $i++) {
            // loop from our starting position up 4 frets
            for ($n = $pos; $n < $pos + 5; $n++) {
                // if this note is in our array, add it
                if (in_array($arrStrings[$i][$n], $arrSetup)) {
                    // if not data is here, add it now
                    if (!strlen($arrChord[$i])) {
                        $arrChord[$i] = $n;
                    }
                }
            }
        }
        
        // walk back through the array and set the last note as the root
        /*for ($i = 6; $i >= 4; $i--) {
            // if this value is the root, stop here
            if ($arrStrings[$i][$arrChord[$i]] == $arrNotes[0]) {
                break;
            } else {
                // reset the value
                $arrChord[$i] = "";
            }
        }*/
        
        // return the array
        return $arrChord;
    }
    
    // our function to return the choord spelling
    function build_chord_spelling($arrNotes, $arrStrings, $arrSetup, $pos = 0) {
        // create our chord info
        $arrChord = array("1" => "", "2" => "", "3" => "", "4" => "", "5" => "", "6" => "");
        
        // set the notes added so far
        $arrLetters = array();
        
        // loop through our strings to start processing
        for ($i = 1; $i <= 6; $i++) {
            // loop from our starting position up 4 frets
            for ($n = $pos; $n < $pos + 5; $n++) {
                // if this note is in our array, add it
                if (in_array($arrStrings[$i][$n], $arrSetup)) {
                    // if not data is here, add it now
                    if (!strlen($arrChord[$i])) {
                        $arrChord[$i] = $n;
                        $arrLetters[] = $arrStrings[$i][$n];
                    }
                }
            }
        }
        
        // return the array
        return $arrLetters;
    }
    
    // our function to print the chord out for display
    function print_chord($notes, $strings, $pos, $root) {
        // create our string
        $chart = "";
        
        // set the end fret to loop to
        if ($pos) {
            $end = $pos + 4;
        } else {
            $end = $pos + 5;
        }
        
        // loop through each string and out put our data
        for ($i = 1; $i <= 6; $i++) {
            // append the string name
            $chart .= $strings[$i][0] . " |";
            
            // loop from our starting position up 5 strings
            for ($n = $pos; $n < $end; $n++) {
                // see only display our position if it's not the open string
                if ($n) {
                    // if this note is in our array, add it
                    if ($notes[$i] == $n && !$n) {
                        $chart .= "-0-|";
                    } else if ($notes[$i] == $n && $n) {
                        // see if it's the root note
                        if ($strings[$i][$n] == $root) {
                            $chart .= "-<b style=\"color: red;\">X</b>-|";
                        } else {
                            $chart .= "-X-|";
                        }
                    } else {
                        $chart .= "---|";
                    }
                }
            }
            
            // end the row
            if (strlen($notes[$i])) {
                $chart .= " (" . $notes[$i] . ")\n";
            } else {
                $chart .= "\n";
            }
        }
        
        // add our next line to the chart
        $chart .= "  ";
        
        // print our the fret #'s
        for ($n = $pos; $n < $end; $n++) {
            // don't display the open string
            if ($n) {
                // if this note is in our array, add it
                if (strlen($n) == 1) {
                    $chart .= "  $n ";
                } else {
                    $chart .= "  $n";
                }
            }
        }
        
        // print out the string
        print $chart;
    }
    
    // our function to print the chord out for display
    function print_chord_map($notes, $strings, $root, $chord) {
        // create our string
        $chart = "";
        $arrRoots = array();
        
        // create our position array
        $arrNotes = array("C" => 0, "C#" => 1, "D" => 2, "D#" => 3, "E" => 4, "F" => 5, "F#" => 6,
                          "G" => 7, "G#" => 8, "A" => 9, "A#" => 10, "B" => 11);
        
        // loop through each string and out put our data
        for ($i = 1; $i <= 6; $i++) {
            // append the string name
            $chart .= $strings[$i][0] . " |";
            
            // loop from our starting position up 5 strings
            for ($n = 0; $n < 13; $n++) {
                if ($n) {
                    // if this note is in our array, add it
                    if ($notes[0] == $strings[$i][$n]) {
                        // display it as a root note
                        $chart .= "-R-|";
                        
                        // if we're beyond the 2nd string, track our root notes
                        if ($i > 2 && $n < 12) {
                            $arrRoots[$i] = $n;
                        }
                    } else if (in_array($strings[$i][$n], $notes)) {
                        $chart .= "-o-|";
                    } else {
                        $chart .= "---|";
                    }
                } else {
                    // if we're beyond the 2nd string, track our root notes
                    if ($i > 2 && $root == $strings[$i][$n]) {
                        $arrRoots[$i] = $n;
                    }
                }
            }
            
            // end the row
            $chart .= "\n";
        }
        
        // print our the fret #'s
        for ($n = 0; $n < 13; $n++) {
            if ($n) {
                // if this note is in our array, add it
                if (strlen($n) == 1) {
                    // see if this fret is in our array
                    if (in_array($n, $arrRoots)) {
                        $chart .= "  $n ";
                    } else {
                        $chart .= "  $n ";
                    }
                } else {
                    if (in_array($n, $arrRoots)) {
                        $chart .= "  $n";
                    } else {
                        $chart .= "  $n";
                    }
                }
            } else {
                if (in_array($n, $arrRoots)) {
                    $chart .= "$n ";
                } else {
                    $chart .= "$n ";
                }
            }
        }
        
        // print out the string
        print $chart;
    }
    
    // our function to return the # of times fret appears in a chord listing
    function count_chord_frets($array, $fret) {
        // set our # of occurrances
        $count = 0;
        
        // loop through the array
        foreach ($array as $key => $value) {
            // if the values match, update the count
            if ($value == $fret) {
                $count++;
            }
        }
        
        // return the count
        return $count;
    }
    
    // our function to print the chord out for display
    function print_scale($notes, $strings, $pos, $root) {
        // create our string
        $chart = "";
        
        // set the end fret to loop to
        $end = $pos + 5;
        
        // loop through each string and output our data
        for ($i = 1; $i <= 6; $i++) {
            // if we're on the open fret, italicize the root note if it is to be used
            if (!$pos) {
                // if this note is in our array, display it
                if (in_array($strings[$i][0], $notes)) {
                    //$chart .= "<em>" . $strings[$i][0] . "</em> |";
                    $chart .= "o |";
                } else {
                    //$chart .= $strings[$i][0] . " |";
                    $chart .= "  |";
                }
            } else {
                // append the string name
                $chart .= $strings[$i][0] . " |";
            }
            
            // loop from our starting fret to our ending fret for this string
            for ($n = $pos; $n < $end; $n++) {
                // only dipslay inner frets
                if ($n) {
                    // only process it if it's not on another string
                    if ($i != 3 || ($i == 3 && !find_string_notes(2, $strings[$i][$n], $strings, $pos, $end))) {
                        // if this our root note, mark it now
                        if ($strings[$i][$n] == $root) {
                            $chart .= "-<b style=\"color: red;\">R</b>-|";
                        } else if (in_array($strings[$i][$n], $notes)) {
                            $chart .= "-o-|";
                        } else {
                            $chart .= "---|";
                        }
                    } else {
                        $chart .= "---|";
                    }
                }
            }
            
            // end the row
            $chart .= "\n";
        }
        
        // add our next line to the chart
        $chart .= "  ";
        
        // print our the fret #'s
        for ($n = $pos; $n < $end; $n++) {
            // don't display the open string
            if ($n) {
                // if this note is in our array, add it
                if (strlen($n) == 1) {
                    $chart .= "  $n ";
                } else {
                    $chart .= "  $n";
                }
            }
        }
        
        // print out the string
        print $chart;
    }
    
    // our function to create an array of our strings and frets
    function create_tab_array($notes, $strings, $start, $end) {
        // create our array
        $arrTab = array(1 => array(), 2 => array(), 3 => array(), 4 => array(), 5 => array(), 6 => array());
        
        // loop through our strings
        for ($i = 1; $i <= 6; $i++) {
            // loop through our frets
            for ($n = $start; $n <= $end; $n++) {
                // only process it if it's not on another string
                if ($i != 3 || ($i == 3 && !find_string_notes(2, $strings[$i][$n], $strings, $start, $end))) {
                    // if this value is in our array, add it
                    if (in_array($strings[$i][$n], $notes)) {
                        $arrTab[$i][] = $n;
                    }
                }
            }
        }
        
        // return the array
        return $arrTab;
    }
    
    // our function to print the scale tablature
    function print_scale_tab($notes, $strings, $start, $end) {
        // create our string
        $chart = "";
        
        // create our array of tab strings
        $arrFrets = create_tab_array($notes, $strings, $start, $end);
        
        // create the display array
        $arrDisplay = array();
        
        // loop backwards through the array to display
        for ($i = 6; $i >= 1; $i--) {
            // set the string item
            $arrDisplay[$i] = "";
            
            // loop through this string to set the data
            for ($n = 0; $n < count($arrFrets[$i]); $n++) {
                // append this data to our string
                $arrDisplay[$i] .= $arrFrets[$i][$n] . "-";
            }
        }
        
        // loop through the array to start displaying the data
        /*for ($i = 1; $i <= count($arrDisplay); $i++) {
            // get the length of this data
            $length = strlen($arrDisplay[$i]);
            
            // set the new value
            $temp = "";
            
            // loop through to add our spaces
            for ($n = 0; $n < get_string_pad_count($arrDisplay, $i); $n++) {
                $temp .= "-";
            }
            
            // see if we need to add a few dashes before
            if ((isset($arrDisplay[$i + 1]) && strlen($arrDisplay[$i]) < strlen($arrDisplay[$i + 1])) || (isset($arrDisplay[$i - 1]) && strlen($arrDisplay[$i]) < strlen($arrDisplay[$i - 1]))) {
                print "<!-- " . strlen($arrDisplay[$i]);
                if (isset($arrDisplay[$i + 1])) { print " " . strlen($arrDisplay[$i + 1]); }
                if (isset($arrDisplay[$i - 1])) { print " " . strlen($arrDisplay[$i - 1]); }
                print " -->\n";
                $temp = "--" . $temp;
            }
            
            // pad this to our string
            $temp .= $arrDisplay[$i];
            
            // update the array value
            $arrDisplay[$i] = "|--" . $temp;
        }*/
        
        // reverse the array assignment
        $arrDisplay = array_reverse($arrDisplay);
        
        /*
        [0] => 12-13-15-
        [1] => 12-13-15-
        [2] => 12-14-
        [3] => 12-14-15-
        [4] => 12-14-15-
        [5] => 12-13-15-
        */
        
        // loop through the array, and start to build our display
        for ($i = 5; $i >= 0; $i--) {
            // see if the previous line was set
            if (!empty($arrDisplay[$i + 1])) {
                // set the count
                $count = (strlen($arrDisplay[$i + 1]) - strlen($arrDisplay[$i]));
                
                // pad the count number with lines (string)
                for ($n = 1; $n <= $count; $n++) {
                    $arrDisplay[$i] = "-" . $arrDisplay[$i];
                }
            }
            
            // see if the previous line was set
            if (!empty($arrDisplay[$i - 1])) {
                // set the count
                $count = strlen($arrDisplay[$i - 1]);
                
                // pad the count number with lines (string)
                for ($n = 1; $n <= $count; $n++) {
                    $arrDisplay[$i] = $arrDisplay[$i] . "-";
                }
            }
        }
        
        // loop through our bottom strings to fix padding issues
        for ($i = 5; $i >= 2; $i--) {
            // set the count
            $count = (strlen($arrDisplay[0]) - strlen($arrDisplay[$i]));
            
            // pad the count number with lines (string)
            for ($n = 1; $n <= $count; $n++) {
                $arrDisplay[$i] = $arrDisplay[$i] . "-";
            }
        }
        
        // loop through and add our start and end stops
        foreach ($arrDisplay as $key => $string) {
            $arrDisplay[$key] = "&nbsp;&nbsp;&nbsp;&nbsp;|--" . $string . "-|";
        }
        
        // print the tab out
        $tab = implode("\n", $arrDisplay);
        
        print $tab;
    }
    
    // our function to loop through and return the number of space counts for tab lines
    function get_string_pad_count($arrDisplay, $start) {
        // set our count
        $count = 0;
        
        // loop through teh array and add to our count
        for ($i = $start; $i < count($arrDisplay); $i++) {
            $count += strlen($arrDisplay[$i]);
        }
        
        // return the count
        return $count;
    }
    
    // our function to loop through and return the number of space counts for tab lines
    function get_string_end_count($arrDisplay, $start) {
        // set our count
        $count = 0;
        
        // loop through teh array and add to our count
        for ($i = $start; $i < count($arrDisplay); $i++) {
            $count += strlen($arrDisplay[$i]);
        }
        
        // return the count
        return $count;
    }
    
    // our function to print the chord out for display
    function print_scale_map($notes, $strings, $root, $chord) {
        // create our string
        $chart = "";
        $arrRoots = array();
        
        // create our position array
        $arrNotes = array("C" => 0, "C#" => 1, "D" => 2, "D#" => 3, "E" => 4, "F" => 5, "F#" => 6,
                          "G" => 7, "G#" => 8, "A" => 9, "A#" => 10, "B" => 11);
        
        // loop through each string and out put our data
        for ($i = 1; $i <= 6; $i++) {
            // append the string name
            if (in_array($strings[$i][0], $notes)) {
                $chart .= "<i>" . $strings[$i][0] . "</i> |";
            } else {
                $chart .= $strings[$i][0] . " |";
            }
            
            // loop from our starting position up 5 strings
            for ($n = 0; $n < 13; $n++) {
                if ($n) {
                    // if this note is in our array, add it
                    if ($notes[0] == $strings[$i][$n]) {
                        // display it as a root note
                        $chart .= "-R-|";
                        
                        // if we're beyond the 2nd string, track our root notes
                        if ($i > 2 && $n < 12) {
                            $arrRoots[$i] = $n;
                        }
                    } else if (in_array($strings[$i][$n], $notes)) {
                        $chart .= "-o-|";
                    } else {
                        $chart .= "---|";
                    }
                } else {
                    // if we're beyond the 2nd string, track our root notes
                    if ($i > 2 && $root == $strings[$i][$n]) {
                        $arrRoots[$i] = $n;
                    }
                }
            }
            
            // end the row
            $chart .= "\n";
        }
        
        // print our the fret #'s
        for ($n = 0; $n < 13; $n++) {
            if ($n) {
                // if this note is in our array, add it
                if (strlen($n) == 1) {
                    // see if this fret is in our array
                    if (in_array($n, $arrRoots)) {
                        $chart .= "  $n ";
                    } else {
                        $chart .= "  $n ";
                    }
                } else {
                    if (in_array($n, $arrRoots)) {
                        $chart .= "  $n";
                    } else {
                        $chart .= "  $n";
                    }
                }
            } else {
                if (in_array($n, $arrRoots)) {
                    $chart .= "$n ";
                } else {
                    $chart .= "$n ";
                }
            }
        }
        
        // print out the string
        print $chart;
    }
    
    // the function to find notes on a string
    function find_string_notes($string, $note, $strings, $start, $end) {
        // loop through the notes in our string
        for ($i = $start; $i <= $end; $i++) {
            // if the note matches, let us know
            if ($strings[$string][$i] == $note) {
                return true;
            }
        }
        
        // if we've gotten this far, it wasn't found
        return false;
    }
?>
