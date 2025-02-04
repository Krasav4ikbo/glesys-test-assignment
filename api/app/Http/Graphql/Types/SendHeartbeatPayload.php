<?php
namespace App\Http\Graphql\Types;

use \App\Models\Heartbeat;

class SendHeartbeatPayload
{
    public function heartbeat(Heartbeat $source): Heartbeat
    {
        return $source;
    }
}
