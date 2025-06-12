<?php

namespace Tests\Unit;

use Domain\Entities\UserEntity;
use Domain\Enums\EventsWahaEnum;
use Domain\HttpClients\ClientHttpInterface;
use Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\UserNotFoundException;
use App\UseCase\WebhookReceiveMessageWahaUseCase;
use DateTime;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class WebhookReceiveMessageWahaUseCaseTest extends TestCase
{

    public function test_when_receiving_a_payload_where_the_event_is_not_message_returns_false()
    {

        Log::info("--1--\ntest_when_receiving_a_payload_where_the_event_is_not_message_returns_false");

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

        Log::info("--2--\ntest_when_receiving_a_payload_that_does_not_have_the_senders_number_return_false_and_simulate_sending_the_message_to_the_client");

        // Mock do repositório
        $mockRepository = Mockery::mock(UserRepositoryInterface::class);

        // Mock do Htt Client
        $mockHttpClient = \Mockery::mock(ClientHttpInterface::class);

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

    public function test_when_receiving_a_payload_that_does_not_have_the_senders_message_id_return_false_and_simulate_sending_the_message_to_the_client()
    {

        Log::info("--3--\ntest_when_receiving_a_payload_that_does_not_have_the_senders_message_id_return_false_and_simulate_sending_the_message_to_the_client");

        // Mock do repositório
        $mockRepository = Mockery::mock(UserRepositoryInterface::class);

        // Mock do Htt Client
        $mockHttpClient = \Mockery::mock(ClientHttpInterface::class);

        $mockHttpClient->shouldReceive('sendError')
            ->once()
            ->with('5511956558187@c.us', EventsWahaEnum::MESSAGENOTUNDERSTOOD)
            ->andReturn(true);

        // Instanciando a use case com a dependência mockada
        $WebhookReceiveMessageWahaUseCase = new WebhookReceiveMessageWahaUseCase(
            $mockRepository,
            $mockHttpClient
        );

        $payload = [
            'event' => 'message',
            'payload' => [
                "from" => "5511956558187@c.us"
            ]
        ];
        $response = $WebhookReceiveMessageWahaUseCase->webhookReceiveMessage($payload);
        $this->assertFalse($response);
    }

    public function test_when_receiving_a_payload_that_does_not_have_the_senders_body_return_false_and_simulate_sending_the_message_to_the_client()
    {

        Log::info("--4--\ntest_when_receiving_a_payload_that_does_not_have_the_senders_body_return_false_and_simulate_sending_the_message_to_the_client");

        // Mock do repositório
        $mockRepository = Mockery::mock(UserRepositoryInterface::class);

        // Mock do Htt Client
        $mockHttpClient = \Mockery::mock(ClientHttpInterface::class);

        $mockHttpClient->shouldReceive('sendError')
            ->once()
            ->with('5511956558187@c.us', EventsWahaEnum::MESSAGENOTUNDERSTOOD)
            ->andReturn(true);

        // Instanciando a use case com a dependência mockada
        $WebhookReceiveMessageWahaUseCase = new WebhookReceiveMessageWahaUseCase(
            $mockRepository,
            $mockHttpClient
        );

        $payload = [
            'event' => 'message',
            'payload' => [
                "from" => "5511956558187@c.us",
                "id" => "false_5511951651712@c.us_3F21C4FC29E36870B975"
            ]
        ];
        $response = $WebhookReceiveMessageWahaUseCase->webhookReceiveMessage($payload);
        $this->assertFalse($response);
    }

    public function test_when_the_number_does_not_exist_in_the_database()
    {

        Log::info("--5--\ntest_when_the_number_does_not_exist_in_the_database");

        // Mock do repositório
        $mockRepository = Mockery::mock(UserRepositoryInterface::class);

        $mockRepository->shouldReceive('getUserWithPhoneNumber')
            ->once()
            ->with('5511956558188')
            ->andThrow(new UserNotFoundException());

        // Mock do Htt Client
        $mockHttpClient = \Mockery::mock(ClientHttpInterface::class);

        $mockHttpClient->shouldReceive('sendError')
            ->once()
            ->with('5511956558188@c.us', EventsWahaEnum::USERNOTFOUND)
            ->andReturn(true);

        // Instanciando a use case com a dependência mockada
        $WebhookReceiveMessageWahaUseCase = new WebhookReceiveMessageWahaUseCase(
            $mockRepository,
            $mockHttpClient
        );

        $payload = [
            'event' => 'message',
            'payload' => [
                "from" => "5511956558188@c.us",
                "id" => "false_5511951651712@c.us_3F21C4FC29E36870B975",
                "body" => "ola"
            ]
        ];
        $response = $WebhookReceiveMessageWahaUseCase->webhookReceiveMessage($payload);
        $this->assertFalse($response);
    }

    // public function test_when_calling_the_email_and_the_information_is_correct_return_true()
    // {

    //     Log::info("--6--\ntest_when_calling_the_email_and_the_information_is_correct_return_true");

    //     // Mock do repositório
    //     $mockRepository = Mockery::mock(UserRepositoryInterface::class);

    //     // Mock do Htt Client
    //     $mockHttpClient = \Mockery::mock(ClientHttpInterface::class);

    //     // Instanciando a use case com a dependência mockada
    //     $WebhookReceiveMessageWahaUseCase = new WebhookReceiveMessageWahaUseCase(
    //         $mockRepository,
    //         $mockHttpClient
    //     );

    //     $user = new UserEntity(
    //         "123",
    //         "matheus",
    //         "math@gmail.com",
    //         "123",
    //         false,
    //         "OTPU123",
    //         "5511956558188",
    //         false,
    //         "medium",
    //         new DateTime(),
    //         new DateTime()
    //     );

    //     $response = $WebhookReceiveMessageWahaUseCase->sendEmail($user, $user->getOtpCode());
    //     $this->assertTrue($response);
    // }
}
