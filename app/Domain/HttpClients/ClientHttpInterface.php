<?php

namespace App\Domain\HttpClients;

interface ClientHttpInterface
{
    public function sendViewMessage(string $number, string $messageId);

    public function startTyping(string $number);

    public function stopTyping(string $number);

    public function sendResponse(string $number, string $response): bool;

    public function sendButtons(string $number): bool;

    public function sendError();
}
