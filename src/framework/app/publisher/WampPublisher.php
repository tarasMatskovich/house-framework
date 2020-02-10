<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 07.02.2020
 * Time: 17:24
 */

namespace houseframework\app\publisher;


use houseframework\app\publisher\message\PublisherMessageInterface;
use Thruway\ClientSession;


/**
 * Class WampPublisher
 * @package houseframework\app\publisher
 */
class WampPublisher implements PublisherInterface
{

    /**
     * @var ClientSession
     */
    private $clientSession;

    /**
     * WampPublisher constructor.
     * @param ClientSession $clientSession
     */
    public function __construct(ClientSession $clientSession)
    {
        $this->clientSession = $clientSession;
    }

    /**
     * @param PublisherMessageInterface $message
     * @return void
     */
    public function publish(PublisherMessageInterface $message)
    {
        $this->clientSession->publish($message->getTopic(), [
            $message->getEventType(),
            json_encode($message->getArguments())
        ]);
    }
}
