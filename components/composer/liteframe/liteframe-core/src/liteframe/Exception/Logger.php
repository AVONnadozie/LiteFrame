<?php

namespace LiteFrame\Exception;

use Exception;
use LiteFrame\Exception\Exceptions\HttpException;
use LiteFrame\Http\Request;
use LiteFrame\View\View;
use const DS;
use function appIsOnDebugMode;
use function config;
use function isCLI;
use function nPath;
use function response;
use function storagePath;

class Logger
{

    /**
     * Output Medium constants.
     */
    const MEDIUM_FILE = 1;
    const MEDIUM_STDOUT = 2;
    const MEDIUM_STDOUT_FILE = 3;

    /**
     * Log File Format constants.
     */
    const LOG_FILE_FORMAT_SINGLE = 'single';
    const LOG_FILE_FORMAT_DAILY = 'daily';
    const LOG_FILE_FORMAT_WEEKLY = 'weekly';

    private $outputMedium;
    private $exception;
    private $logFileFormat;

    public function __construct($exception, $outputMedium = null)
    {
        if (empty($outputMedium)) {
            $outputMedium = $exception instanceof HttpException ?
                Logger::MEDIUM_STDOUT :
                Logger::MEDIUM_STDOUT_FILE;
        }
        $this->outputMedium = $outputMedium;
        $this->exception = $exception;
    }

    /**
     * Set file format.
     *
     * @param type $format
     *
     * @return $this
     */
    public function setLogFileFormat($format)
    {
        $this->logFileFormat = $format;

        return $this;
    }

    /**
     * Get log file format.
     *
     * @return type
     */
    public function getLogFileFormat()
    {
        return $this->logFileFormat;
    }

    /**
     * Get output medium.
     *
     * @return type
     */
    public function getOutputMedium()
    {
        return $this->outputMedium;
    }

    /**
     * Get content as exception.
     *
     * @return Exception|HttpException
     */
    public function getException()
    {
        if (is_string($this->exception)) {
            return new Exception($this->exception);
        }

        return $this->exception;
    }

    /**
     * Set output medium.
     *
     * @param type $outputMedium
     *
     * @return $this
     */
    public function setOutputMedium($outputMedium)
    {
        $this->outputMedium = $outputMedium;

        return $this;
    }

    /**
     * Set exception.
     *
     * @param Exception $exception
     *
     * @return $this
     */
    public function setException($exception)
    {
        $this->exception = $exception;

        return $this;
    }

    /**
     * Write content to file if output medium is not set to <code>MEDIUM_STDOUT</code>
     * and return the content of logger.
     *
     * @return string content of logger
     * @throws Exception
     */
    public function log()
    {
        if (isCLI()) {
            $message = $this->getContentForFile();
            $this->writeToLogFile($message);
            return $message;
        } else {
            if ($this->outputMedium !== static::MEDIUM_STDOUT) {
                $this->writeToLogFile($this->getContentForFile());
            }

            if ($this->outputMedium !== static::MEDIUM_FILE) {
                return $this->getResponse();
            }
        }
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\LiteFrame\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    private function getResponse()
    {
        $exception = $this->getException();
        $bag = new ErrorBag($exception);
        //Remove trace if not in debug mode
        if (!appIsOnDebugMode()) {
            $bag->setTrace('');
        }

        $request = Request::getInstance();
        if ($request->wantsJson()) {
            $content = [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace()
            ];
        } else {
            $content = $this->getErrorPage($bag);
        }
        return response($content, $bag->getCode());
    }

    /**
     * Return error page content for the given ErrorBag
     * @param ErrorBag $errorBag
     * @return string
     * @throws Exception
     */
    private function getErrorPage(ErrorBag $errorBag)
    {
        $code = $errorBag->getCode();
        $data = ['bag' => $errorBag, 'errorBag' => $errorBag];
        //Fetch user error page for the error code
        try {
            return View::fetch("errors/$code", $data);
        } catch (Exception $e) {
            return View::fetch("errors/default", $data);
        }
    }

    private function getContentForFile()
    {
        $content = '';
        if ($this->exception instanceof Exception) {
            $content = $this->exception->getMessage() . PHP_EOL;
            if ($this->outputMedium !== static::MEDIUM_STDOUT) {
                $date = date('Y-m-d H:i:s');
                $file = $this->exception->getFile();
                $line = $this->exception->getLine();
                $code = $this->exception->getCode();
                $content = "Exception: (code $code) [$date] at $file line $line" . PHP_EOL
                    . $content;

                $content .= 'Stack trace:' . PHP_EOL;
                $content .= $this->exception->getTraceAsString() . PHP_EOL;
            }
        } else {
            $content = $this->exception . PHP_EOL;
        }

        return $content;
    }

    private function writeToLogFile($content)
    {
        switch ($this->logFileFormat ?: config('app.log', 'daily')) {
            case static::LOG_FILE_FORMAT_DAILY:
                $filename = 'liteframe-' . date('d-m-Y') . '.log';
                break;
            case static::LOG_FILE_FORMAT_WEEKLY:
                $filename = 'liteframe-' . date('W-Y') . '.log';
                break;
            default:
                $filename = 'liteframe.log';
                break;
        }

        $dir = logsStoragePath();
        if (!file_exists($dir)) {
            mkdir($dir, 0775);

            //Ignore files
            $handle = fopen($dir . DS . '.gitignore', 'w');
            fwrite($handle, "*\n!.gitignore\n!.htaccess");
            fclose($handle);
        }

        error_log($content . PHP_EOL, 3, nPath($dir, $filename));
    }
}
