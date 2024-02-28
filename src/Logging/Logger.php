<?php

namespace Logging;

use Http\HttpResponse;
use Settings\Settings;
use Throwable;

class Logger
{
    private static $instance = null;
    private string $logFileDirectory;
    private string $logFileName;
    private bool $truncateEnabled;
    private int $truncateLimit;

    private function __construct()
    {
        $this->logFileDirectory = Settings::env("LOG_FILE_LOCATION");
        $this->truncateEnabled = Settings::env('LOG_TRUNCATE_ENABLED') === 'true';
        $this->truncateLimit = intval(Settings::env('LOG_TRUNCATE_LIMIT'));
        $this->initializeLogFile();
    }

    private function initializeLogFile(): void
    {
        if (!file_exists($this->logFileDirectory)) {
            mkdir($this->logFileDirectory, 0755, true);
        }
        $this->logFileName = $this->logFileDirectory . DIRECTORY_SEPARATOR . date('Ymd') . '.log';
        if (!file_exists($this->logFileName)) {
            file_put_contents($this->logFileName, '');
        }
    }

    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        return self::$instance;
    }

    public function log(LogLevel $level, String $message, array $context = []): void
    {
        $logEntry = '[' . date('Y-m-d H:i:s') . '] ' . strtoupper($level->value) . ' ' . $message;
        if (!empty($context)) {
            $logEntry .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        file_put_contents($this->logFileName, $logEntry . PHP_EOL, FILE_APPEND);
    }

    public function logRequest(): void
    {
        $requestInfo = [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'N/A',
            'uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
            'query' => $_SERVER['QUERY_STRING'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'post_data' => $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : 'N/A',
            'files_data' => $_SERVER['REQUEST_METHOD'] === 'POST' ? $_FILES : 'N/A'
        ];
        if ($this->truncateEnabled) {
            $requestInfo['post_data'] = $this->truncateArray($_POST, $this->truncateLimit);
            $requestInfo['files_data'] = $this->truncateArray($_FILES, $this->truncateLimit);
        }
        $this->log(LogLevel::INFO, 'Request received', ['request' => $requestInfo]);
    }

    public function logResponse(HttpResponse $httpResponse): void
    {
        $messageBody = $httpResponse->getMessageBody();
        if ($this->truncateEnabled) {
            $outputMessageBody = substr($messageBody, 0, $this->truncateLimit)
                . (strlen($messageBody) > $this->truncateLimit ? '...' : '');
        } else {
            $outputMessageBody = $messageBody;
        }
        $responseInfo = [
            'status_code' => $httpResponse->getStatusCode() ?? 'N/A',
            'headers' => $httpResponse->getHeaders() ?? 'N/A',
            'message_body' => $outputMessageBody ?? 'N/A'
        ];
        $this->log(LogLevel::INFO, 'Response sent', ['response' => $responseInfo]);
    }

    public function logError(Throwable $e)
    {
        $this->log(LogLevel::ERROR, $e->getMessage() . PHP_EOL . $e->getTraceAsString());
    }

    private function truncateArray($array, $limit): array
    {
        $truncated = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $truncated[$key] = $this->truncateArray($value, $limit);
            } else {
                $truncated[$key] = strlen($value) > $limit ? substr($value, 0, $limit) . '...' : $value;
            }
        }
        return $truncated;
    }
}
