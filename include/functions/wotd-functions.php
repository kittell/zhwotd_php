<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
require_once('queries.php');


function get_cron_key() {
    return get_cron_key_config();
}

// Queries

function get_wotd_by_date($d) {
    // DESCRIPTION: Retrieve the word of the day for a given date.
    // INPUT: date $d
    // OUTPUT: $row: entry from database for given date's word

    // TODO: decide how to handle missing date
    // If the date is blank, use today's date.
//     if (!isset($d)) {
//         $d_2 = date("Y-m-d");
//     } else {
//         $d_2 = date("Y-m-d", strtotime($d));
//     }
    
    $query = "SELECT * FROM wotd WHERE date='$d'";
    $row = single_query($query);
    return $row;
    
}

function get_word_from_dict($word) {
    // DESCRIPTION: Retrieve info about a word from database
    // INPUT: string $word
    // OUTPUT: $row: entry from database with word info
    
    $query = "SELECT * FROM dictionary WHERE term='$word'";
    $row = single_query($query);
    return $row;
}



function get_offset_date($d, $offset) {
    // DESCRIPTION: 
    // INPUT: 
    // OUTPUT: 
    // DEPENDENCIES: none
    $str_offset = $offset . ' day';
    $d_offset = date('Y-m-d', (strtotime($str_offset, strtotime($d))));
    return $d_offset;
}

function get_query_part($query_row, $part) {
    // TODO: handle failure
    // TODO: maybe this should be in wotd-functions.php
    $result = $query_row[$part];
    return $result;
}


function get_wotd($d, $offset = 0) {
    //TODO: what to do if there is no entry for that date?
    
    // Account for offset in date
    if ($offset == 0) {
        $d_offset = $d;
    } else {
        $d_offset = get_offset_date($d, $offset);
    }
    
    $wotd_row = get_wotd_by_date($d_offset);
    $word = $wotd_row['word'];
    return $word;
}

function get_wotd_series($d, $offset = 0, $n = 1, $forward = false) {
    // Setup iterator
    $i = -1;
    $x = -1;
    if ($forward) {
        $x = 1;
    }
    
    while ($i < $n) {
        $i++;
        $this_offset = $x * $i + $offset;
        $this_d = get_offset_date($d, $this_offset);
        $wotd_array[$this_d] = get_wotd($this_d, 0);
        
    }
    
    return $wotd_array;
}

function get_date_series($d, $offset = 0, $n = 1, $forward = false) {
    // Setup iterator
    $i = -1;
    $x = -1;
    if ($forward) {
        $x = 1;
    }
    
    while ($i < $n) {
        $i++;
        $this_offset = $x * $i + $offset;
        $this_d = get_offset_date($d, $this_offset);
        $wotd_array[$i] = $this_d;
        
    }
    
    return $wotd_array;
}

function get_wotd_entry($d, $offset = 0) {
    // Return a full Word of the Day entry
    $word = get_wotd($d, $offset);
    $dict_entry = get_word_from_dict($word);
    return $dict_entry;
}