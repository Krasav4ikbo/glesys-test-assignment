<?php
namespace Feature\Mutations;

use App\Models\Heartbeat;
use Tests\TestCase;

class SendHeartbeatTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsConsumer();

        $this->migrateDatabase();
    }

    public function test_returns_all_expected_fields()
    {
        $query = <<<'GQL'
            mutation storeHeartbeat{
                sendHeartbeat(input: {
                        applicationKey: "app-test-1",
                        heartbeatKey: "source-test-1",
                        unhealthyAfterMinutes: 1
                    }) {
                    heartbeat {
                        applicationKey,
                        heartbeatKey,
                        unhealthyAfterMinutes,
                        lastCheckIn
                    }
                }
            }
            GQL;

        $this->graphql($query)
            ->assertOk()
            ->assertJsonCount(1, 'data.sendHeartbeat.*.applicationKey')
            ->assertJsonPathCanonicalizing('data.sendHeartbeat.*.applicationKey', ['app-test-1'])
            ->assertJsonPathCanonicalizing('data.sendHeartbeat.*.heartbeatKey', ['source-test-1'])
            ->assertJsonPathCanonicalizing('data.sendHeartbeat.*.unhealthyAfterMinutes', [1])
            ->assertJsonPathCanonicalizing('data.sendHeartbeat.*.lastCheckIn', [date('Y-m-d H:i:s')]);
    }

    public function test_returns_one_expected_field()
    {
        $query = <<<'GQL'
            mutation storeHeartbeat{
                sendHeartbeat(input: {
                        applicationKey: "app-test-1",
                        heartbeatKey: "source-test-1",
                        unhealthyAfterMinutes: 1
                    }) {
                    heartbeat {
                        applicationKey
                    }
                }
            }
            GQL;

        $this->graphql($query)
            ->assertOk()
            ->assertJsonCount(1, 'data.sendHeartbeat.*.applicationKey')
            ->assertJsonPathCanonicalizing('data.sendHeartbeat.*.applicationKey', ['app-test-1'])
            ->assertJsonPathCanonicalizing('data.sendHeartbeat.*.heartbeatKey', [null])
            ->assertJsonPathCanonicalizing('data.sendHeartbeat.*.unhealthyAfterMinutes', [null])
            ->assertJsonPathCanonicalizing('data.sendHeartbeat.*.lastCheckIn', [null]);
    }

    public function test_returns_error_with_missed_app_key_input_field()
    {
        $query = <<<'GQL'
            mutation storeHeartbeat{
                sendHeartbeat(input: {
                        heartbeatKey: "source-test-1",
                        unhealthyAfterMinutes: 1
                    }) {
                    heartbeat {
                        applicationKey
                    }
                }
            }
            GQL;

        $this->graphql($query)
            ->assertOk()
            ->assertJsonCount(1, 'errors')
            ->assertJsonPathCanonicalizing('errors.*.message',
                [
                    'Field SendHeartbeatInput.applicationKey of required type String! was not provided.'
                ]
            );
    }

    public function test_returns_errors_with_missed_all_input_fields()
    {
        $query = <<<'GQL'
            mutation storeHeartbeat{
                sendHeartbeat(input: {
                    }) {
                    heartbeat {
                        applicationKey
                    }
                }
            }
            GQL;

        $this->graphql($query)
            ->assertOk()
            ->assertJsonCount(3, 'errors')
            ->assertJsonPathCanonicalizing('errors.*.message',
                [
                    'Field SendHeartbeatInput.applicationKey of required type String! was not provided.',
                    'Field SendHeartbeatInput.heartbeatKey of required type String! was not provided.',
                    'Field SendHeartbeatInput.unhealthyAfterMinutes of required type Int! was not provided.',
                ]
            );
    }

    public function test_returns_error_with_extra_input_field()
    {
        $query = <<<'GQL'
            mutation storeHeartbeat{
                sendHeartbeat(input: {
                        applicationKeyTest: "app-test-1",
                        applicationKey: "app-test-1",
                        heartbeatKey: "source-test-1",
                        unhealthyAfterMinutes: 1
                    }) {
                    heartbeat {
                        applicationKey
                    }
                }
            }
            GQL;

        $this->graphql($query)
            ->assertOk()
            ->assertJsonCount(1, 'errors')
            ->assertJsonPathCanonicalizing('errors.*.message',
                [
                    'Field "applicationKeyTest" is not defined by type SendHeartbeatInput; Did you mean applicationKey?'
                ]
            );
    }

    public function test_returns_correct_stored_data()
    {
        $query = <<<'GQL'
            mutation storeHeartbeat{
                sendHeartbeat(input: {
                        applicationKey: "app-test-1",
                        heartbeatKey: "source-test-1",
                        unhealthyAfterMinutes: 1
                    }) {
                    heartbeat {
                        applicationKey,
                        heartbeatKey,
                        unhealthyAfterMinutes,
                        lastCheckIn
                    }
                }
            }
            GQL;

        $this->graphql($query)
            ->assertOk();

        $model = Heartbeat::where('applicationKey', 'app-test-1')->where('heartbeatKey', 'source-test-1')->first();

        $this->assertSame(date('Y-m-d H:i:s'), $model->updated_at->format('Y-m-d H:i:s'));
        $this->assertSame('app-test-1', $model->applicationKey);
        $this->assertSame('source-test-1', $model->heartbeatKey);
        $this->assertSame(1, $model->unhealthyAfterMinutes);
    }
}
