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
use Psr\Http\Message\ServerRequestInterface;

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

    /**
     * @return ServerRequest|ServerRequestMessage|ServerRequestInterface
     */
    public static function fromGlobals()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $headers = getallheaders();
        $uri = self::getUriFromGlobals();
        $body = new CachingStream(new LazyOpenStream('php://input', 'r+'));
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';
        $parsedBody = json_decode(file_get_contents('php://input'), true);
        $serverRequest = new ServerRequestMessage($method, $uri, $headers, $body, $protocol, $_SERVER);

        return $serverRequest
            ->withCookieParams($_COOKIE)
            ->withQueryParams($_GET)
            ->withAttributes($_POST)
            ->withParsedBody($parsedBody)
            ->withUploadedFiles(self::normalizeFiles($_FILES));

    }

    /**
     * @param $requestClassName
     * @return ServerRequest|ServerRequestMessage
     */
    public static function fromGlobalsWithSpecialRequest($requestClassName)
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $headers = getallheaders();
        $uri = self::getUriFromGlobals();
        $body = new CachingStream(new LazyOpenStream('php://input', 'r+'));
        $parsedBody = json_decode(file_get_contents('php://input'), true);
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';
        $serverRequest = new $requestClassName($method, $uri, $headers, $body, $protocol, $_SERVER);
        /**
         * @var ServerRequestMessage $serverRequest
         */
        return $serverRequest
            ->withCookieParams($_COOKIE)
            ->withQueryParams($_GET)
            ->withAttributes($_POST)
            ->withParsedBody($parsedBody)
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

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->newAttributes;
    }

    /**
     * @param $attribute
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($attribute, $default = null)
    {
        if (!isset($this->newAttributes[$attribute])) {
            $parsedBody = $this->getParsedBody();
            return $parsedBody[$attribute] ?? $default;
        }
        return $this->newAttributes[$attribute];
    }

    /**
     * @param $attribute
     * @param $value
     * @return ServerRequest|ServerRequestMessage
     */
    public function withAttribute($attribute, $value)
    {
        $new = clone $this;
        $new->newAttributes[$attribute] = $value;
        return $new;

    }

}
