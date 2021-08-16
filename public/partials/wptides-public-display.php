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
	<div class="title" style="text-align: center;">Today's Tides for <?= ucwords(strtolower($station['name'])) ?>, <?= $station['state'] ?> - Station ID <?= $station['id']?></div>
	<div class="date" style="text-align: center;"><?= $station_datetime->format('F j, Y h:i:s A') ?></div>
	<?php foreach( $tide_predictions as $index=>$prediction ) : ?>
		<div class="row" style="<?= $prediction['next_tide'] ? 'font-weight:bold; ':'' ?>display: flex; flex-direction: row; flex-wrap: wrap; width: 100%;">
			<div class="column" style="align-items: center; display: flex; flex-direction: column; flex-basis: 100%; flex: 1;">
				<?= $prediction['local_time'] ?>
			</div>
			<div class="column" style="align-items: center; display: flex; flex-direction: column; flex-basis: 100%; flex: 1;">
				<?= $prediction['type'] == 'L' ? 'Low' : 'High' ?>
			</div>
			<div class="column" style="align-items: center; display: flex; flex-direction: column; flex-basis: 100%; flex: 1;">
				<?= $prediction['v'] ?> ft.
			</div>
		</div>
	<?php endforeach; ?>

</div>


