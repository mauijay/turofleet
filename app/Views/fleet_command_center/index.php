<?php
/** @var array<string, mixed> $commandCenter */
/** @var array{css: ?string, js: ?string} $assets */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($commandCenter['page_title']) ?> | FleetOS</title>
    <?php if ($assets['css'] !== null): ?>
        <link rel="stylesheet" href="/build/<?= esc($assets['css'], 'attr') ?>">
    <?php endif; ?>
</head>
<body class="fleet-shell">
    <a class="skip-link" href="#main-content">Skip to main content</a>

    <div class="app-frame">
        <?= view('fleet_command_center/components/navigation', ['items' => $commandCenter['navigation']]) ?>

        <main id="main-content" class="command-main" tabindex="-1">
            <header class="top-status" aria-label="Fleet status bar">
                <div>
                    <p class="eyebrow">FleetOS Mission Control</p>
                    <h1>Fleet Command Center</h1>
                    <p class="status-copy">Operational picture as of <?= esc($commandCenter['as_of']) ?></p>
                </div>
                <div class="status-cluster" aria-label="Future integrations">
                    <span>Tesla API</span>
                    <span>Weather</span>
                    <span>Traffic</span>
                </div>
            </header>

            <section class="section" id="fleet-status" aria-labelledby="fleet-status-heading">
                <div class="section-heading">
                    <p class="eyebrow">Live Operations</p>
                    <h2 id="fleet-status-heading">Fleet Status</h2>
                </div>
                <div class="metric-grid status-grid">
                    <?php foreach ($commandCenter['fleet_status'] as $card): ?>
                        <?= view('fleet_command_center/components/metric_card', ['card' => $card]) ?>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="section mission-section" id="todays-mission" aria-labelledby="mission-heading">
                <div class="section-heading split-heading">
                    <div>
                        <p class="eyebrow">Today</p>
                        <h2 id="mission-heading">Today's Mission</h2>
                    </div>
                    <?php if ($commandCenter['mission_clear']): ?>
                        <p class="mission-clear">All clear. No operational tasks are due today.</p>
                    <?php endif; ?>
                </div>
                <div class="mission-grid">
                    <?php foreach ($commandCenter['mission'] as $task): ?>
                        <?= view('fleet_command_center/components/task_card', ['task' => $task]) ?>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="section" id="decision-support" aria-labelledby="decision-support-heading">
                <div class="section-heading">
                    <p class="eyebrow">Decision Support</p>
                    <h2 id="decision-support-heading">Recommendations</h2>
                </div>
                <?= view('fleet_command_center/components/recommendations_panel', ['decisionSupport' => $commandCenter['decision_support']]) ?>
            </section>

            <section class="section" id="fleet-activity" aria-labelledby="fleet-activity-heading">
                <div class="section-heading split-heading">
                    <div>
                        <p class="eyebrow">Fleet Vehicles</p>
                        <h2 id="fleet-activity-heading">Fleet Activity</h2>
                    </div>
                    <a class="text-link" href="#fleet-timeline">Open timeline</a>
                </div>
                <div class="vehicle-grid">
                    <?php foreach ($commandCenter['vehicles'] as $vehicle): ?>
                        <?= view('fleet_command_center/components/vehicle_card', ['vehicle' => $vehicle]) ?>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="section" id="fleet-timeline" aria-labelledby="timeline-heading">
                <div class="section-heading">
                    <p class="eyebrow">Scheduling</p>
                    <h2 id="timeline-heading">Fleet Timeline</h2>
                </div>
                <div class="timeline-layout">
                    <?= view('fleet_command_center/components/timeline_card', ['timeline' => $commandCenter['timeline']['today']]) ?>
                    <?= view('fleet_command_center/components/timeline_card', ['timeline' => $commandCenter['timeline']['tomorrow']]) ?>
                    <?= view('fleet_command_center/components/timeline_card', ['timeline' => $commandCenter['timeline']['next_7_days']]) ?>
                </div>
            </section>

            <section class="section" id="financial-snapshot" aria-labelledby="financial-heading">
                <div class="section-heading">
                    <p class="eyebrow">Owner View</p>
                    <h2 id="financial-heading">Financial Snapshot</h2>
                </div>
                <div class="financial-grid">
                    <?php foreach ($commandCenter['financial'] as $card): ?>
                        <?= view('fleet_command_center/components/financial_card', ['card' => $card]) ?>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="section" id="fleet-health" aria-labelledby="health-heading">
                <div class="section-heading">
                    <p class="eyebrow">Warnings Only</p>
                    <h2 id="health-heading">Fleet Health</h2>
                </div>
                <div class="alert-stack">
                    <?php if ($commandCenter['health_alerts'] === []): ?>
                        <div class="empty-state">No active fleet health warnings.</div>
                    <?php endif; ?>
                    <?php foreach ($commandCenter['health_alerts'] as $alert): ?>
                        <?= view('fleet_command_center/components/alert_card', ['alert' => $alert]) ?>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="section" id="executive-kpis" aria-labelledby="kpi-heading">
                <div class="section-heading">
                    <p class="eyebrow">Executive KPIs</p>
                    <h2 id="kpi-heading">Performance</h2>
                </div>
                <div class="metric-grid kpi-grid">
                    <?php foreach ($commandCenter['executive_kpis'] as $card): ?>
                        <?= view('fleet_command_center/components/metric_card', ['card' => $card]) ?>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="section" id="future-integrations" aria-labelledby="integrations-heading">
                <div class="section-heading">
                    <p class="eyebrow">Reserved Space</p>
                    <h2 id="integrations-heading">Future Integrations</h2>
                </div>
                <div class="integration-grid">
                    <?php foreach ($commandCenter['future_integrations'] as $integration): ?>
                        <div class="integration-chip">
                            <span><?= esc($integration['name']) ?></span>
                            <strong><?= esc($integration['status']) ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>

        <?= view('fleet_command_center/components/activity_panel', ['activity' => $commandCenter['activity']]) ?>
    </div>

    <?php if ($assets['js'] !== null): ?>
        <script type="module" src="/build/<?= esc($assets['js'], 'attr') ?>"></script>
    <?php endif; ?>
</body>
</html>
