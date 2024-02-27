<?php

namespace Services;

use Render\HTMLRenderer;
use Render\JSONRenderer;

class ConvertService
{
    public function __construct()
    {
    }

    public function getEditor(): HTMLRenderer
    {
        return new HTMLRenderer(200, 'editor', []);
    }

    public function convertMarkdownToHTML(string $markdownSrc): JSONRenderer
    {
        $parsedown = new \Parsedown();
        $parsedown->setSafeMode(true);
        $htmlSrc = $parsedown->text($markdownSrc);
        return new JSONRenderer(200, ['html' => $htmlSrc]);
    }
}
