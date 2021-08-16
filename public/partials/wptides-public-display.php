<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/coldlamper
 * @since      1.0.0
 *
 * @package    Wptides
 * @subpackage Wptides/public/partials
 */
?>
<div class="wpTides">
	<div class="title">Today's Tides for <?= ucwords(strtolower($station['name'])) ?>, <?= $station['state'] ?> - Station ID <?= $station['id']?></div>
	<div class="date"><?= $station_datetime->format('F j, Y h:i:s A') ?></div>
	<?php foreach( $tide_predictions as $index=>$prediction ) : ?>
		<div class="row" style="<?= $prediction['next_tide'] ? 'font-weight:bold; ':'' ?>">
			<div class="column">
				<?= $prediction['local_time'] ?>
			</div>
			<div class="column">
				<?= $prediction['type'] == 'L' ? 'Low' : 'High' ?>
			</div>
			<div class="column">
				<?= $prediction['v'] ?> ft.
			</div>
		</div>
	<?php endforeach; ?>

</div>


