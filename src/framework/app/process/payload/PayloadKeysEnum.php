<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 17.02.2020
 * Time: 12:37
 */

namespace houseframework\app\process\payload;


/**
 * Class PayloadKeysEnum
 * @package houseframework\app\process\payload
 */
class PayloadKeysEnum
{

    const CONFIG = 'config';

    const RPC_RUN = 'run';

    const RPC_BUILD = 'build';

    const ACTION = 'action';

    const ACTION_ROUTE  = 'actionRoute';

    const PAYLOAD = 'payload';

    const ATTRIBUTES = 'attributes';

    const CONTAINER_DEF = 'containerDef';

    const ROUTER_DEF = 'routerDef';

    const REQUEST_BUILDER_DEF = 'requestBuilderDef';

    const PIPELINE_BUILDER_DEF = 'pipelineBuilderDef';

    const CONFIG_DEF = 'configDef';

    const CLIENT_SESSION = 'clientSession';

}
