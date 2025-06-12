<?php

namespace Domain\Repositories;

interface PointRepositoryInterface
{
        public function getByUserUuidWithDates(string $userUuid, string $startDate, string $endDate);
}
