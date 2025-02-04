<?php

use App\Http\Controllers\GraphqlController;
use Illuminate\Support\Facades\Route;

Route::get('/graphql', GraphqlController::class);
