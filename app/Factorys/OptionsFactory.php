<?php

namespace App\Factorys;

use App\UseCase\CheckThePointsHitTodayUseCase;
use App\UseCase\checkThePointsOfTheMounthUseCase;
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

            case 'checkThePointsOfTheMounth':
                return app(checkThePointsOfTheMounthUseCase::class);
                break;
            
            case 'support':
                // return app(SupportUseCase::class);
                return null;
                break;

            case 'finalize':
                // return app(FinalizeUseCase::class);
                return null;
                break;

            default:
                break;
        }
    }
}
