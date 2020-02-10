<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 07.02.2020
 * Time: 17:18
 */

namespace houseframework\app\publisher;


use houseframework\app\publisher\message\PublisherMessageInterface;

/**
 * Interface PublisherInterface
 * @package houseframework\app\publisher
 */
interface PublisherInterface
{

    /**
     * @param PublisherMessageInterface $message
     * @return void
     */
    public function publish(PublisherMessageInterface $message);

}
