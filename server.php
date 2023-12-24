<?php
require_once "./vendor/autoload.php";
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // $html = file_get_contents('index.html');
    $html = file_get_contents('test.html');
    echo $html;
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    file_put_contents('post_data.txt', var_export($_POST, true));
    $markdownSrc = $_POST["md_src"];
    $htmlSrc = $parsedown->text($markdownSrc);
    file_put_contents('response_data.txt', var_export($htmlSrc, true));
    $array = ['html' => $htmlSrc];
    echo json_encode($array);
} else {
    echo "Server Error: Invalid Request Method";
}
