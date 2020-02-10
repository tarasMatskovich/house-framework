<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 15:27
 */

namespace houseframework\app\request\builder;


use houseframework\app\request\ServerRequestMessage;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestBuilder
 * @package houseframework\app\request\builder
 */
class RequestBuilder implements RequestBuilderInterface
{

    /**
     * @return ServerRequestInterface
     */
    public function build()
    {
        return ServerRequestMessage::fromGlobals();
    }

    /**
     * @param $specialRequestClassName
     * @return \GuzzleHttp\Psr7\ServerRequest|ServerRequestMessage|\houseframework\app\request\ValidatedRequestMessage
     */
    public function buildSpecialRequest($specialRequestClassName)
    {
        return ServerRequestMessage::fromGlobalsWithSpecialRequest($specialRequestClassName);
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $attributes
     * @return ServerRequestInterface
     */
    public function attachAttributesToRequest(ServerRequestInterface $request, array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }
        return $request;
    }

}
