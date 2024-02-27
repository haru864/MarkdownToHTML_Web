<?php

namespace Validate;

use Exceptions\InvalidRequestMethodException;

class ValidationHelper
{
    public static function validateGetEditorRequest(): void
    {
        $allowedMethod = "GET";
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod !== $allowedMethod) {
            throw new InvalidRequestMethodException("Valid method is {$allowedMethod}, but {$requestMethod} given.");
        }
    }

    public static function validateConvertRequest(): void
    {
        $allowedMethod = "POST";
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod !== $allowedMethod) {
            throw new InvalidRequestMethodException("Valid method is {$allowedMethod}, but {$requestMethod} given.");
        }
        $requiredParam = "markdown";
        if ($_SERVER['REQUEST_METHOD'] === "POST" && !isset($_POST[$requiredParam])) {
            throw new InvalidRequestMethodException("There is no required parameter '{$requiredParam}'.");
        }
        $requiredContentType = "application/x-www-form-urlencoded";
        $requestContentType = $_SERVER["CONTENT_TYPE"];
        if (strtolower($requestContentType) !== "application/x-www-form-urlencoded") {
            throw new InvalidRequestMethodException("Content-Type must be {$requiredContentType}, but {$requestContentType} given.");
        }
    }

    public static function validateGetStaticFileRequest(): void
    {
        $allowedMethod = "GET";
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod !== $allowedMethod) {
            throw new InvalidRequestMethodException("Valid method is {$allowedMethod}, but {$requestMethod} given.");
        }
    }
}
