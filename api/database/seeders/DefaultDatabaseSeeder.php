<?php

namespace Database\Seeders;

use App\Models\Heartbeat;
use Butler\Service\Models\Consumer;
use Illuminate\Database\Seeder;

class DefaultDatabaseSeeder extends Seeder
{
    public function run()
    {
        if (app()->isLocal() || app()->runningUnitTests()) {
            $this->seedDeveloperConsumer();
            $this->seedHeartBeat();
        }
    }

    private function seedDeveloperConsumer(): void
    {
        Consumer::firstOrCreate(['name' => 'developer'])
            ->tokens()
            ->firstOrCreate([
                'token' => hash('sha256', 'secret'),
                'abilities' => ['*'],
            ]);
    }

    private function seedHeartBeat(): void
    {
        for ($i = 0; $i < 10; $i++)
        {
            Heartbeat::create([
                'applicationKey' => 'app-' . $i,
                'heartbeatKey' => 'source-' . $i,
                'unhealthyAfterMinutes' => $i,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

    }
}
