<?php
// 這是網站會用到的 SQL 查尋

$qry1_album_info = "SELECT `id`, `title`, `description` FROM album WHERE id = ? LIMIT 1";
$qry1_album_media = "SELECT am.media_id, m.type, ms.location, ms.is_local, mm.description, am.description
FROM album_media am
    INNER JOIN media_storage ms  ON am.media_id = ms.media_id
    INNER JOIN media m           ON am.media_id = m.id
    INNER JOIN media_metadata mm ON am.media_id = mm.media_id
WHERE am.album_id = ?
ORDER BY am.sequence";
$qry2_user_session = "SELECT user_id FROM user_session WHERE session_key = ? AND last_active > ? LIMIT 1";
$qry1_media_file = "SELECT `is_private`, `location`, `is_local`, `mime_type` FROM media_storage WHERE media_id = ? LIMIT 1"
?>
