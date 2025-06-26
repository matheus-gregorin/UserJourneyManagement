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

    public function getCompanyByUuid(string $uuid): CompanyEntity|Exception
    {
        try {
            $companyModel = $this->companyMysqlModel::where('uuid', '=', $uuid)->first();
            return $this->modelToEntity($companyModel);
        } catch (Exception $e) {
            Log::critical("Error in get company by uuid: ", ['message' => $e->getMessage()]);
            throw new CompanyNotFoundException("Company not found", 400);
        }
    }

    public function modelToEntity($companyModel, bool $removePass = false): CompanyEntity|Exception
    {

        if (is_null($companyModel)) {
            Log::critical("Company not found: ", ['user' => json_encode($companyModel)]);
            throw new CompanyNotFoundException("Company not found", 400);
        }

        $password = $removePass ? "" : $companyModel->password;

        $company = new CompanyEntity(
            $companyModel->uuid,
            $companyModel->corporate_reason,
            $companyModel->fantasy_name,
            $companyModel->cnpj,
            $companyModel->plan,
            $companyModel->active,
            $companyModel->created_at,
            $companyModel->updated_at
        );

        return $company;
    }
}
