<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 15:35
 */

namespace houseframework\action;


use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ActionInterface
 * @package houseframework\action
 */
interface ActionInterface
{

    public function __invoke(ServerRequestInterface $request);

}
