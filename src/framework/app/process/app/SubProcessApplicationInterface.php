<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 17.02.2020
 * Time: 12:12
 */

namespace houseframework\app\process\app;


use WyriHaximus\React\ChildProcess\Messenger\Messages\Payload;

/**
 * Interface SubProcessApplicationInterface
 * @package houseframework\app\process\app
 */
interface SubProcessApplicationInterface
{

    /**
     * @param Payload $payload
     * @return array
     */
    public function run(Payload $payload);

}
