<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 31.01.2020
 * Time: 13:20
 */

namespace houseframework\app\response;


use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseHandler
 * @package houseframework\app\response
 */
class ResponseHandler
{

    /**
     * @param ResponseInterface $response
     */
    public static function respond(ResponseInterface $response)
    {
        static::sendHeaders($response);
        static::sendBody($response);
    }

    /**
     * @param ResponseInterface $response
     */
    private static function sendBody(ResponseInterface $response)
    {
        echo $response->getBody();
    }

    /**
     * @param ResponseInterface $response
     */
    private static function sendHeaders(ResponseInterface $response)
    {
        // headers have already been sent by the developer
        if (headers_sent()) {
            return;
        }

        // headers
        $headers = $response->getHeaders();
        $statusCode = $response->getStatusCode();
        $protocol = $response->getProtocolVersion();
        $reasonPhrase = $response->getReasonPhrase();
        foreach ($headers as $name => $values) {
            $replace = 0 === strcasecmp($name, 'Content-Type');
            foreach ($values as $value) {
                header($name.': '.$value, $replace, $statusCode);
            }
        }

        // status
        header(sprintf('HTTP/%s %s %s', $protocol, $statusCode, $reasonPhrase), true, $statusCode);
    }

}
