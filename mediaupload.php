<?php
//// mediaupload.php -- 媒體上傳功能與介面
include ("queries.php");
include ("util.php");

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
if ($should_has_file) {
    $should_has_file = $_POST["should_has_file"];
    $is_error = 0;
    $upload_succeed = 0;
    $message = "";
    $upload_file_attr = $_FILES["media"];
    
    // 確定檔案上傳沒有出錯
    if (is_null($upload_file_attr)) {
        $is_error = 1;
        $message = "沒有檔案";
    } else if (is_array($upload_file_attr)) {
        $is_error = 1;
        $message = "有多個檔案";
    } else {
        switch ($upload_file_attr["error"]) {
            case UPLOAD_ERR_OK:
                // 無錯誤
            break;
            
            case 1: case 2:
                $is_error = 1;
            $message = "檔案太大";
            break;

        case 3: case 4: case 5: case 6: case 7: case 8:
            $is_error = 1;
            $message = "上傳過程出錯";
            break;
            
            default:
            $is_error = 1;
            $message = "其他錯誤";
            break;
        }
    }
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


    // 確定本機沒有同樣的圖片
    $new_file_name = "";
    if (!$is_error) {
        $new_file_name = $str_media_dir
                         . hash_file("sha256", $upload_file_attr["tmp_name"])
                         . "."
                         . strip_but_file_extension(basename($upload_file_attr["name"]));
        if (file_exists($new_file_name)) {
            $is_error = 1;
            $message = "有同樣的媒體";
        }
    }

    // 檢查通過，確定上傳
    if (!$is_error) {
        $user_cookie = $_COOKIE[$cki_user_session];
        $conn = conn_db();
        $stat_user_session = $conn->prepare($qry2_user_session);
        $stat_user_session->bind_param("ii", $user_cookie, get_session_constraint($COOKIE_ACTIVE_PERIOD));
        $stat_user_session->execute();
        $result_user_session = $stat_user_session->get_result();
        
        $userid = 0;  // TODO 資料庫：把 user.id = 0 的資料移到其他地方
        if (mysqli_num_rows($result_user_session) > 0) {
            $userid = mysqli_fetch_array($result_user_session)[0];
        }

        $file_size = $upload_file_attr["size"];
        $file_title = $upload_file_attr["name"];
        $file_type = str_split($mime_type, 5)[0];
        $file_is_private = $_POST["is_private"] ?? 0;
        $file_desc = strip_tags($_POST["description"]) ?? "No Description";  // 這應該要有值
        $file_id = mysqli_fetch_array(mysqli_query($conn, $qry_media_id))[0] ?? 0;

        
        if (move_uploaded_file($upload_file_attr["tmp_name"], $new_file_name)) {
            // INSERT INTO media (`id`, `type`, `title`, `is_local`, `is_private`, `location`, `file_size`, `mime_type`)
            $stat_insert_media = $conn->prepare($qry8_insert_media);
            $stat_insert_media->bind_param("issiisis", $file_id, $file_type,
                                        $file_title,/* is local = */1 ,
                                        $new_file_name, $file_size, $mime_type);
            $stat_insert_media->execute();
            $stat_insert_media_create = $conn->prepare($qry3_insert_media_create);
            $stat_insert_media_create->bind_param("iis", $file_id, $userid, date("Y-m-d H:i:s"));
            $stat_insert_media_create->execute();
        } else {
            $is_error = 1;
            $message = "上傳過程出錯";
        }
    }

    if ($is_error && $upload_succeed) {
        unlink($upload_file_attr["tmp_name"]);
    }
}

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
        <form enctype="multipart/form-data" action="mediaupload.php" method="POST">
            <input type="hidden" name="should_has_file" value="1">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?= $int_max_upload_size; ?>" accept="<?= $accept_str; ?>">
            上傳媒體：<input required type="file" name="media"> <br />
            描述文字：<input required onclick="enableSubmitButton()" type="text" name="description" value="<?= $str_media_upload_textarea; ?>"> <br />
            <input type="checkbox" name="is_private"> 限定僅有註冊使用者能檢視<br /> 
            <input id="submit"type="submit" value="上傳媒體">
        </form>
    </body>
</html>
