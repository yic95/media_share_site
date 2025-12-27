<?php

include ("secret.php");  // provides get_db_passwd()

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

function conn_db($exit_on_error = 1, $header = 1) {
    $conn = mysqli_connect("localhost", "media_share", get_db_passwd(), "media_share");
    if (!$conn && $exit_on_error) {
        if ($header) {
            http_response_code(501);
        }
        exit();
    }
    mysqli_set_charset($conn, "utf8mb4");
    return $conn;
}

function get_session_constraint($active_period) {
    return time() - $active_period;
}

function get_session_user($conn, $session) {
    $conn->prepare("SELECT user_id FROM user_session WHERE session_key = ? AND last_active > ? LIMIT 1");
    $conn->bind_param("si", $session, get_session_constraint(172800));  // 2 days
    $conn->execute();
    $result = $conn->get_result();
    if (mysqli_num_rows($result) == 0)
        return false;
    else 
        mysqli_fetch_array($result)[0];
}

function gen_session_cookie() {
    return bin2hex(random_bytes(32));
}

function strip_file_extension($fname) {
    return preg_replace("/\.[^\.]*$/", "", $fname);
}

// NOTE this doesn't keep the dot.
function strip_but_file_extension($fname) {
    return preg_replace("/^([^\.]*\.)*/", "", $fname);
}