<?php

namespace App\Repositories;

use App\Domain\Repositories\PointRepositoryInterface;
use App\Models\PointMysqlModel;

class PointsMysqlRepository implements PointRepositoryInterface
{

    private PointMysqlModel $pointMysqlModel;

    public function __construct(PointMysqlModel $pointMysqlModel)
    {
        $this->pointMysqlModel = $pointMysqlModel;
    }

    public function getByUserUuidWithDates(string $userUuid, string $startDate, string $endDate)
    {
        $points = $this->pointMysqlModel
            ->where('user_uuid', $userUuid)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        dd("uuid", $userUuid, $points, count($points));
    }
}
