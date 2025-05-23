<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\UserEntity;

interface OptionUseCaseInterface
{
    public function receive(UserEntity $user, string $number, ?string $messageId = null);
}
