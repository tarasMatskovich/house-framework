<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 17.02.2020
 * Time: 12:13
 */

namespace houseframework\app\process\host;


use React\Promise\PromiseInterface;
use Thruway\ClientSession;

/**
 * Interface ApplicationHostProcessInterface
 * @package houseframework\app\process\host
 */
interface ApplicationHostProcessInterface
{

    /**
     * @return void
     */
    public function start();

    /**
     * @param string $action
     * @param $attributes
     * @param ClientSession $clientSession
     * @return PromiseInterface
     */
    public function run(string $action, $attributes, ClientSession $clientSession);

}
