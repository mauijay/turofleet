<?php /** @var array<string, string> $card */ ?>
<article class="financial-card">
    <span><?= esc($card['label']) ?></span>
    <strong><?= esc($card['value']) ?></strong>
    <small><?= esc($card['detail']) ?></small>
</article>
