<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 13:44
 */

namespace houseframework\app\router\enum;


/**
 * Class HttpMethodsEnum
 * @package houseframework\app\router\enum
 */
class HttpMethodsEnum
{

    const POST = 'POST';

    const GET = 'GET';

    const DELETE = 'DELETE';

    const PUT = 'PUT';

    const AVAILABLE_METHODS = [
        self::GET,
        self::POST,
        self::DELETE,
        self::PUT
    ];

}
