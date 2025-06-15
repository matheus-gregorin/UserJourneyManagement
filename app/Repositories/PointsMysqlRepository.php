<?php

namespace App\Repositories;

use Domain\Entities\PointEntity;
use Domain\Repositories\PointRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;
use App\Models\PointMysqlModel;
use Exception;
use Illuminate\Support\Facades\Log;

class PointsMysqlRepository implements PointRepositoryInterface
{

    private PointMysqlModel $pointMysqlModel;
    private UserRepositoryInterface $userRepositoryInterface;

    public function __construct(
        PointMysqlModel $pointMysqlModel,
        UserRepositoryInterface $userRepositoryInterface
    ) {
        $this->pointMysqlModel = $pointMysqlModel;
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    public function getByUserUuidWithDates(string $userUuid, string $startDate, string $endDate)
    {
        try {
            $pointsModel = $this->pointMysqlModel
                ->where('user_uuid', $userUuid)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $points = [];
            
            if($pointsModel->isEmpty()){
                throw new Exception("Points empty ", 400);
            }

            foreach ($pointsModel as $key => $point) {
                $points[$key] = $this->modelToEntity($point)->presentation();
            }
            return $points;
        } catch (Exception $e) {
            Log::critical("Error in get points: ", ['message' => $e->getMessage()]);
            throw new Exception("Error in get points: " . $e->getMessage(), 400);
        }
    }

    public function modelToEntity($pointMysqlModel): PointEntity|Exception
    {

        if (is_null($pointMysqlModel) || empty($pointMysqlModel)) {
            Log::critical("Points not found: ", ['points' => json_encode($pointMysqlModel)]);
            throw new Exception("Points not found", 400);
        }

        $point = new PointEntity(
            $pointMysqlModel->uuid,
            $pointMysqlModel->observation,
            $pointMysqlModel->checked,
            $pointMysqlModel->updated_at,
            $pointMysqlModel->created_at
        );

        if (!empty($pointMysqlModel->user)) {
            $user = $this->userRepositoryInterface->modelToEntity($pointMysqlModel->user);
            $point->setUser($user);
        }

        return $point;
    }
}
