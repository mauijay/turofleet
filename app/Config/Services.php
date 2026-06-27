<?php

namespace Config;

use App\Repositories\FleetIntelligenceRepository;
use App\Services\Fleet\DecisionSupport\BusinessInsightService;
use App\Services\Fleet\DecisionSupport\DecisionSupportDashboardService;
use App\Services\Fleet\DecisionSupport\FleetOptimizationService;
use App\Services\Fleet\DecisionSupport\GuestRiskService;
use App\Services\Fleet\DecisionSupport\MaintenancePredictionService;
use App\Services\Fleet\DecisionSupport\PricingRecommendationService;
use App\Services\Fleet\DecisionSupport\RecommendationFactory;
use App\Services\Fleet\DecisionSupport\RevenueForecastService;
use App\Services\Fleet\FleetCommandCenterViewModelService;
use App\Services\Fleet\FleetCommandService;
use App\Services\Fleet\FleetHealthService;
use App\Services\Fleet\FleetStatisticsService;
use App\Services\Fleet\RevenueService;
use App\Services\Fleet\TaskService;
use App\Services\Fleet\TripAnalyticsService;
use App\Services\Fleet\VehicleAvailabilityService;
use App\Services\View\AssetManifestService;
use CodeIgniter\Config\BaseService;
use Config\DecisionSupport;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    public static function fleetIntelligenceRepository(bool $getShared = true): FleetIntelligenceRepository
    {
        if ($getShared) {
            return static::getSharedInstance('fleetIntelligenceRepository');
        }

        return new FleetIntelligenceRepository();
    }

    public static function revenueService(bool $getShared = true): RevenueService
    {
        if ($getShared) {
            return static::getSharedInstance('revenueService');
        }

        return new RevenueService(static::fleetIntelligenceRepository());
    }

    public static function fleetStatisticsService(bool $getShared = true): FleetStatisticsService
    {
        if ($getShared) {
            return static::getSharedInstance('fleetStatisticsService');
        }

        return new FleetStatisticsService(static::fleetIntelligenceRepository(), static::revenueService());
    }

    public static function fleetHealthService(bool $getShared = true): FleetHealthService
    {
        if ($getShared) {
            return static::getSharedInstance('fleetHealthService');
        }

        return new FleetHealthService(static::fleetIntelligenceRepository());
    }

    public static function vehicleAvailabilityService(bool $getShared = true): VehicleAvailabilityService
    {
        if ($getShared) {
            return static::getSharedInstance('vehicleAvailabilityService');
        }

        return new VehicleAvailabilityService(static::fleetIntelligenceRepository());
    }

    public static function tripAnalyticsService(bool $getShared = true): TripAnalyticsService
    {
        if ($getShared) {
            return static::getSharedInstance('tripAnalyticsService');
        }

        return new TripAnalyticsService(static::fleetIntelligenceRepository());
    }

    public static function taskService(bool $getShared = true): TaskService
    {
        if ($getShared) {
            return static::getSharedInstance('taskService');
        }

        return new TaskService(static::fleetIntelligenceRepository(), static::fleetHealthService());
    }

    public static function fleetCommandService(bool $getShared = true): FleetCommandService
    {
        if ($getShared) {
            return static::getSharedInstance('fleetCommandService');
        }

        return new FleetCommandService(
            static::fleetStatisticsService(),
            static::fleetHealthService(),
            static::vehicleAvailabilityService(),
            static::taskService(),
        );
    }

    public static function fleetCommandCenterViewModelService(bool $getShared = true): FleetCommandCenterViewModelService
    {
        if ($getShared) {
            return static::getSharedInstance('fleetCommandCenterViewModelService');
        }

        return new FleetCommandCenterViewModelService(
            static::fleetCommandService(),
            static::fleetStatisticsService(),
            static::fleetHealthService(),
            static::taskService(),
            static::vehicleAvailabilityService(),
            static::tripAnalyticsService(),
            static::decisionSupportDashboardService(),
        );
    }

    public static function assetManifestService(bool $getShared = true): AssetManifestService
    {
        if ($getShared) {
            return static::getSharedInstance('assetManifestService');
        }

        return new AssetManifestService();
    }

    public static function recommendationFactory(bool $getShared = true): RecommendationFactory
    {
        if ($getShared) {
            return static::getSharedInstance('recommendationFactory');
        }

        return new RecommendationFactory(config(DecisionSupport::class));
    }

    public static function pricingRecommendationService(bool $getShared = true): PricingRecommendationService
    {
        if ($getShared) {
            return static::getSharedInstance('pricingRecommendationService');
        }

        return new PricingRecommendationService(
            static::fleetStatisticsService(),
            static::revenueService(),
            config(DecisionSupport::class),
            static::recommendationFactory(),
        );
    }

    public static function fleetOptimizationService(bool $getShared = true): FleetOptimizationService
    {
        if ($getShared) {
            return static::getSharedInstance('fleetOptimizationService');
        }

        return new FleetOptimizationService(
            static::fleetStatisticsService(),
            config(DecisionSupport::class),
            static::recommendationFactory(),
        );
    }

    public static function maintenancePredictionService(bool $getShared = true): MaintenancePredictionService
    {
        if ($getShared) {
            return static::getSharedInstance('maintenancePredictionService');
        }

        return new MaintenancePredictionService(
            static::fleetHealthService(),
            config(DecisionSupport::class),
            static::recommendationFactory(),
        );
    }

    public static function guestRiskService(bool $getShared = true): GuestRiskService
    {
        if ($getShared) {
            return static::getSharedInstance('guestRiskService');
        }

        return new GuestRiskService(
            static::tripAnalyticsService(),
            config(DecisionSupport::class),
            static::recommendationFactory(),
        );
    }

    public static function revenueForecastService(bool $getShared = true): RevenueForecastService
    {
        if ($getShared) {
            return static::getSharedInstance('revenueForecastService');
        }

        return new RevenueForecastService(
            static::revenueService(),
            config(DecisionSupport::class),
            static::recommendationFactory(),
        );
    }

    public static function businessInsightService(bool $getShared = true): BusinessInsightService
    {
        if ($getShared) {
            return static::getSharedInstance('businessInsightService');
        }

        return new BusinessInsightService(
            static::fleetStatisticsService(),
            config(DecisionSupport::class),
            static::recommendationFactory(),
        );
    }

    public static function decisionSupportDashboardService(bool $getShared = true): DecisionSupportDashboardService
    {
        if ($getShared) {
            return static::getSharedInstance('decisionSupportDashboardService');
        }

        return new DecisionSupportDashboardService(
            static::pricingRecommendationService(),
            static::maintenancePredictionService(),
            static::fleetOptimizationService(),
            static::revenueForecastService(),
            static::guestRiskService(),
            static::businessInsightService(),
        );
    }
}
