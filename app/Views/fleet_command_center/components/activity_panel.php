<?php /** @var array<string, mixed> $activity */ ?>
<aside class="activity-panel" aria-label="Activity panel">
    <div class="panel-card">
        <p class="eyebrow">Activity</p>
        <h2>Operations Queue</h2>
        <dl class="activity-list">
            <div><dt>Today</dt><dd><?= esc((string) $activity['today_count']) ?></dd></div>
            <div><dt>Tomorrow</dt><dd><?= esc((string) $activity['tomorrow_count']) ?></dd></div>
            <div><dt>Urgent</dt><dd><?= esc((string) $activity['urgent_count']) ?></dd></div>
        </dl>
    </div>
    <div class="panel-card">
        <p class="eyebrow">Future Signals</p>
        <h2>External Context</h2>
        <ul class="signal-list">
            <li>Weather alerts <span><?= esc($activity['weather_status']) ?></span></li>
            <li>Traffic alerts <span><?= esc($activity['traffic_status']) ?></span></li>
            <li>Battery alerts <span><?= esc($activity['battery_status']) ?></span></li>
        </ul>
    </div>
</aside>
