<?php

/**
 * Provide a admin area view for the plugin
 *
 * @link       https://github.com/coldlamper
 * @since      1.0.0
 *
 * @package    Wptides
 * @subpackage Wptides/admin/partials
 */
?>

<h2>wpTides Plugin Settings</h2>
<div>Find your station id here <a target="_blank" href="https://tidesandcurrents.noaa.gov/tide_predictions.html">https://tidesandcurrents.noaa.gov/tide_predictions.html</a></div>
<div>Use the shortcode [wptides_display] to show the tide predictions table</div>
<form action="options.php" method="post">
	<?php
	settings_fields( 'wptides_settings' );
	do_settings_sections( 'wptides-plugin-main' );
	?>
	<input
			type="submit"
			name="submit"
			class="button button-primary"
			value="<?php esc_attr_e( 'Save' ); ?>"
	/>
</form>

<br><br>
Sample output
<br><br>
<?= $sample_output ?>
