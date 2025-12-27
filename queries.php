<?php
// 這是網站會用到的 SQL 查尋

$qry1_album_info = "SELECT `id`, `title`, `description` FROM album WHERE id = ? LIMIT 1";
$qry1_album_media = "SELECT am.media_id, m.type, m.location, m.is_local, mm.description, am.description
FROM album_media am
    INNER JOIN media m           ON am.media_id = m.id
    INNER JOIN media_metadata mm ON am.media_id = mm.media_id
WHERE am.album_id = ?
ORDER BY am.sequence";
$qry8_insert_media = "INSERT INTO media (`id`, `type`, `title`, `is_local`, `is_private`, `location`, `file_size`, `mime_type`)
VALUES
(?, ?, ?, ?, ?, ?, ?, ?)";
$qry2_insert_media_meta = "INSERT INTO media_metadata (`media_id`, `description`) VALUES (?, ?)";
$qry3_insert_media_create = "INSERT INTO media_create (`media_id`, `user_id`, `create_date`) VALUES (?, ?, ?)";
$qry4_insert_user = "INSERT INTO user (`id`, `username`, `email_address`, `password_hash`) VALUES (?, ?, ?, SHA2(?, 256))";
$qry3_insert_user_profile = "INSERT INTO user_profile (`user_id`, `description`, `create_date`) VALUES (?, ?, ?)";
$qry4_insert_user_session = "INSERT INTO user_session (`session_key`, `user_id`, `last_active`, `ip`) VALUES (?, ?, ?, ?)";
$qry_user_id = "SELECT IFNULL(MAX(id) + 1, 0) FROM user";
$qry_media_id = "SELECT IFNULL(MAX(id) + 1, 0) FROM media";
$qry1_media_file = "SELECT `is_private`, `location`, `is_local`, `mime_type`, `file_size` FROM media WHERE id = ? LIMIT 1";
$qry1_file_hash = "SELECT `id` FROM media WHERE is_local = 1 AND `location` = ?";
$qry2_user_session = "SELECT user_id FROM user_session WHERE session_key = ? AND last_active > ? LIMIT 1";
$qry1_user_username = "SELECT `id`, `email_address`, `password_hash` FROM user WHERE username = ?";
$qry_user_id = "SELECT IFNULL(MAX(id) + 1, 1) FROM user";  // uid 0 is reserved for anonymous uploads

$cki_user_session = "session_key";

$str_media_dir = __DIR__ . "/media";
$int_max_upload_size = 268435456;  // 256 MiB
$COOKIE_ACTIVE_PERIOD = 2 * 24 * 3600;
