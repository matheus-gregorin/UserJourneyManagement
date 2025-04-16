<?php

namespace Tests\Unit;

use App\Domain\Enums\EventsWahaEnum;
use App\Domain\HttpClients\ClientHttpInterface;
use App\Domain\Repositories\UserRepositoryInterface;
use App\UseCase\WebhookReceiveMessageWahaUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class WebhookReceiveMessageWahaUseCaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_example()
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_when_receiving_a_payload_where_the_event_is_not_message_returns_false()
    {

        // Mock do repositório
        $mockRepository = Mockery::mock(UserRepositoryInterface::class);

        // Mock do Htt Client
        $mockHttpClient = \Mockery::mock(ClientHttpInterface::class);

        // Instanciando a use case com a dependência mockada
        $WebhookReceiveMessageWahaUseCase = new WebhookReceiveMessageWahaUseCase(
            $mockRepository,
            $mockHttpClient
        );

        $payload = ['event' => 'Options'];
        $response = $WebhookReceiveMessageWahaUseCase->webhookReceiveMessage($payload);
        $this->assertFalse($response);
    }

    public function test_when_receiving_a_payload_that_does_not_have_the_senders_number_return_false_and_simulate_sending_the_message_to_the_client()
    {

        // Mock do repositório
        $mockRepository = Mockery::mock(UserRepositoryInterface::class);

        // Mock do Htt Client
        $mockHttpClient = \Mockery::mock(ClientHttpInterface::class);
        // $mockHttpClient->shouldReceive('sendError')
        //     ->once()
        //     ->with('5511956558187', EventsWahaEnum::USERNOTFOUND)
        //     ->andReturn(new \GuzzleHttp\Psr7\Response(
        //         200,
        //         [],
        //         json_encode(['success' => true])
        //     ));

        // Instanciando a use case com a dependência mockada
        $WebhookReceiveMessageWahaUseCase = new WebhookReceiveMessageWahaUseCase(
            $mockRepository,
            $mockHttpClient
        );

        $payload = [
            'event' => 'message',
            'payload' => []
        ];
        $response = $WebhookReceiveMessageWahaUseCase->webhookReceiveMessage($payload);
        $this->assertFalse($response);
    }
}
