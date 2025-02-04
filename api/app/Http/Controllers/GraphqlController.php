<?php

namespace App\Http\Controllers;

use Butler\Graphql\Concerns\HandlesGraphqlRequests;
use Butler\Service\Http\Controllers\Controller;

class GraphqlController extends Controller
{
    use HandlesGraphqlRequests;
}
