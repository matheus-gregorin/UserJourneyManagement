<?php

namespace App\Factorys;

use App\UseCase\CallSupportUseCase;
use App\UseCase\CheckThePointsHitTodayUseCase;
use App\UseCase\CheckThePointsOfTheMounthUseCase;
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
                return app(CheckThePointsOfTheMounthUseCase::class);
                break;
            
            case 'support':
                return app(CallSupportUseCase::class);
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
