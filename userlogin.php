<?php
include ("util.php");
include ("queries.php");

$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";

if ($username && $password) {
    $conn = conn_db();
    $stat_user_password = $conn->prepare($qry2_user_password);
    $stat_user_password->bind_param("ss", $username, $password);
    $stat_user_password->execute();
    $result = $stat_user_password->get_result();
    if (mysqli_num_rows($result) > 1) {
        $ip = $_SERVER["REMOTE_ADDR"];
        $session = gen_session_cookie();
        $today_ts = time();
        $id = mysqli_fetch_array($result)[0];
        $stat_insert_user_sess = $conn->prepare($qry4_insert_user_session);
        $stat_insert_user_sess->bind_param("siis", $session, $id, $today_ts, $ip);
        $stat_insert_user_sess->execute();

        header("Set-Cookie: {$cki_user_session}=\"{$session}\"");
    } else {
        http_response_code(400);
    }
} else {
    http_response_code(400);
}