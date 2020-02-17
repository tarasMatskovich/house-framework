<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 17.02.2020
 * Time: 12:12
 */

namespace houseframework\app\process\app;


use houseframework\app\response\WampResponse;
use WyriHaximus\React\ChildProcess\Messenger\Messages\Payload;

/**
 * Interface SubProcessApplicationInterface
 * @package houseframework\app\process\app
 */
interface SubProcessApplicationInterface
{

    /**
     * @param Payload $payload
     * @return WampResponse
     */
    public function run(Payload $payload);

}
