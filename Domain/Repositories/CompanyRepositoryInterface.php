<?php

namespace Domain\Repositories;

use Domain\Entities\CompanyEntity;
use Exception;

interface CompanyRepositoryInterface
{
    public function createCompany(CompanyEntity $company);
    public function getCompanyByUuid(string $uuid): CompanyEntity|Exception;
    public function modelToEntity($companyModel): CompanyEntity|Exception;
}
