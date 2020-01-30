<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 15:39
 */

namespace houseframework\app\request\pipeline\builder;


use houseframework\app\request\pipeline\PipelineInterface;

/**
 * Interface PipelineBuilderInterface
 * @package houseframework\app\request\pipeline\builder
 */
interface PipelineBuilderInterface
{

    /**
     * @param string $action
     * @return PipelineInterface
     */
    public function build(string $action);

}
