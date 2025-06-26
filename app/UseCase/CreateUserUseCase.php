<?php

namespace App\UseCase;

use App\Exceptions\CompanyNotFoundException;
use Domain\Entities\UserEntity;
use Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\UserNotCreatedException;
use DateTime;
use Domain\Repositories\CompanyRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class CreateUserUseCase
{

    private UserRepositoryInterface $userRepository;
    private CompanyRepositoryInterface $companyRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }

    public function createUser(array $data)
    {
        $company = $this->companyRepository->getCompanyByUuid($data['company_uuid']);
        if (!$company) {
            Log::critical("CreateUserUseCase invalid", ['data' => json_encode($data)]);
            throw new CompanyNotFoundException("Company not found", 400);
        }

        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new UserEntity(
            Uuid::uuid4()->toString(),
            $data['name'],
            $data['email'],
            $password,
            false,
            $data['phone'],
            $data['is_admin'],
            $data['role'],
            $company,
            new DateTime(),
            new DateTime()
        );

        $user = $this->userRepository->createUser($user);
        if ($user) {
            $user = $user->toArray();
            unset($user['password']);
            // unset($user['otp_code']);
            unset($user['id']);
            return $user;
        }

        Log::critical("CreateUserUseCase invalid", ['data' => json_encode($data)]);
        throw new UserNotCreatedException("User not created", 503);
    }
}
