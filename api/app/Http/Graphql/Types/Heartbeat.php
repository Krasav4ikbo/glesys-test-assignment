<?php
namespace App\Http\Graphql\Types;

use \App\Models\Heartbeat as HeartbeatModel;

class Heartbeat
{
    public function lastCheckIn(HeartbeatModel $source)
    {
        return $source->updated_at;
    }
}
