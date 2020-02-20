<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 20.02.2020
 * Time: 18:48
 */

namespace houseframework\app\publisher;


use houseframework\app\publisher\message\PublisherMessageInterface;
use Thruway\ClientSession;
use Thruway\Peer\Client;


/**
 * Class DelayedWampPublisher
 * @package houseframework\app\publisher
 */
class DelayedWampPublisher implements PublisherInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var PublisherMessageInterface[]
     */
    private $delayedMessages = [];

    /**
     * @var array
     */
    private $options = ['acknowledge' => true];

    /**
     * DelayedWampPublisher constructor.
     * @param Client $client
     * @throws \Exception
     */
    public function __construct(
        Client $client
    )
    {
        $this->client = $client;
        $this->client->start(false);
        $this->client->on('open', function(ClientSession $session) {
            if (count($this->delayedMessages)) {
                foreach ($this->delayedMessages as $message) {
                    $this->publish($message);
                    array_shift($this->delayedMessages);
                }
            } else {
                $session->getLoop()->stop();
            };
        });
    }

    /**
     * @param PublisherMessageInterface $message
     * @return void
     */
    public function publish(PublisherMessageInterface $message)
    {
        if (!$this->client->getSession()) {
            $this->pushMessage($message);
            $this->client->getLoop()->run();
        } else {
            $this->client->getSession()->publish($message->getTopic(), [
                $message->getEventType(),
                json_encode($message->getArguments())
            ], [], $this->options)->then(function () {
                $this->client->getLoop()->stop();
            });
        }
    }

    /**
     * @param PublisherMessageInterface $publisherMessage
     */
    private function pushMessage(PublisherMessageInterface $publisherMessage)
    {
        $this->delayedMessages[] = $publisherMessage;
    }
}
