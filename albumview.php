<?php
include ("queries.php");

$passwd = getenv("MEDIA_SHARE_PASSWORD");
$conn = mysqli_connect("localhost", "media_share", $passwd, "media_share");
$album_id = $_GET["albumid"];

$stat_album_info = $conn->prepare_statement($qry1_album_info);
if ($has_id) {
    $stat_album_info->bind_param("i", $album_id);
    $stat_album_info->execute();
}
$result = $stat_album_info->get_result();
$album_metadata = mysqli_fetch_array($result);
$num_row = mysqli_num_rows($result);

$title = "";

if ($num_row == 0 || $album_id == null) {
    $title = "找不到合輯！";
} else {
    $title = $album_metadata[1];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <meta charset=utf-8>
    </head>
    <body>
        <?php
if ($num_row == 0 || $album_id == null) {
    echo <<< DOC
    <h1> 找不到合輯！ </h1>
    <p>
    本合輯可能已遭作者移除。您可以點擊「上一頁」按鈕，或者點擊以下連接：<br />
    <a href="index.php">回首頁</a>
    </p>
DOC;
} else {
    $stat_album_media = $conn->prepare_statement($qry1_album_media);
    $stat_album_media->bind_param("i", $album_id);
    $stat_album_media->execute();
    $media = $stat_album_media->get_result();

    // 以下是媒體的輪盤
    $cnt = 1;
    echo "<div id=\"media_slide_container\">";
    while ($row = mysqli_fetch_array()) {
        // $row: media id, media type, media location, alt text, album story
        $media_view_url = "mediaview.php?mediaid=" . $row[0];
        $media_location_url = "";
        if ($row[3] == 0) {  // is_local == 0
            $media_location_url = $row[2];  // location
        } else {
            $media_location_url = "mediaget.php?mediaid=" . $row[0];
        }

        echo "<div id=\"media_slide_" . $cnt . "\" class=\"media_slide\">";
        echo "<h1>第 " . $cnt . " 段</h1>";
        if ($row[1] == "image") {
            echo "<a href=\"" . $media_view_url . "\">";
            echo "<img src=\"" . $media_location_url . "\" alt=\"" . $row[3] . "\">";
            echo "</a>";
        } else if ($row[1] == "audio") {
            echo "<audio controls src=\"" . $media_location_url . "\">";
            echo $row[3];
            echo "<br />";
            echo "<a href=\"" . $media_location_url . "\">音訊下載連接</a>";
            echo "</audio>";
            echo "<a href=\"" . $media_view_url . "\">詳細資料</a>";
        } else {  // video
            echo "<video controls src=\"" . $media_location_url . "\">";
            echo $row[3];
            echo "<br />";
            echo "<a href=\"" . $media_location_url . "\">影片下載連接</a>";
            echo "</video>";
            echo "<a href=\"" . $media_view_url . "\">詳細資料</a>";
        }
        echo "</div>";
    }
    echo "</div>";
}
        ?>
    </body>
</html>
