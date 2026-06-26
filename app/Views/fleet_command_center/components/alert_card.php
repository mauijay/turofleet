<?php /** @var array<string, mixed> $alert */ ?>
<article class="alert-card tone-<?= esc($alert['tone'], 'attr') ?>">
    <div>
        <h3><?= esc($alert['label']) ?></h3>
        <p><?= esc($alert['message']) ?></p>
    </div>
    <span class="count-pill"><?= esc((string) $alert['count']) ?></span>
</article>
