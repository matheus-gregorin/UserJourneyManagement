<?php

namespace App\Factorys;

use App\UseCase\CheckThePointsHitTodayUseCase;

class OptionsFactory
{
    public static function getOptions(string $option)
    {
        switch ($option) {
            case 'checkThePointsHitToday':
                return app(CheckThePointsHitTodayUseCase::class);
                break;
            
            default:
                # code...
                break;
        }
    }
}
