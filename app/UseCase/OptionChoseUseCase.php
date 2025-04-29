<?php

namespace App\UseCase;

use App\Domain\HttpClients\ClientHttpInterface;

class OptionChoseUseCase
{

    private ClientHttpInterface $clientHttp;

    public function __construct(ClientHttpInterface $clientHttp)
    {
        $this->clientHttp = $clientHttp;
    }

    public function checkThePointsHitToday()
    {
        dd("FUNCAO A SER IMPLEMENTADA");
    }
}
