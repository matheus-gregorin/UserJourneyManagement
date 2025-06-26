<?php

namespace App\UseCase\Companies;

use App\Exceptions\CompanyNotCreatException;
use DateTime;
use Domain\Entities\CompanyEntity;
use Domain\Repositories\CompanyRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class CreateCompanyUseCase
{

    private CompanyRepositoryInterface $companyRepositoryInterface;

    public function __construct(
        CompanyRepositoryInterface $companyRepositoryInterface
    ) {
        $this->companyRepositoryInterface = $companyRepositoryInterface;
    }

    public function createCompany(array $data)
    {
        $company = new CompanyEntity(
            Uuid::uuid4()->toString(),
            $data['corporate_reason'],
            $data['fantasy_name'],
            $data['cnpj'],
            $data['plan'],
            $data['active'],
            new DateTime(),
            new DateTime()
        );

        $company = $this->companyRepositoryInterface->createCompany($company);

        if ($company) {
            return $company;
        }

        Log::critical("CreateCompanyUseCase invalid", ['data' => json_encode($data)]);
        throw new CompanyNotCreatException("Company not created", 503);
    }
}
