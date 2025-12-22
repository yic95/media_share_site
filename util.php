<?php

function text_to_html($text) {
    $result = "";
    $blank_line_count = 1;
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

function media_element($type, $view, $src, $alt) {
    $result = "";
    if ($type == "image") {
        $result = "<a href=\"{$view}\"><img src=\"{$src}\" alt=\"" . htmlentities($alt) . "\"></a>";
    } else if ($type == "audio") {
        $result = "<audio controls src=\"{$src}\">"
                        . strip_tags($alt)
                        . "<br /><a href=\"{$src}\">音訊下載連接</a></audio>"
                    . "<br />"
                    . "<a href=\"{$view}\">詳細資料</a>";
    } else {  // video
        $result = "<video controls src=\"{$src}\">"
                        . strip_tags($alt)
                        . "<br /><a href=\"{$src}\">影片下載連接</a></video>"
                    . "<br />"
                    . "<a href=\"{$view}\">詳細資料</a>";
    }
    return $result;
}
?>