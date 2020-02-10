<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 06.02.2020
 * Time: 18:09
 */

namespace houseframework\app\request;


/**
 * Class ValidatedRequestMessage
 * @package houseframework\app\request
 */
abstract class ValidatedRequestMessage extends ServerRequestMessage
{

    /**
     * @return array
     */
    abstract public function getRules(): array;

    /**
     * @return array
     */
    abstract public function getMessages(): array;

}
