<?php
namespace Feature\Queries;

use App\Models\Heartbeat;
use Tests\TestCase;

class UnhealthyHeartbeatsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsConsumer();

        $this->migrateDatabase();
    }

    public function test_returns_correct_values_by_empty_input_id_list()
    {
        $query = <<<'GQL'
            query {
              unhealthyHeartbeats(applicationKeys: []){
                applicationKey,
                heartbeatKey,
                unhealthyAfterMinutes,
                lastCheckIn
              }
            }
            GQL;

        $this->graphql($query)
            ->assertOk()
            ->assertJsonCount(1, 'data.unhealthyHeartbeats.*.applicationKey')
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.applicationKey', ["app-0"])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.heartbeatKey', ["source-0"])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.unhealthyAfterMinutes', [0])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.lastCheckIn', [date('Y-m-d H:i:s')]);
    }

    public function test_returns_correct_values_by_input_id_list()
    {
        $query = <<<'GQL'
            query {
              unhealthyHeartbeats(applicationKeys: ["app-0"]){
                applicationKey,
                heartbeatKey,
                unhealthyAfterMinutes,
                lastCheckIn
              }
            }
            GQL;

        $this->graphql($query)
            ->assertOk()
            ->assertJsonCount(1, 'data.unhealthyHeartbeats.*.applicationKey')
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.applicationKey', ["app-0"])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.heartbeatKey', ["source-0"])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.unhealthyAfterMinutes', [0])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.lastCheckIn', [date('Y-m-d H:i:s')]);
    }

    public function test_returns_error_by_incorrect_input_variable()
    {
        $query = <<<'GQL'
            query {
              unhealthyHeartbeats(applicationKeys: ["app-0", "app-1"]){
                applicationKey,
                heartbeatKey,
                unhealthyAfterMinutes,
                wrongVariable
              }
            }
            GQL;

        $this->graphql($query)
            ->assertOk()
            ->assertJsonCount(1, 'errors')
            ->assertJsonPathCanonicalizing('errors.*.message',
                [
                    'Cannot query field "wrongVariable" on type "Heartbeat".'
                ]
            );
    }

    public function test_returns_one_expected_field()
    {
        $query = <<<'GQL'
            query {
              unhealthyHeartbeats(applicationKeys: ["app-0", "app-1"]){
                applicationKey
              }
            }
            GQL;


        $this->graphql($query)
            ->assertOk()
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.applicationKey', ["app-0"])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.heartbeatKey', [null])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.unhealthyAfterMinutes', [null])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.lastCheckIn', [null]);
    }

    public function test_returns_error_for_extra_input_parameters()
    {
        $query = <<<'GQL'
            query {
              unhealthyHeartbeats(applicationKeys: ["app-0", "app-1"], testKeys: []){
                applicationKey,
                heartbeatKey,
                unhealthyAfterMinutes,
                lastCheckIn
              }
            }
            GQL;

        $this->graphql($query)
            ->assertServerError();
    }

    public function test_returns_error_for_empty_input_parameters()
    {
        $query = <<<'GQL'
            query {
              unhealthyHeartbeats(){
                applicationKey,
                heartbeatKey,
                unhealthyAfterMinutes,
                lastCheckIn
              }
            }
            GQL;

        $this->graphql($query)
            ->assertOk()
            ->assertJsonCount(1, 'errors')
            ->assertJsonPathCanonicalizing('errors.*.message',
                [
                    'Syntax Error: Expected Name, found )'
                ]
            );
    }

    public function test_returns_correct_list_of_variables_by_multiple_input_values()
    {
        Heartbeat::create([
            'applicationKey' => 'app-22',
            'heartbeatKey' => 'source-2',
            'unhealthyAfterMinutes' => 0
        ]);

        $query = <<<'GQL'
            query {
              unhealthyHeartbeats(applicationKeys: ["app-0", "app-22"]){
                applicationKey,
                heartbeatKey,
                unhealthyAfterMinutes,
                lastCheckIn
              }
            }
            GQL;

        $this->graphql($query)
            ->assertOk()
            ->assertJsonCount(2, 'data.unhealthyHeartbeats')
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.applicationKey', ["app-0", "app-22"])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.heartbeatKey', ["source-0", "source-2"])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.unhealthyAfterMinutes', [0, 0])
            ->assertJsonPathCanonicalizing('data.unhealthyHeartbeats.*.lastCheckIn', [date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
    }
}
