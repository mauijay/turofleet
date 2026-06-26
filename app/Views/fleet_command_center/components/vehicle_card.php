<?php /** @var array<string, mixed> $vehicle */ ?>
<article class="vehicle-card tone-<?= esc($vehicle['priority'], 'attr') ?>">
    <div class="vehicle-photo" aria-label="Vehicle photo placeholder">
        <span>Photo pending</span>
    </div>
    <div class="vehicle-body">
        <div class="card-row">
            <div>
                <h3><?= esc($vehicle['fleet_code']) ?></h3>
                <p><?= esc($vehicle['model_label']) ?></p>
            </div>
            <?= view('fleet_command_center/components/status_badge', ['label' => $vehicle['segment'], 'tone' => $vehicle['segment_tone']]) ?>
        </div>
        <div class="vehicle-status-row">
            <?= view('fleet_command_center/components/status_badge', ['label' => $vehicle['status_label'], 'tone' => $vehicle['priority']]) ?>
            <span><?= esc($vehicle['cleaning_status']) ?></span>
        </div>
        <dl class="vehicle-facts">
            <div><dt>Battery</dt><dd><?= esc($vehicle['current_battery_label']) ?></dd></div>
            <div><dt>Location</dt><dd><?= esc($vehicle['current_location_label']) ?></dd></div>
            <div><dt>Odometer</dt><dd><?= esc($vehicle['current_odometer_label']) ?></dd></div>
            <div><dt>Next</dt><dd><?= esc($vehicle['next_reservation_label']) ?></dd></div>
        </dl>
        <?php if ($vehicle['issues'] !== []): ?>
            <ul class="issue-list">
                <?php foreach ($vehicle['issues'] as $issue): ?>
                    <li><?= esc($issue) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</article>
