<?php

namespace rapidPHP\library\core\server\response;

use rapidPHP\library\core\server\Response;
use Swoole\Http\Response as SwooleResponse;

class SHttpResponse extends Response
{
    /**
     * @var SwooleResponse
     */
    private $swooleResponse;

    /**
     * Response constructor.
     * @param SwooleResponse $response
     */
    public function __construct(SwooleResponse $response)
    {
        $this->swooleResponse = $response;
    }

    /**
     * 快速获取实例对象
     * @param SwooleResponse $response
     * @return Response
     */
    public static function getInstance(SwooleResponse $response)
    {
        return new self($response);
    }

    /**
     * 设置HttpCode，如404, 501, 200
     * @param $code
     */
    public function status($code)
    {
        $this->swooleResponse->status($code);
    }

    /**
     * 设置Http头信息
     * @param $data
     * @param bool $ucfirst
     */
    public function header($data, $ucfirst = true)
    {
        $data = explode(":", $data);

        $key = B()->getData($data, 0);

        $value = trim(B()->getData($data, 1));

        $this->swooleResponse->header($key, $value, false);
    }

    /**
     * 设置Cookie
     *
     * @param string $key
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @param string $samesite 从 v4.4.6 版本开始支持
     */
    public function cookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false, $samesite = '')
    {
        $this->swooleResponse->cookie($key, $value, $expire, $path, $domain, $secure, $httponly, $samesite);
    }


    /**
     * 启用Http-Chunk分段向浏览器发送数据
     *
     * @param string $data
     */
    public function write($data)
    {
        if (empty($data)) return;

        $this->swooleResponse->write($data);
    }

    /**
     * 发送文件或者下载文件
     * @param string $filename
     * @param array $options
     */
    public function sendFile($filename, $options = [])
    {
        $fileSize = filesize($filename);

        $headers = [
            'Connection: keep-alive',
            'Accept-Ranges: bytes',
            'Pragma: cache',
            'Content-Length: ' . $fileSize,
        ];

        $isDownload = (int)B()->getData($options, 'download');
        if ($isDownload) {
            $headers[] = ['Content-Disposition: inline; filename=' . basename($filename)];
            $headers[] = ['Content-Transfer-Encoding: binary'];
        }

        $cacheExpire = B()->getData($options, 'cache-expire');
        if ($cacheExpire > 0) $headers[] = ['Cache-Control: max-age=' . $cacheExpire];

        $mime = B()->getData($options, 'mime');
        if ($mime) $headers[] = ['Content-type: ' . $mime];

        $start = (int)B()->getData($options, 'start');
        $end = (int)B()->getData($options, 'end');
        if ($start > 0 && $end > 0) {
            $headers[] = ['Pragma: no-cache'];
            $headers[] = ['Cache-Control: max-age=0'];
            $headers[] = ['Content-Range: bytes' . ($start - $end / $fileSize)];
        }

        $headers = array_merge($headers, B()->getData($options, 'headers'));
        $this->setHeader($headers);

        $this->swooleResponse->sendFile($filename, $start, max(0, $end - $start));
    }

    /**
     * 结束Http响应，发送HTML内容
     * @param string $data
     */
    public function end($data = '')
    {
        $this->swooleResponse->end($data);
    }
}