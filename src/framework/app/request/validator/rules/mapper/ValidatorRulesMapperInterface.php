<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 06.02.2020
 * Time: 15:33
 */

namespace houseframework\app\request\validator\rules\mapper;


/**
 * Interface ValidationRulesMapperInterface
 * @package houseframework\app\request\validator\rules\mapper
 */
interface ValidatorRulesMapperInterface
{

    /**
     * @param $rule
     * @return string|null
     */
    public function map($rule);

}
