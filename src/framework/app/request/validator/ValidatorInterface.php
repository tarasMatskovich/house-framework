<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 06.02.2020
 * Time: 14:27
 */

namespace houseframework\app\request\validator;


use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ValidatorInterface
 * @package houseframework\app\request\validator
 */
interface ValidatorInterface
{

    /**
     * @param ServerRequestInterface $request
     * @param array $rules
     * @return bool
     */
    public function validate(ServerRequestInterface $request, array $rules);

    /**
     * @return array
     */
    public function getErrors();

}
