<?php

namespace App\Models;

use App\Traits\MultiplePrimaryKeyUse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $applicationKey
 * @property mixed $heartbeatKey
 * @property mixed $unhealthyAfterMinutes
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class Heartbeat extends Model
{
    use HasFactory;

    use MultiplePrimaryKeyUse;

    protected $connection = 'default';

    protected $primaryKey = ['applicationKey', 'heartbeatKey'];

    public $incrementing = false;

    /**
     * Fillable fields
     */
    protected $fillable = [
        'applicationKey',
        'heartbeatKey',
        'unhealthyAfterMinutes',
        'updated_at'
    ];
}
