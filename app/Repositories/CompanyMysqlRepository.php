<?php

namespace App\Repositories;

use App\Exceptions\CompanyNotCreatException;
use App\Exceptions\CompanyNotFoundException;
use App\Models\CompanyMysqlModel;
use Domain\Entities\CompanyEntity;
use Domain\Repositories\CompanyRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class CompanyMysqlRepository implements CompanyRepositoryInterface
{
    private CompanyMysqlModel $companyMysqlModel;

    public function __construct(CompanyMysqlModel $companyMysqlModel)
    {
        $this->companyMysqlModel = $companyMysqlModel;
    }

    public function createCompany(CompanyEntity $company)
    {
        try {
            return $this->modelToEntity($this->companyMysqlModel::create($company->toArray()), true);
        } catch (Exception $e) {
            Log::critical("Error in created company: ", ['message' => $e->getMessage()]);
            throw new CompanyNotCreatException("Error in created company", 400);
        }
    }

    public function modelToEntity($companyMysqlModel, bool $removePass = false): CompanyEntity|Exception
    {

        if (is_null($companyMysqlModel)) {
            Log::critical("Company not found: ", ['user' => json_encode($companyMysqlModel)]);
            throw new CompanyNotFoundException("Company not found", 400);
        }

        $password = $removePass ? "" : $companyMysqlModel->password;

        $company = new CompanyEntity(
            $companyMysqlModel->uuid,
            $companyMysqlModel->corporate_reason,
            $companyMysqlModel->fantasy_name,
            $companyMysqlModel->cnpj,
            $companyMysqlModel->plan,
            $companyMysqlModel->active,
            $companyMysqlModel->created_at,
            $companyMysqlModel->updated_at
        );

        return $company;
    }
}
