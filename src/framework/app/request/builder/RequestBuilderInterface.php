<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 15:27
 */

namespace houseframework\app\request\builder;


use houseframework\app\request\ValidatedRequestMessage;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface RequestBuilderInterface
 * @package houseframework\app\request\builder
 */
interface RequestBuilderInterface
{

    /**
     * @return ServerRequestInterface
     */
    public function build();

    /**
     * @param $specialRequestClassName
     * @return ValidatedRequestMessage
     */
    public function buildSpecialRequest($specialRequestClassName);

    /**
     * @param ServerRequestInterface $request
     * @param array $attributes
     * @return ServerRequestInterface
     */
    public function attachAttributesToRequest(ServerRequestInterface $request, array $attributes);

}
