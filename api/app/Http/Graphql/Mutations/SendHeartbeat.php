<?php
namespace App\Http\Graphql\Mutations;

use App\Models\Heartbeat;

class SendHeartbeat
{
    public function __invoke($root, $args, $context, $info)
    {
        /**
         * Use updated_at field as last check in
         * Update it each time to avoid case with new heartbeat
         * with the same unhealthyAfterMinutes value
         * */
        return Heartbeat::updateOrCreate([
            'applicationKey' => $args['input']['applicationKey'],
            'heartbeatKey' => $args['input']['heartbeatKey'],
        ],[
            'unhealthyAfterMinutes' => $args['input']['unhealthyAfterMinutes'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
