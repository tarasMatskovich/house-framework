<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 06.02.2020
 * Time: 15:34
 */

namespace houseframework\app\request\validator\rules\mapper;


use houseframework\app\request\validator\rules\EmailRule;
use houseframework\app\request\validator\rules\MaxCharactersRule;
use houseframework\app\request\validator\rules\MinCharactersRule;
use houseframework\app\request\validator\rules\RequiredRule;

/**
 * Class ValidatorRulesMapper
 * @package houseframework\app\request\validator\rules\mapper
 */
class ValidatorRulesMapper implements ValidatorRulesMapperInterface
{


    /**
     * @var array
     */
    private $mapping = [
        'required' => RequiredRule::class,
        'email' => EmailRule::class,
        'max' => MaxCharactersRule::class,
        'min' => MinCharactersRule::class
    ];

    /**
     * @param $rule
     * @return string|null
     */
    public function map($rule)
    {
        return $this->mapping[$rule] ?? null;
    }
}
