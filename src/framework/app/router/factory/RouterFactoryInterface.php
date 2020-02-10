<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 13:41
 */

namespace houseframework\app\router\factory;


use houseframework\app\router\RouterInterface;

/**
 * Interface RouterFactoryInterface
 * @package houseframework\app\router\factory
 */
interface RouterFactoryInterface
{

    /**
     * @param string $buildKey
     * @return RouterInterface
     */
    public function make(string $buildKey);

}
