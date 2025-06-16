<?php

namespace App\Factorys;

use App\UseCase\CheckThePointsHitTodayUseCase;
use App\UseCase\HitPointUseCase;

class OptionsFactory
{
    public static function getOptions(string $option)
    {
        switch ($option) {
            case 'checkThePointsHitToday':
                return app(CheckThePointsHitTodayUseCase::class);
                break;

            case 'hitPoint':
                return app(HitPointUseCase::class);
                break;
            
            default:
                break;
        }
    }
}
