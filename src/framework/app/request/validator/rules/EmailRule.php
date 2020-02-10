<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 06.02.2020
 * Time: 16:06
 */

namespace houseframework\app\request\validator\rules;


/**
 * Class EmailRule
 * @package houseframework\app\request\validator\rules
 */
class EmailRule implements ValidatorRuleInterface
{

    /**
     * @param $data
     * @param null $additionalData
     * @return bool
     */
    public static function validateData($data, $additionalData = null)
    {
        if (!$data)
            return false;
        return (bool)preg_match(
            '/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-0-9A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u', $data);
    }

    /**
     * @return string
     */
    public static function getDefaultMessage()
    {
        return "The property :field must be a valid email address";
    }
}
