<?php
namespace App\Http\Graphql\Queries;

use App\Models\Heartbeat;

class UnhealthyHeartbeats
{
    public function __invoke($root, $args, $context, $info)
    {
        /**
         * This format is to make the conversion from days to minutes more visual.
         * */
        $query = Heartbeat::whereRaw('(julianday() - julianday(updated_at)) * 24 * 60 > heartbeats.unhealthyAfterMinutes');

        if (!empty($args['applicationKeys'])) {
            $query->whereIn('applicationKey', $args['applicationKeys']);
        }

        return $query->get();
    }
}
