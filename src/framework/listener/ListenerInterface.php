<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 05.02.2020
 * Time: 10:46
 */

namespace houseframework\listener;


use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ListenerInterface
 * @package houseframework\listener
 */
interface ListenerInterface
{

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request);

}
