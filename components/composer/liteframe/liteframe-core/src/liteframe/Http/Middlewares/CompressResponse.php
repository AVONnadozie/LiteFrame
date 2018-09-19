<?php

namespace LiteFrame\Http\Middlewares;

use Closure;
use LiteFrame\Http\Middleware;
use LiteFrame\Http\Request;
use LiteFrame\Http\Response;
use LiteFrame\Http\Response\FileResponse;

class CompressResponse extends Middleware
{
    private static $noOptimize;

    /**
     * Turn off response compression.
     */
    public static function off()
    {
        self::$noOptimize = true;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure $next
     * @param Request  $request
     *
     * @return mixed
     */
    public function run(Closure $next = null, Request $request = null)
    {
        if (!$next) {
            return;
        }

        $response = $next($request);
        if (!config('app.compress_output') ||
                self::$noOptimize ||
                $response instanceof FileResponse ||
                $response->getStatusCode() === 500) {
            return $response;
        } else {
            return $this->optimize($response);
        }
    }

    protected function optimize(Response $response)
    {
        if ($response->isJson()) {
            return $response;
        }
        
        $buffer = $response->getContent();
        if (stripos($buffer, '<pre>') !== false ||
                stripos($buffer, '<textarea') !== false) {
            $replace = array(
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/" => '<?php ',
                "/\r/" => '',
                "/>\n</" => '><',
                "/>\s+\n</" => '><',
                "/>\n\s+</" => '><',
            );
        } else {
            $replace = array(
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/" => '<?php ',
                "/\n([\S])/" => '$1',
                "/\r/" => '',
                "/\n/" => '',
                "/\t/" => '',
                '/ +/' => ' ',
            );
        }
        
        $tinyContent = preg_replace(array_keys($replace), array_values($replace), $buffer);
        $response->setContent($tinyContent);
        ini_set('zlib.output_compression', 'On'); //enable GZip, too!
        return $response;
    }
}
