<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $html = file_get_contents('index.html');
    echo $html;
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    file_put_contents('post_data.txt', var_export($_POST, true));
    $action = $_POST["action"];
    $markdownSrc = $_POST["md_src"];
    echo $action . PHP_EOL . $markdownSrc;
} else {
    echo "Server Error: Invalid Request, GET is only allowed.";
}
