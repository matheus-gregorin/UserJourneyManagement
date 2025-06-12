<?php

namespace Domain\UseCase;

use Domain\Entities\UserEntity;

interface OptionUseCaseInterface
{
    public function receive(UserEntity $user, string $number, ?string $messageId = null);
}
