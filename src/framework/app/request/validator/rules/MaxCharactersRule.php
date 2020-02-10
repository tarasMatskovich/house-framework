<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 06.02.2020
 * Time: 16:35
 */

namespace houseframework\app\request\validator\rules;


/**
 * Class MaxCharactersRule
 * @package houseframework\app\request\validator\rules
 */
class MaxCharactersRule implements ValidatorRuleInterface
{

    /**
     * @param $data
     * @param null $additionalData
     * @return bool
     */
    public static function validateData($data, $additionalData = null)
    {
        if (!$data || !$additionalData) {
            return false;
        }
        if (\is_string($data)) {
            return mb_strlen($data) <= (int)$additionalData;
        }
        return false;
    }

    /**
     * @return string
     */
    public static function getDefaultMessage()
    {
        return "The length of property :field should not exceed :additional characters";
    }
}
