<?php $forecast = Helpers::forecast(); ?>
<div class="weather-container">
<?php if ( $forecast ) : $x = 0; $count = (count( $forecast ) - 1); ?>
    <?php foreach( $forecast as $tmp ) : ?>
        <?php if ( $x == $count ) $last = '-last'; else $last = null; ?>
        <div class="item<?= $last; ?>">
            <span class="meta day-of-week"><?= $tmp['day']; ?></span>
            <div class="icon"><img src="<?= $tmp['icon_url']; ?>" /></div>
            <span class="meta high-low">
                <?= $tmp['high']; ?>&deg;
                <?php if ( $tmp['low'] ) : ?><?= $tmp['low']; ?>&deg;<?php endif; ?>
            </span>
        </div>
    <?php $x++; endforeach;?>
<?php else: ?>
    <p>Unable to determine weather conditions.</p>
<?php endif; ?>
</div>