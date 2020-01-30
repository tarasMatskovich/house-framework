<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 16:38
 */

namespace houseframework\app\eventlistener;


/**
 * Class EventListener
 * @package houseframework\app\eventlistener
 */
class EventListener implements EventListenerInterface
{

    /**
     * @var array
     */
    private $channels;

    /**
     * EventListener constructor.
     * @param array $channels
     */
    public function __construct(array $channels = [])
    {
        $this->channels = $channels;
    }

    /**
     * @return array
     */
    public function getChannels()
    {
        return $this->channels;
    }
}
