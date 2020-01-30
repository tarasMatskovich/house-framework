<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 11:23
 */

namespace houseframework\app\factory;


use houseframework\app\ApplicationInterface;

/**
 * Interface ApplicationFactoryInterface
 * @package houseframework\app\factory
 */
interface ApplicationFactoryInterface
{

    /**
     * @param string $applicationType
     * @return ApplicationInterface
     */
    public function make(string $applicationType);

}
