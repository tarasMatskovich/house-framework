<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 07.02.2020
 * Time: 17:19
 */

namespace houseframework\app\publisher\message;


/**
 * Interface PublisherMessageInterface
 * @package houseframework\app\publisher\message
 */
interface PublisherMessageInterface
{

    /**
     * @return string
     */
    public function getTopic();

    /**
     * @return string
     */
    public function getEventType();

    /**
     * @return array
     */
    public function getArguments();

}
