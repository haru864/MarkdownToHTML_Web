<?php

$items = [
    ['name' => 'banana', 'price' => 200],
    ['name' => 'orange', 'price' => 150],
];

// jsonに変換
$data = json_encode($items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

// header("Content-Type: application/octet-stream");
header("Content-Type: text/plain");
header('Content-Disposition: attachment; filename="hello.txt"');

echo $data;
