<?php

// 這是確保只有註冊使用者能看到本站圖片
include("queries.php");
include("util.php");

$conn = conn_db();

$media_id = $_GET["mediaid"];
if ($media_id == null) {
    http_response_code(400);
    exit(0);
}
$user_cookie = $_COOKIE[$cki_user_session];

$stat_media_file = $conn->prepare_statement($qry1_media_file);
$stat_media_file->bind_param("i", $media_id);
$stat_media_file->execute();
$mresult = $stat_media_file->get_result();
$mcount = mysqli_num_rows($mresult);
$minfo = mysqli_fetch_array($mresult);

if ($mcount == 0 || $minfo[2] == 1) {
    http_response_code(404);
    exit(0);
} else if (minfo[0] == 1) {
    $stat_user_session = $conn->prepare_statement($qry2_user_session);
    $stat_user_session->bind_param("ii", $user_cookie, get_session_constraint($COOKIE_ACTIVE_PERIOD));
    $stat_user_session->execute();
    $is_valid = mysqli_num_rows($stat_user_session->get_result());

    if ($is_valid == 0) {
        http_response_code(405);
        exit(0);
    }
}

$fp = fopen($str_media_dir . basename($minfo[1]), 'rb');
http_response_code(200);
header("Content-Type: " . $minfo[3]);
header("Content-Length: " . filesize($minfo[1]));
fpassthru($fp);
exit(0);
