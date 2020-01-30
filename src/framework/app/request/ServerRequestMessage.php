<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 15:24
 */

namespace houseframework\app\request;


use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Class ServerRequestMessage
 * @package houseframework\app\request
 */
class ServerRequestMessage extends ServerRequest
{

    /**
     * @var array
     */
    private $newAttributes = [];

    public static function fromGlobals()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $headers = getallheaders();
        $uri = self::getUriFromGlobals();
        $body = new CachingStream(new LazyOpenStream('php://input', 'r+'));
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';

        $serverRequest = new ServerRequestMessage($method, $uri, $headers, $body, $protocol, $_SERVER);

        return $serverRequest
            ->withCookieParams($_COOKIE)
            ->withQueryParams($_GET)
            ->withAttributes($_POST)
            ->withUploadedFiles(self::normalizeFiles($_FILES));

    }

    /**
     * @param array $attributes
     * @return static
     */
    public function withAttributes(array $attributes)
    {
        $request = clone $this;
        $request->newAttributes = $attributes;
        return $request;
    }

    public function getAttributes()
    {
        return $this->newAttributes;
    }

    public function getAttribute($attribute, $default = null)
    {
        if (!isset($this->newAttributes[$attribute]))
            return $default;
        return $this->newAttributes[$attribute];
    }

    public function withAttribute($attribute, $value)
    {
        $new = clone $this;
        $new->newAttributes[$attribute] = $value;
        return $new;

    }

}
