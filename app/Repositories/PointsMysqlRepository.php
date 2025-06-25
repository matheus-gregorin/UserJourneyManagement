<?php

namespace App\Repositories;

use Domain\Entities\PointEntity;
use Domain\Repositories\PointRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;
use App\Models\PointMysqlModel;
use Domain\Entities\UserEntity;
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

    public function hitPoint(PointEntity $point): PointEntity|Exception
    {
        try {
            $pointMysqlModel = $this->pointMysqlModel->create([
                'uuid' => $point->getUuid(),
                'user_uuid' => $point->getUser()->getUuid(),
                'observation' => $point->getObservation(),
                'checked' => 'false',
            ]);

            return $this->modelToEntity($pointMysqlModel);
        } catch (Exception $e) {
            Log::critical("Error in hit point: ", ['message' => $e->getMessage()]);
            throw new Exception("Error in hit point: " . $e->getMessage(), 400);
        }
    }

    public function getByUserUuidWithDates(string $userUuid, string $startDate, string $endDate)
    {
        try {
            $pointsModel = $this->pointMysqlModel
                ->where('user_uuid', $userUuid)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $points = [];

            if ($pointsModel->isEmpty()) {
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

    public function validateLastPoint(UserEntity $user): PointEntity|Exception
    {
        try {
            $pointMysqlModel = $this->pointMysqlModel
                ->where('user_uuid', $user->getUuid())
                ->orderBy('created_at', 'desc')
                ->first();

            if (is_null($pointMysqlModel)) {
                throw new Exception("No points found for user", 400);
            }

            $pointMysqlModel->checked = 'true';
            $pointMysqlModel->save();

            return $this->modelToEntity($pointMysqlModel);
        } catch (Exception $e) {
            Log::critical("Error in validate last point: ", ['message' => $e->getMessage()]);
            throw new Exception("Error in validate last point: " . $e->getMessage(), 400);
        }
    }

    public function deleteLastPoint(UserEntity $user): PointEntity|true|Exception
    {
        try {
            $pointMysqlModel = $this->pointMysqlModel
                ->where('user_uuid', $user->getUuid())
                ->orderBy('created_at', 'desc')
                ->first();

            if (is_null($pointMysqlModel)) {
                throw new Exception("No points found for user", 400);
            }

            $pointMysqlModel->delete();

            return true;
        } catch (Exception $e) {
            Log::critical("Error in validate last point: ", ['message' => $e->getMessage()]);
            throw new Exception("Error in validate last point: " . $e->getMessage(), 400);
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
