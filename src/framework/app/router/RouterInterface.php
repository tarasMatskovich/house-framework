<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 11:37
 */

namespace houseframework\app\router;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface RouterInterface
 * @package houseframework\app\router
 */
interface RouterInterface
{

    /**
     * @param ServerRequestInterface $request
     * @return array
     */
    public function run(ServerRequestInterface $request);

    /**
     * @return array
     */
    public function getRoutes();

}
