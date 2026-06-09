<?php

namespace App\Service;

use App\Repository\CompanyRepository;

class CompanyService
{
    public function __construct(private CompanyRepository $companyRepository) 
    {
    }

    public function getMainCompany()
    {
        return $this->companyRepository->findOneBy([]);
    }

    // public function isOpenNow(): bool
    // {
    //     $company = $this->getMainCompany();

    //     $now = new \DateTime();

    //     return $now >= $company->getOpeningTime()
    //         && $now <= $company->getClosingTime();
    // }
}