<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include ("util.php");
include ("queries.php");

$is_error = 0;

$username = strtolower($_POST["username"] ?? "");
$email = $_POST["email"];
$password = $_POST["password"];
$description = strip_tags($_POST["desc"] ?? "");

if (!$username || !$email || !$password || !$description)
    $is_error = 1;

$ip = "";
$today_ts = 0;
$today = "";
$session = "";
$new_id = 0;

if (!$is_error) {
    $ip = $_SERVER["REMOTE_ADDR"];
    $today_ts = time();
    $today = date("Y-m-d H:i:s", $today_ts);
    $session = gen_session_cookie();
    $new_id = 0;
}

if (!$is_error) {
    $conn = conn_db();
    $stat_username = $conn->prepare($qry1_user_username);
    $stat_username->bind_param("s", $username);
    $stat_username->execute();
    if (mysqli_num_rows($stat_username->get_result()) > 0)
        $is_error = 1;
}

if (!$is_error) {
    mysqli_begin_transaction($conn);
    try {
        $new_id = mysqli_fetch_array(mysqli_query($conn, $qry_user_id))[0];
        $stat_insert_user = $conn->prepare($qry4_insert_user);
        $stat_insert_user->bind_param("isss", $new_id, $username, $email, $password);
        $stat_insert_user->execute();
        $stat_insert_user_prof = $conn->prepare($qry3_insert_user_profile);
        $stat_insert_user_prof->bind_param("iss", $new_id, $description, $today);
        $stat_insert_user_prof->execute();
        $stat_insert_user_sess = $conn->prepare($qry4_insert_user_session);
        $stat_insert_user_sess->bind_param("siis", $session, $new_id, $today_ts, $ip);
        $stat_insert_user_sess->execute();
        mysqli_commit($conn);
    } catch (mysqli_sql_exception $exception) {
        mysqli_rollback($conn);
        throw $exception;
    }

    header("Set-Cookie: {$cki_user_session}=\"{$session}\"");
}
