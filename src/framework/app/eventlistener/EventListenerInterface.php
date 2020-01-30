<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 16:37
 */

namespace houseframework\app\eventlistener;


/**
 * Interface EventListenerInterface
 * @package houseframework\app\eventlistener
 */
interface EventListenerInterface
{

    /**
     * @return array
     */
    public function getChannels();

}
