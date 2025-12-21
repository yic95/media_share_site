<?php
include ("queries.php");
include ("util.php");

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
    echo "<h1>" . $title . "</h1>";
    $stat_album_media = $conn->prepare_statement($qry1_album_media);
    $stat_album_media->bind_param("i", $album_id);
    $stat_album_media->execute();
    $media = $stat_album_media->get_result();
    $media_count = mysqli_num_rows($media);

    // 以下是媒體的輪盤
    $cnt = 1;
    echo "<div id=\"media_slide_container\"><main>";
    while ($row = mysqli_fetch_array()) {
        // SELECT am.media_id, m.type, ms.location, ms.is_local, mm.description, am.description
        $media_view_url = "mediaview.php?mediaid=" . $row[0];
        $media_location_url = "";
        if ($row[3] == 0) {  // is_local == 0
            $media_location_url = $row[2];  // location
        } else {
            $media_location_url = "mediaget.php?mediaid=" . $row[0];
        }

        echo "<div id=\"media_slide_" . $cnt . "\" class=\"media_slide\">";

        echo "<h2>" . $cnt . "/" . $media_count . "</h2>";
        
        // TODO 要用CSS把這float到左邊。
        echo "<div id=\"media_slide_media_\"" . $cnt . "\" class=\"media_slide_media\">";
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
        echo "</div>";  // media_slide_media

        echo text_to_html($row[4]);
        
        echo "</div>";  // media_slide
    }
    echo "</main></div>";  // media_slide_container
}
        ?>
    </body>
</html>
