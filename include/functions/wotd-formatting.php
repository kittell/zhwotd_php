<?php

require_once 'wotd-functions.php';
    
/* General methods */
function get_wotd_url($d) {
    // TODO: make sure that date is in YYYY-MM-DD format
    $base_url = "http://" . $_SERVER['SERVER_NAME'];
    $path = get_wotd_path($d);
    $result = $base_url . '/' . $path;
    return $result;
}

function get_wotd_path($d) {
    $result = 'wotd/' . $d;
    return $result;
}


function edit_attribute_values($attribute_values, $attribute, $new_value, $change_type = 'replace') {
    if ($change_type == 'replace') {
        $attribute_values[$attribute] = $new_value;
    } elseif ($change_type == 'append') {
        if (array_key_exists($attribute, $attribute_values)) {
            // Add the new value to the old value
            $attribute_values[$attribute] = $attribute_values[$attribute] . ' ' . $new_value;
        } else {
            $attribute_values[$attribute] = $new_value;
        }
    }
    
    return $attribute_values;
}

/* Full WOTD entries */

function format_wotd_part($dict_row, $part, $tag, $class='') {
    $wotd_part = get_query_part($dict_row, $part);
    
    $wotd_attribute_values = NULL;
    if ($class != '') {
        $wotd_attribute_values['class'] = $class;
    }
    
    $result = wrap_in_html_tag($tag, $wotd_part, false, $wotd_attribute_values);
    
    return $result;
}


function format_wotd_entry($d, $offset = 0, $extra_class = '') {
    $this_d = get_offset_date($d, $offset);
    $wotd_entry = get_wotd_entry($this_d, 0);
    
    // Get parts of wotd
    $wotd_term = format_wotd_part($wotd_entry, 'term', 'p', 'term');
    $wotd_pinyin = format_wotd_part($wotd_entry, 'pinyin', 'p', 'pinyin');
    $wotd_definition = format_wotd_part($wotd_entry, 'definition', 'p', 'definition');
    
    // Format header
    $wotd_heading = $this_d . ': ' . get_query_part($wotd_entry, 'term');
    $heading_attribute_values['class'] = 'wotd-header';
    if ($extra_class != '') {
        $heading_attribute_values = edit_attribute_values($heading_attribute_values, 'class', $extra_class, 'append');
    }    
    $wotd_heading = wrap_in_html_tag('h2', $wotd_heading, false, $heading_attribute_values);

    
    $div_content = $wotd_heading . "\n" . $wotd_term . "\n" . $wotd_pinyin . "\n" . $wotd_definition;
    $div_attribute_values['id'] = 'wotd-' . $this_d;
    $div_attribute_values['class'] = 'wotd';
    
    $result = wrap_in_html_tag('div', $div_content, true, $div_attribute_values);
    
    return $result;
}


/* WOTD teasers */

function format_wotd_teaser($d, $offset = 0, $with_link = false, $extra_class = '') {
    $this_d = get_offset_date($d, $offset);
    $wotd_entry = get_wotd_entry($d, $offset);
    
    $result = "";
    $teaser_attribute_values = array();
    $term .= $wotd_entry['term'];
    
    if ($with_link) {
        $url = get_wotd_url($this_d);
        $a_attribute_values['href'] = $url;
        $result = wrap_in_html_tag('a', $term, false, $a_attribute_values);
    } else {
        $result = $term;
    }
    
    // Add extra classes sent in function call
    if ($extra_class != '') {
        $teaser_attribute_values = edit_attribute_values($teaser_attribute_values, 'class', $extra_class, 'append');
    }
    
    $result = wrap_in_html_tag('span', $result, false, $teaser_attribute_values);
    
    return $result;
}

function format_series_of_teasers($d, $offset = 0, $n_teasers = 1, $forward = false, $sep = ', ', $extra_class = '', $leader = '') {
    
    $d_series = get_date_series($d, $offset, $n_teasers, $forward);

    $result = $leader;
    $i = -1;
    foreach ($d_series as $this_d) {
        $i++;
        if ($i > 0) {
            $result .= $sep;
        }
        $result .= format_wotd_teaser($this_d, 0, true, $extra_class);
    }
    
    $result = wrap_in_html_tag('p', $result);
    
    return $result;
}


/* HTML helpers */

function build_html_tag($tag, $end = false, $attribute_array = NULL) {
    // DESCRIPTION: bundle an HTML tag in required angle brackts
    // INPUTS:
    //      - string $tag: 
    //      - bool $end: if true, add the ending slash to the tag; otherwise, do nothing
    // OUTPUTS: string $result: HTML tag wrapped in angle brackets
    
    // TODO: handle parameters like id, class, etc.
    
    // Start out witih blank options
    $attribute_values = '';
//     $tag_end = '';
//     $tag_class = '';
//     $tag_id = '';
//     $tag_other = '';
    
    if ($end) {
        $tag_end = '/';
    } else {
        if (isset($attribute_array)) {
        // Only handle class, id, etc., for start tags
        }
        
        foreach ($attribute_array as $attribute => $value) {
            $attribute_values .= ' ' . $attribute . '="' . $value . '"';
        }
    }
    
    // Put it all together
    $result = '<' . $tag_end . $tag . $attribute_values . '>';
    
    return $result;
}

function wrap_in_html_tag($tag, $content, $newlines = false, $attribute_values = NULL) {
    // DESCRIPTION: Simply, wrap some HTML tags around some content
    // INPUTS:
    //      - string $tag: 
    //      - string $content: 
    //      - bool $newlines: if true, put the tags and content on separate lines;
    //          otherwise, put them on the same line
    // OUTPUTS: string $result: content wrapped in specified HTML tags
    
    // Build the tags
    $pre_tag = build_html_tag($tag, false, $attribute_values);
    $post_tag = build_html_tag($tag, true);
    
    // Add newlines if specified
    if ($newlines) {
        $result = "\n" . $content . "\n";
    } else {
        $result = $content;
    }
    
    $result = $pre_tag . $result . $post_tag;
    
    return $result;
}