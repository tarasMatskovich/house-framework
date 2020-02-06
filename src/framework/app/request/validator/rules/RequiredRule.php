<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 06.02.2020
 * Time: 14:34
 */

namespace houseframework\app\request\validator\rules;


/**
 * Class RequiredRule
 * @package houseframework\app\request\validator\rules
 */
class RequiredRule implements ValidatorRuleInterface
{

    /**
     * @param $data
     * @param null $additionalData
     * @return bool
     */
    public static function validateData($data, $additionalData = null)
    {
        if (null === $data || $data === '') {
            return false;
        }
        if (\is_array($data)) {
            return !empty($data);
        }
        return true;
    }

    /**
     * @return string
     */
    public static function getDefaultMessage()
    {
        return 'The property :field is required';
    }
}
