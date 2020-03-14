<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/config.php');

/* Handle database connections */

function db_connect() {
    // DESCRIPTION: Open database connection
    $db_login = get_db_login();
    $conn = new mysqli($db_login['host'], $db_login['user'], $db_login['password'], $db_login['db']);
    if ($conn->connect_error) { die("Fatal Error"); }
    $conn->set_charset("utf8");
    return $conn;
}

function db_close($conn) {
    // DESCRIPTION: Close database connection
    $conn->close();
}


/* QUERIES */
    
function single_query($query) {
    // DESCRIPTION: Retrieve records from database
    // INPUT: string $q: prebuilt SELECT/FROM/WHERE type query
    // OUTPUT: $row: entry from database with word info
    
    //TODO: how does this handle multiple records?
    
    $conn = db_connect();
    $result = $conn->query($query);
    if (!$result) { die("Fatal Error"); }
    
    $row = $result->fetch_array(MYSQLI_ASSOC);
    
    $result->close();
    db_close($conn);
    
    return $row;
}

function update_query($query) {
    $conn = db_connect();
    $result = $conn->query($query);
    if (!$result) { die("Failed to update"); }
    db_close($conn);
}


/*  QUERY BUILDERS */

//TODO: query builders