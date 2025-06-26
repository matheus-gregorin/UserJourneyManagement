<?php

namespace App\Http\Controllers;

use App\Exceptions\CompanyNotCreatException;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Responses\ApiResponse;
use App\UseCase\Companies\CreateCompanyUseCase;
use Domain\Enums\CodesEnum;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompaniesControllers extends Controller
{

    private CreateCompanyUseCase $createCompanyUseCase;

    public function __construct(
        CreateCompanyUseCase $createCompanyUseCase
    ) {
        $this->createCompanyUseCase = $createCompanyUseCase;
    }

    public function createCompany(CreateCompanyRequest $request)
    {
        try {
            $company = $this->createCompanyUseCase->createCompany($request->all());

            return ApiResponse::success(
                [
                    $company->toArray()
                ],
                CodesEnum::messageCompanyCreated,
                CodesEnum::codeCreated
            );
        } catch (CompanyNotCreatException $e) {
            Log::error('Error creating company: ' . $e->getMessage());
            return ApiResponse::error(
                [
                    CodesEnum::messageDataInvalid
                ],
                CodesEnum::messageCompanyNotCreated,
                CodesEnum::codeErrorBadRequest
            );
        } catch (Exception $e) {
            Log::error('Error creating company: ' . $e->getMessage());
            return ApiResponse::error(
                [
                    CodesEnum::messageInternalServerError
                ],
                CodesEnum::messageCompanyNotCreated,
                CodesEnum::codeErrorBadRequest
            );
        }
    }

    public function getAllCompanies(Request $request)
    {
        // Logic to retrieve all companies
        return response()->json(['message' => 'Retrieved all companies successfully'], 200);
    }
}
