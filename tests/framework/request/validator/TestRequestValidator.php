<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 06.02.2020
 * Time: 14:40
 */

namespace housetests\framework\request\validator;


use houseframework\app\request\builder\RequestBuilder;
use houseframework\app\request\validator\rules\RequiredRule;
use houseframework\app\request\validator\Validator;

/**
 * Class TestRequestValidator
 * @package housetests\framework\request\validator
 */
class TestRequestValidator extends \PHPUnit_Framework_TestCase
{

    /**
     * @param array $data
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function getRequest(array $data)
    {
        $requestBuilder = new RequestBuilder();
        $request = $requestBuilder->attachAttributesToRequest($requestBuilder->build(), $data);
        return $request;
    }

    /**
     * method testValidator
     */
    public function testValidator()
    {
        /**
         * test required rule
         */
        $request = $this->getRequest([
            'test' => 'key'
        ]);
        $validator = new Validator();
        $rules = [
          'test' => [RequiredRule::class]
        ];
        $result = $validator->validate($request, $rules);
        $this->assertTrue($result);
        $request = $this->getRequest([
            'test' => ''
        ]);
        $result = $validator->validate($request, $rules);
        $this->assertFalse($result);
        $errors = $validator->getErrors();
        $this->assertNotEmpty($errors);
        $request = $this->getRequest([]);
        $result = $validator->validate($request, $rules);
        $this->assertFalse($result);
        $errors = $validator->getErrors();
        $this->assertNotEmpty($errors);
        $rules = [
            'test' => 'required'
        ];
        $request = $this->getRequest([]);
        $result = $validator->validate($request, $rules);
        $this->assertFalse($result);
        $errors = $validator->getErrors();
        $this->assertNotEmpty($errors);
        /**
         * test email rule
         */
        $request = $this->getRequest([
            'email' => 'test@gmail.com'
        ]);
        $rules = [
            'email' => ['email', 'required']
        ];
        $result = $validator->validate($request, $rules);
        $this->assertTrue($result);
        $request = $this->getRequest([
            'email' => 'd82usjox'
        ]);
        $result = $validator->validate($request, $rules);
        $this->assertFalse($result);
        $request = $this->getRequest([
            'email' => ''
        ]);
        $result = $validator->validate($request, $rules);
        $this->assertFalse($result);
        /**
         * test min and max
         */
        $request = $this->getRequest([
            'password' => '123456'
        ]);
        $rules = [
            'password' => 'max:6'
        ];
        $result = $validator->validate($request, $rules);
        $this->assertTrue($result);
        $request = $this->getRequest([
            'password' => '1234567'
        ]);
        $result = $validator->validate($request, $rules);
        $this->assertFalse($result);
        $request = $this->getRequest([
            'password' => '12'
        ]);
        $rules = [
            'password' => 'min:3'
        ];
        $result = $validator->validate($request, $rules);
        $this->assertFalse($result);
        $request = $this->getRequest([
            'password' => '123'
        ]);
        $result = $validator->validate($request, $rules);
        $this->assertTrue($result);
        $request = $this->getRequest([
           'password' => '12'
        ]);
        $rules = [
            'password' => ['required', 'min:3', 'max:6']
        ];
        $result = $validator->validate($request, $rules);
        $this->assertFalse($result);
        $request = $this->getRequest([
            'password' => '12345'
        ]);
        $result = $validator->validate($request, $rules);
        $this->assertTrue($result);
        /**
         * test a specific case
         */
        $request = $this->getRequest([]);
        $rules = [
            'name' => ['test', 'max^2']
        ];
        $result = $validator->validate($request, $rules);
        $this->assertTrue($result);
    }

}
