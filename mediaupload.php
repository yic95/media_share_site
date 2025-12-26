<?php
//// mediaupload.php -- 媒體上傳功能與介面
include ("queries.php");
include ("util.php");

/*
error_reporting(E_ALL);
ini_set('display_errors',1);
*/

$str_media_upload_textarea = "兩三句就好";
$accept = [
    "mp3", "wav", "aac", "flac", "ogg",
    "wma", "m4a", "aiff", "alac", "opus",
    
    "jpg", "jpeg", "png", "gif", "bmp",
    "tiff", "tif", "webp", "svg", "heic",
    
    "mp4", "mkv", "avi", "mov", "wmv",
    "flv", "webm", "mpeg", "mpg", "3gp"
];

$accept_str = "";
{
    $not_first = 0;
    foreach ($accept as $s) {
        if ($not_first) {
            $accept_str = $accept_str . ",";
        }
        $not_first = 1;
        $accept_str = $accept_str . $s;
    }
}

/// 上傳功能部分
$is_error = 0;
$message = "";
$alt_text_value = $str_media_upload_textarea;
$should_has_file = $_POST["should_has_file"];
if ($should_has_file) {
    $upload_succeed = 0;
    $upload_file_attr = $_FILES["media"];
    
    // 確定檔案上傳沒有出錯
    if (is_null($upload_file_attr)) {
        $is_error = 1;
        $message = "沒有檔案";
    } else if (is_array($upload_file_attr["error"])) {
        $is_error = 1;
        $message = "有多個檔案";
    } else {
        switch ($upload_file_attr["error"]) {
        case UPLOAD_ERR_OK:
            // 無錯誤
        break;
        
        case 1:
            $is_error = 1;
            $message = "檔案太大";
            /* FALLTHRU */
        case 2:
            $message = "檔案太大.";
            break;

        case 3: case 4: case 5: case 6: case 7: case 8:
            $is_error = 1;
            $message = "上傳過程出錯." . $upload_file_attr["error"];
            break;
            
            default:
            $is_error = 1;
            $message = "內部錯誤";
            break;
        }
    }
    if (!$is_error)
        $upload_succeed = 1;
    
    if (!$is_error && !$_POST["description"] || $_POST["description"] == $str_media_upload_textarea) {
        $is_error = 1;
        $message = "糟糕的媒體說明文字";
    }

    // 確定上傳的檔案真的是媒體
    $mime_type = "";
    if (!$is_error) {
        $mime_type = mime_content_type($upload_file_attr["tmp_name"]);
        if (!$mime_type) {
            $is_error = 1;
            $message = "無法確定檔案類別";
        } else if (!in_array(str_split($mime_type, 5)[0], ["image", "audio", "video"])) {
            $is_error = 1;
            $message = "檔案類別錯誤";
        }
    }

    $conn = 0;
    if (!$is_error) {
        $conn = conn_db(0);
        if (!$conn) {
            $is_error = 1;
            $message = "資料庫連線錯誤";
        }
    }

    // 確定本機沒有同樣的圖片
    $new_file_name = "";
    if (!$is_error) {
        $ext_name = strip_but_file_extension(basename($upload_file_attr["name"]));
        $new_file_name = hash_file("sha256", $upload_file_attr["tmp_name"])
                         . ($ext_name == ""? "" : ".")
                         . $ext_name;
        $stat_file_hash = $conn->prepare($qry1_file_hash);
        $stat_file_hash->bind_param("s", $new_file_name);
        $stat_file_hash->execute();
        $result = $stat_file_hash->get_result();
        $rescnt = mysqli_num_rows($result);
        if ($rescnt >= 1) {
            $is_error = 1;
            $message = "有{$rescnt}個同樣的媒體";
        }
    }

    // 確定移檔成功
    if (!$is_error) {
        if (!file_exists($str_media_dir)) {
            mkdir($str_media_dir, 0744);
        }
        if (!move_uploaded_file($upload_file_attr["tmp_name"], "$str_media_dir/$new_file_name")) {
            $is_error = 1;
            $message = "移檔過程出錯（{$upload_file_attr["name"]}: {$new_file_name}，"
                        . "{$str_media_dir}/{$new_file_name}，" . (is_writable("$str_media_dir")? "可寫）" : "不可寫）");
        }
    }

    if (!$is_error) {
        $user_cookie = $_COOKIE[$cki_user_session];
        $stat_user_session = $conn->prepare($qry2_user_session);
        $stat_user_session->bind_param("ii", $user_cookie, get_session_constraint($COOKIE_ACTIVE_PERIOD));
        $stat_user_session->execute();
        $result_user_session = $stat_user_session->get_result();

        $userid = 0;
        if (mysqli_num_rows($result_user_session) > 0) {
            $userid = mysqli_fetch_array($result_user_session)[0];
        }

        $file_size = $upload_file_attr["size"];
        $file_title = $upload_file_attr["name"];
        $file_type = str_split($mime_type, 5)[0];
        $file_is_private = 0;
        if ($_POST["is_private"])  // is_private == "on"
            $file_is_private = 1;
        $file_desc = strip_tags($_POST["description"]) ?? "No Description";  // 這應該要有值
        $is_local = 1;

        mysqli_begin_transaction($conn);
        try {
            $file_id = mysqli_fetch_array(mysqli_query($conn, $qry_media_id))[0] ?? 0;
            
            // INSERT INTO media (`id`, `type`, `title`, `is_local`, `is_private`, `location`, `file_size`, `mime_type`)
            $stat_insert_media = $conn->prepare($qry8_insert_media);
            $stat_insert_media->bind_param("issiisis", $file_id, $file_type,
                                        $file_title, $is_local, $file_is_private,
                                        $new_file_name, $file_size, $mime_type);
            $stat_insert_media->execute();

            $stat_insert_media_create = $conn->prepare($qry3_insert_media_create);
            $stat_insert_media_create->bind_param("iis", $file_id, $userid, date("Y-m-d H:i:s"));
            $stat_insert_media_create->execute();

            $stat_insert_media_meta = $conn->prepare($qry2_insert_media_meta);
            $stat_insert_media_meta->bind_param("is", $file_id, $file_desc);
            $stat_insert_media_meta->execute();
            mysqli_commit($conn);
        } catch (mysqli_sql_exception $exception) {
            $is_error = 1;
            $message = "資料庫錯誤";
            mysqli_rollback($conn);
            throw $exception;
        }
    }

    if ($is_error && $upload_succeed) {
        unlink($upload_file_attr["tmp_name"]);
    }
}
if ($is_error)
    $alt_text_value = $_POST["description"];

?><!DOCTYPE html>
<html>
    <head>
        <title>上傳媒體</title>
        <script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById("submit").disabled = true;
}, false);

function enableSubmitButton() {
    document.getElementById("submit").disabled = false;
}
        </script>
    </head>
    <body>
        <?php
        if ($is_error) {
            echo "<div id=\"errmsg\">上次上傳檔案有錯誤：{$message}</div>";
        }
        ?>
        <form enctype="multipart/form-data" action="mediaupload.php" method="POST">
            <input type="hidden" name="should_has_file" value="1">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?= $int_max_upload_size; ?>" accept="<?= $accept_str; ?>">
            上傳媒體：<input required type="file" name="media"> <br />
            描述文字：<input required onchange="enableSubmitButton()" type="text" name="description" value="<?= $alt_text_value; ?>"> <br />
            <input type="checkbox" name="is_private"> 限定僅有註冊使用者能檢視<br /> 
            <input id="submit" type="submit" value="上傳媒體">
        </form>
    </body>
</html>
