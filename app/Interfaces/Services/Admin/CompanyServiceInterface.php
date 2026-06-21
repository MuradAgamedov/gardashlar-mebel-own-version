<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface CompanyServiceInterface extends BaseServiceInterface
{
    //

    public function getCompanies();

    public function createCompany(array $data, array $translates, $request);

    public function updateCompany(\App\Models\Company $company, array $data, array $translates, $request);

    public function deleteCompany(\App\Models\Company $company);

    public function reorderCompanies(array $order);

    public function addProductToCompany(\App\Models\Company $company, int $productId);

    public function removeProductFromCompany(\App\Models\Company $company, int $productId);
}
