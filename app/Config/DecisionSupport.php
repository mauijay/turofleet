<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class DecisionSupport extends BaseConfig
{
    public int $recommendationTtlDays = 7;

    public int $pricingLookbackDays = 90;

    public int $forecastLookbackMonths = 3;

    public float $pricingHighOccupancyDelta = 0.15;

    public float $pricingLowOccupancyDelta = -0.15;

    public float $adrCompetitivenessDelta = 0.10;

    public int $priceStepDollars = 5;

    public float $highUtilization = 0.80;

    public float $lowUtilization = 0.35;

    public float $revenueConcentrationShare = 0.55;

    public float $segmentCapacityUtilization = 0.75;

    public int $maintenanceHorizonDays = 30;

    public int $registrationHorizonDays = 45;

    public int $insuranceHorizonDays = 45;

    public int $staleCleaningDays = 1;

    public int $longTermRentalDays = 14;

    public float $guestCancellationRate = 0.20;
}
