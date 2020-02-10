<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 17:23
 */

namespace houseframework\app\config;


/**
 * Interface ConfigInterface
 * @package houseframework\app\config
 */
interface ConfigInterface
{

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key);

}
