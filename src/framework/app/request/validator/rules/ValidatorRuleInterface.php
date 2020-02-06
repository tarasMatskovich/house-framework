<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 06.02.2020
 * Time: 14:30
 */

namespace houseframework\app\request\validator\rules;


/**
 * Interface ValidatorRuleInterface
 * @package houseframework\app\request\validator\rules
 */
interface ValidatorRuleInterface
{

    /**
     * @param $data
     * @param null $additionalData
     * @return bool
     */
    public static function validateData($data, $additionalData = null);

    /**
     * @return string
     */
    public static function getDefaultMessage();

}
