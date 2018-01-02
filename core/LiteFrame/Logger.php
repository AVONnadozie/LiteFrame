<?php

namespace LiteFrame;

use Exception;

/**
 * Description of Logger.
 *
 * @author Victor Anuebunwa
 */
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

    public function __construct($exception, $outputMedium = self::MEDIUM_STDOUT_FILE)
    {
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
     * @return type
     */
    public function getException()
    {
        if (!$this->exception instanceof Exception) {
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
     */
    public function log()
    {

        if (php_sapi_name() == 'cli') {
            $message = $this->getContentForFile();
            echo $message;
            $this->writeToLogFile($message);
        } else {
            if ($this->outputMedium !== self::MEDIUM_STDOUT) {
                $this->writeToLogFile($this->getContentForFile());
            }

            abort(500, $this->getContentForView());
        }
    }

    private function getContentForView()
    {
        $content = ['trace' => ''];
        if ($this->exception instanceof \Exception ||
                $this->exception instanceof \Error) {
            $file = $this->exception->getFile();
            $line = $this->exception->getLine();
            $title = $this->exception->getMessage();
            $content['search'] = $title;
            $content['title'] = "$title at $file line $line";
            $content['content'] = $content['title'];
            if ($this->outputMedium !== self::MEDIUM_FILE) {
                $content['trace'] = $this->exception->getTraceAsString();

                $content['content'] = $content['title'].PHP_EOL;
                $content['content'] .= 'Stack trace:'.PHP_EOL;
                $content['content'] .= $content['trace'].PHP_EOL;
            }
        } else {
            $content['title'] = $this->exception;
            $content['content'] = $this->exception;
        }

        return $content;
    }

    private function getContentForFile()
    {
        $content = '';
        if ($this->exception instanceof Exception) {
            $content = $this->exception->getMessage().PHP_EOL;
            if ($this->outputMedium !== self::MEDIUM_STDOUT) {
                $date = date('Y-m-d H:i:s');
                $file = $this->exception->getFile();
                $line = $this->exception->getLine();
                $code = $this->exception->getCode();
                $content = "Exception: (code $code) [$date] at $file line $line".PHP_EOL
                        .$content;

                $content .= 'Stack trace:'.PHP_EOL;
                $content .= $this->exception->getTraceAsString().PHP_EOL;
            }
        } else {
            $content = $this->exception . PHP_EOL;
        }

        return $content;
    }

    private function writeToLogFile($content)
    {
        switch ($this->logFileFormat ?: config('app.log', 'single')) {
            case self::LOG_FILE_FORMAT_DAILY:
                $filename = 'liteframe-'.date('d-m-Y').'.log';
                break;
            case self::LOG_FILE_FORMAT_WEEKLY:
                $filename = 'liteframe-'.date('W-Y').'.log';
                break;
            default:
                $filename = 'liteframe.log';
                break;
        }

        $dir = WD.DS.'components'.DS.'logs';
        if (!file_exists($dir)) {
            mkdir($dir, 0775);

            //Ignore files
            $handle = fopen($dir.DS.'.gitignore', 'w');
            fwrite($handle, "*\n!.gitignore\n!.htaccess");
            fclose($handle);
        }

        error_log($content.PHP_EOL, 3, $dir.DS.$filename);
    }
}
