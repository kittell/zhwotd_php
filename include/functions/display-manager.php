<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
require_once 'wotd-functions.php';
require_once 'wotd-formatting.php';

function display_header() {
    include(DISPLAYS_DIR . '/part-header.php');
}

function display_wotd_navigation($d) {
    // DESCRIPTION: Create a navigation bar to show links to previous and next WOTD
    // INPUTS: date $d: anchor date for finding previous and next dates
    // OUTPUTS: $string result: html div containing links
    // DEPENDENCIES:
    //      - ./display_wotd_teaser
    //      - wotd-formatting/wrap_in_html_tag
    
    $prev_wotd = display_wotd_teaser($d, -1);
    $prev_wotd = '&lt; ' . $prev_wotd;
    $next_wotd = display_wotd_teaser($d, 1);
    $next_wotd = $next_wotd . ' &gt;';
    
    $prev_attribute_values['class'] = 'wotd-navigation-prev';
    $span_prev_wotd = wrap_in_html_tag('span', $prev_wotd, false, $prev_attribute_values);
    
    $next_attribute_values['class'] = 'wotd-navigation-spacer';
    $span_spacer = wrap_in_html_tag('span', '|', false, $next_attribute_values);
    
    $next_attribute_values['class'] = 'wotd-navigation-next';
    $span_next_wotd = wrap_in_html_tag('span', $next_wotd, false, $next_attribute_values);
    
    $div_content = $span_prev_wotd . "\n" . $span_spacer . "\n" . $span_next_wotd;
    $div_attribute_values['class'] = 'wotd-navigation';
    $result = wrap_in_html_tag('div', $div_content, true, $div_attribute_values);
    
    return $result;
}

function display_wotd_teaser($d, $offset) {
    // DESCRIPTION: Show the date, WOTD, and link for a given WOTD. Designed for
    //       the previous/next WOTD links on WOTD entries.
    // INPUTS:
    //       - date $d: anchor date for WOTD
    //       - int $offset: number of days offset from $d
    // OUTPUTS: string $result; format: YYYY-MM-DD: WOTD
    // DEPENDENCIES:
    //       - wotd-functions/get_offset_date
    //       - wotd-formatting/format_wotd_teaser
    
    $d_offset = get_offset_date($d, $offset);
    $wotd = format_wotd_teaser($d, $offset, true, '');
    
    $result = $d_offset . ': ' . $wotd;
    
    return $result;
}

function get_css_link($css_file_base) {
    $css_file = CSS_DIR . '/' . $css_file_base . '.css';
    // Auto-versioning to bypass caching
    $css_file = auto_version($css_file);
    $css_link = '<link rel="stylesheet" href="' . $css_file .'" type="text/css">';
    return $css_link;
}

function get_stylesheets($page_type) {
    $css_link_main = get_css_link('main');
    $css_link_wotd = get_css_link('wotd');
    
    $result = $css_link_main;
    switch ($page_type) {
        case 'index':
            $result .= "\n" . $css_link_wotd;
            break;
        case 'wotd':
            $result .= "\n" . $css_link_wotd;
            break;
    }
    return $result;
}

function auto_version($file) {
    // DESCRIPTION: Auto-versioning of filenames to overcome caching of css files
    //      Source: https://stackoverflow.com/a/118886/752784
    if(strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'] . $file))
        return $file;
        
        $mtime = filemtime($_SERVER['DOCUMENT_ROOT'] . $file);
        return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
}