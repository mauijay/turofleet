<?php

namespace App\Services\Fleet\DecisionSupport;

use App\DTOs\Fleet\Recommendation;
use App\Services\Fleet\RevenueService;
use Config\DecisionSupport;
use DateTimeImmutable;

class RevenueForecastService
{
    public function __construct(
        private readonly ?RevenueService $revenueService = null,
        private readonly ?DecisionSupport $config = null,
        private readonly ?RecommendationFactory $factory = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function forecast(?DateTimeImmutable $asOf = null): array
    {
        $asOf ??= new DateTimeImmutable();
        $fromMonth = $asOf->modify('-' . ($this->config()->forecastLookbackMonths - 1) . ' months')->format('Y-m-01');
        $toMonth = $asOf->format('Y-m-01');
        $period = $this->revenue()->period($fromMonth, $toMonth);
        $monthlyAverageRevenue = $this->config()->forecastLookbackMonths <= 0 ? 0.0 : (float) $period['completed_revenue'] / $this->config()->forecastLookbackMonths;
        $monthlyAverageCost = $this->config()->forecastLookbackMonths <= 0 ? 0.0 : array_sum($period['operating_costs']) / $this->config()->forecastLookbackMonths;

        return [
            'forecast_30_day' => round($monthlyAverageRevenue, 2),
            'forecast_60_day' => round($monthlyAverageRevenue * 2, 2),
            'forecast_90_day' => round($monthlyAverageRevenue * 3, 2),
            'cash_flow_30_day' => round($monthlyAverageRevenue - $monthlyAverageCost, 2),
            'cash_flow_60_day' => round(($monthlyAverageRevenue - $monthlyAverageCost) * 2, 2),
            'cash_flow_90_day' => round(($monthlyAverageRevenue - $monthlyAverageCost) * 3, 2),
            'monthly_trend' => $period['months'],
            'forecast_confidence' => $this->confidence((float) $period['completed_revenue']),
            'assumptions' => [
                'Trailing completed revenue continues at the measured monthly average.',
                'Known recurring operating costs continue at the measured monthly average.',
                'Seasonality is not applied until seasonal history exists.',
            ],
        ];
    }

    /** @return array<int, Recommendation> */
    public function recommendations(?DateTimeImmutable $asOf = null): array
    {
        $asOf ??= new DateTimeImmutable();
        $forecast = $this->forecast($asOf);

        if ((float) $forecast['forecast_30_day'] <= 0.0) {
            return [];
        }

        $priority = (float) $forecast['cash_flow_30_day'] < 0.0 ? 'High' : 'Informational';
        $action = (float) $forecast['cash_flow_30_day'] < 0.0
            ? 'Review expenses, loan payments, and near-term booking pipeline before adding new commitments.'
            : 'Use the 30/60/90 day projection as the baseline for upcoming cash planning.';

        return [$this->factory()->make(
            'Review 30/60/90 day revenue forecast',
            'Revenue',
            $priority,
            (int) $forecast['forecast_confidence'],
            'Forecast is based on trailing completed revenue and known recurring operating costs.',
            [
                'forecast_30_day' => (float) $forecast['forecast_30_day'],
                'forecast_60_day' => (float) $forecast['forecast_60_day'],
                'forecast_90_day' => (float) $forecast['forecast_90_day'],
                'cash_flow_30_day' => (float) $forecast['cash_flow_30_day'],
            ],
            $action,
            $asOf,
            self::class,
        )];
    }

    private function confidence(float $completedRevenue): int
    {
        return $completedRevenue <= 0.0 ? 0 : min(90, 55 + ($this->config()->forecastLookbackMonths * 10));
    }

    private function revenue(): RevenueService
    {
        return $this->revenueService ?? service('revenueService');
    }

    private function config(): DecisionSupport
    {
        return $this->config ?? config(DecisionSupport::class);
    }

    private function factory(): RecommendationFactory
    {
        return $this->factory ?? new RecommendationFactory($this->config());
    }
}
