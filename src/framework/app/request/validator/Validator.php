<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 06.02.2020
 * Time: 14:28
 */

namespace houseframework\app\request\validator;


use houseframework\app\request\validator\rules\mapper\ValidatorRulesMapper;
use houseframework\app\request\validator\rules\mapper\ValidatorRulesMapperInterface;
use houseframework\app\request\validator\rules\ValidatorRuleInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Validator
 * @package houseframework\app\request\validator
 */
class Validator implements ValidatorInterface
{

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var ValidatorRulesMapper|ValidatorRulesMapperInterface|null
     */
    private $validatorRulesMapper;

    /**
     * Validator constructor.
     * @param ValidatorRulesMapperInterface|null $validatorRulesMapper
     */
    public function __construct(ValidatorRulesMapperInterface $validatorRulesMapper = null)
    {
        if (!$validatorRulesMapper) {
            $validatorRulesMapper = new ValidatorRulesMapper();
        }
        $this->validatorRulesMapper = $validatorRulesMapper;
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $rules
     * @return bool
     */
    public function validate(ServerRequestInterface $request, array $rules)
    {
        $result = true;
        foreach ($rules as $field => $rule) {
            $fieldValue = $request->getAttribute($field);
            if ($rule) {
                if (\is_array($rule)) {
                    foreach ($rule as $particularRule) {
                        $additionalData = null;
                        if (!$this->isValidRule($particularRule)) {
                            $particularRuleData = explode(":", $particularRule);
                            $particularRule = $this->validatorRulesMapper->map($particularRuleData[0] ?? null);
                            if (!$particularRuleData) {
                                continue;
                            }
                            $additionalData = $particularRuleData[1] ?? null;
                        }
                        if ($this->isValidRule($particularRule) && !$particularRule::validateData($fieldValue, $additionalData)) {
                            $result = false;
                            $this->pushError($field, $this->replaceMessage($field, $particularRule::getdefaultMessage()));
                        }
                    }
                } else {
                    $additionalData = null;
                    if (!$this->isValidRule($rule)) {
                        $ruleData = explode(":", $rule);
                        $rule = $this->validatorRulesMapper->map($ruleData[0] ?? null);
                        if (!$ruleData) {
                            continue;
                        }
                        $additionalData = $ruleData[1] ?? null;
                    }
                    if ($this->isValidRule($rule)) {
                        if (!$rule::validateData($fieldValue, $additionalData)) {
                            $result = false;
                            $this->pushError($field, $this->replaceMessage($field, $rule::getdefaultMessage()));
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $field
     * @param string $value
     * @return string
     */
    private function replaceMessage($field, $value)
    {
        return str_replace(":field", $field, $value);
    }

    /**
     * @param $rule
     * @return bool
     */
    private function isValidRule($rule)
    {
        if (class_exists($rule)) {
            return (new $rule) instanceof ValidatorRuleInterface;
        }
        return false;
    }

    /**
     * @param $field
     * @param $error
     */
    private function pushError($field, $error)
    {
        $this->errors[$field][] = $error;
    }
}
