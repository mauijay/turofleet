<?php /** @var array<string, mixed> $card */ ?>
<a class="metric-card tone-<?= esc($card['tone'], 'attr') ?>" href="<?= esc($card['href'], 'attr') ?>">
    <span><?= esc($card['label']) ?></span>
    <strong><?= esc((string) $card['value']) ?></strong>
    <small><?= esc($card['detail']) ?></small>
</a>
