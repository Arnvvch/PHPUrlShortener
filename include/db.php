<?php
$db = new SQLite3('links.sdb', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

$db->query('CREATE TABLE IF NOT EXISTS "Shortener" (
    ID TEXT PRIMARY KEY NOT NULL,
    URL TEXT NOT NULL,
    VISITS TEXT NOT NULL,
    ADULT TEXT NOT NULL,
    INT_R TEXT NOT NULL,
    EXT_R TEXT NOT NULL,
    EXT_L TEXT NOT NULL
)');
?>