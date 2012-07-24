<?php $current = Helpers::localWeather(); ?>
<?php if ( $current ) : ?>
<div class="weather-container">
    <div class="item">
        <div class="left">
            <div class="icon"><img src="<?= $current['icon_url']; ?>"/></div>
        </div>
        <div class="right">
            <span class="temperature"><?= $current['temp']; ?></span>
        </div>
        <div class="clear"></div>
        <span class="meta"><?= $current['condition']; ?></span>
        <span class="meta"><?= $current['humidity']; ?></span>
        <span class="meta"><?= $current['wind']; ?></span>
    </div>
</div>
<?php endif; ?>