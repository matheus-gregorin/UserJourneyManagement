<?php

namespace Domain\Repositories;

use Domain\Entities\PointEntity;
use Domain\Entities\UserEntity;
use Exception;
use Illuminate\Foundation\Auth\User;

interface PointRepositoryInterface
{
        public function hitPoint(PointEntity $point): PointEntity|Exception;
        public function getByUserUuidWithDates(string $userUuid, string $startDate, string $endDate);
        public function validateLastPoint(UserEntity $user);
        public function deleteLastPoint(UserEntity $user);
}
