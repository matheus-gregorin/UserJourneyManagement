<?php

namespace Domain\Repositories;

use Domain\Entities\CompanyEntity;

interface CompanyRepositoryInterface
{
    public function createCompany(CompanyEntity $company);
}
