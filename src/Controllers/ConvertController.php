<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Services\ConvertService;
use Http\HttpRequest;
use Render\interface\HTTPRenderer;
use Render\HTMLRenderer;
use Render\JSONRenderer;
use Validate\ValidationHelper;

class ConvertController implements ControllerInterface
{
    private ConvertService $convertService;
    private HttpRequest $httpRequest;

    public function __construct(ConvertService $convertService, HttpRequest $httpRequest)
    {
        $this->convertService = $convertService;
        $this->httpRequest = $httpRequest;
    }

    public function assignProcess(): HTTPRenderer
    {
        $requestMethod = $this->httpRequest->getMethod();
        if ($requestMethod === 'GET') {
            return $this->getEditor();
        } else if ($requestMethod === 'POST') {
            return $this->convertMarkdownToHTML();
        }
    }

    private function getEditor(): HTMLRenderer
    {
        ValidationHelper::validateGetEditorRequest();
        return $this->convertService->getEditor();
    }

    private function convertMarkdownToHTML(): JSONRenderer
    {
        ValidationHelper::validateConvertRequest();
        $reqMsgBodyKey = "markdown";
        $markdownSrc = $this->httpRequest->getTextParam($reqMsgBodyKey);
        return $this->convertService->convertMarkdownToHTML($markdownSrc);
    }
}
