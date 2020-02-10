<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 07.02.2020
 * Time: 17:20
 */

namespace houseframework\app\publisher\message;


/**
 * Class PublisherMessage
 * @package houseframework\app\publisher\message
 */
class PublisherMessage implements PublisherMessageInterface
{

    /**
     * @var string
     */
    private $topic;

    /**
     * @var string
     */
    private $eventType;

    /**
     * @var array
     */
    private $arguments;

    /**
     * PublisherMessage constructor.
     * @param string $topic
     * @param string $eventType
     * @param array $arguments
     */
    public function __construct(string $topic, string $eventType, array $arguments = [])
    {
        $this->topic = $topic;
        $this->eventType = $eventType;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

}
