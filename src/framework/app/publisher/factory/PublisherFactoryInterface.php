<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 07.02.2020
 * Time: 17:26
 */

namespace houseframework\app\publisher\factory;


use houseframework\app\publisher\PublisherInterface;

/**
 * Interface PublisherFactoryInterface
 * @package houseframework\app\publisher\factory
 */
interface PublisherFactoryInterface
{

    /**
     * @param string $applicationKey
     * @return PublisherInterface
     */
    public function makePublisher(string $applicationKey);

}
