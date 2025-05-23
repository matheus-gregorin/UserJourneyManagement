<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\UserEntity;

interface PointRepositoryInterface
{
        public function getByUserUuidWithDates(string $userUuid, string $startDate, string $endDate);
}
