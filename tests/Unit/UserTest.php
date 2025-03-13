<?php

namespace Tests\Unit;

use App\Domain\Entities\UserEntity;
use App\Domain\Enums\RolesEnum;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_should_change_the_status_from_medium_to_low()
    {
        $user = new UserEntity(
            Uuid::uuid4()->toString(),
            'Wilson Menezes',
            'wil@gmail.com',
            '12345678',
            false,
            'medium',
            new DateTime(),
            new DateTime()
        );
        $user->changeRole(RolesEnum::LOW);
        $this->assertEquals(RolesEnum::LOW, $user->getRole());
    }
}
