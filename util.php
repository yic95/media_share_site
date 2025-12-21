<?php

function text_to_html($text) {
    $result = "";
    $blank_line_count = 0;
    $is_before_text = 1;
    foreach (preg_split("/\r\n|\n|\r/", $text) as $line) {
        if ($line == "") {
            $blank_line_count++;
            if ($is_before_text == 1) {
                $result = $result . "<br>";
            } else if ($blank_line_count == 1) {
                $result = $result . "</p>";
            } else if ($blank_line_count > 1) {
                $result = $result . "<br>";
            }
        } else {
            $is_before_text = 0;
            if ($blank_line_count == 0) {
                $result = $result . "<br>";
            } else {
                $result = $result . "<p>";
            }
            $result = $result . $line;
            $blank_line_count = 0;
        }
    }
    if ($blank_line_count == 0 && $is_before_text == 0) {
        $result = $result . "</p>";
    }
    return $result;
}
?>