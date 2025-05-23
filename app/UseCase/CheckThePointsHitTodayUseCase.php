<?php

namespace App\UseCase;

use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\CheckThePointsHitTodayUseCaseInterface;
use App\Domain\Repositories\OptionUseCaseInterface;
use App\Domain\Repositories\PointRepositoryInterface;

class CheckThePointsHitTodayUseCase implements OptionUseCaseInterface
{
    private PointRepositoryInterface $pointRepository;
    
    public function __construct(PointRepositoryInterface $pointRepository)
    {
        $this->pointRepository = $pointRepository;
    }

    public function receive(UserEntity $user)
    {
        $this->pointRepository->getByUserUuidWithDates($user->getUuid(), date('2025-01-01 00:00:00'), date('2025-12-31 23:59:59'));
    }

}
