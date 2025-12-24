<?php

// 這是確保只有註冊使用者能看到本站圖片
include("queries.php");
include("util.php");

// error_reporting(E_ALL);
// ini_set('display_errors',1);


$conn = conn_db();

$media_id = $_GET["mediaid"];
if ($media_id == null) {
    http_response_code(400);
    exit(0);
}
$user_cookie = $_COOKIE[$cki_user_session];

$stat_media_file = $conn->prepare($qry1_media_file);
$stat_media_file->bind_param("i", $media_id);
$stat_media_file->execute();
$mresult = $stat_media_file->get_result();
$mcount = mysqli_num_rows($mresult);
$minfo = mysqli_fetch_array($mresult);

if ($mcount == 0) {
    http_response_code(404);
    exit(0);
} else if ($minfo[2] == 0) {  // is_local = 0
    http_response_code(410);
    exit(0);
} else if ($minfo[0] == 1) {  // is_private = 1
    $stat_user_session = $conn->prepare($qry2_user_session);
    $stat_user_session->bind_param("ii", $user_cookie, get_session_constraint($COOKIE_ACTIVE_PERIOD));
    $stat_user_session->execute();
    $is_valid = mysqli_num_rows($stat_user_session->get_result());

    if ($is_valid == 0) {
        http_response_code(403);
        exit(0);
    }
}

$fp = fopen($str_media_dir . "/" . basename($minfo[1]), 'rb');
if (!$fp) {
    http_response_code(500);
    echo "開檔失敗";
} else {
    http_response_code(200);
    header("Content-Type: " . $minfo[3]);
    header("Content-Length: " . $minfo[4]);
    fpassthru($fp);
}
exit(0);
