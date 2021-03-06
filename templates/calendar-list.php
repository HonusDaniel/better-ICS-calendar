<?php
// Require object
if (empty($ics_data)) { return false; }

global $R34ICS;
global $wp_locale;

$start_of_week = get_option('start_of_week', 0);

$date_format = r34ics_date_format($args['format']);


if($date < $now) {
    echo 'date is in the past';
}

$ics_calendar_classes = array(
	'ics-calendar',
	'layout-list',
	(!empty($args['hidetimes']) ? ' hide_times' : ''),
	(!empty($args['toggle']) ? ' r34ics_toggle' : ''),
	(count((array)$ics_data['urls']) > 1 ? ' multi-feed' : ''),
);

// Feed colors custom CSS
if (!empty($ics_data['colors'])) {
	r34ics_feed_colors_css($ics_data, true);
}
?>

<section class="<?php echo esc_attr(implode(' ', $ics_calendar_classes)); ?>" id="<?php echo esc_attr($ics_data['guid']); ?>">

	<?php
	// Title and description
	if (!empty($ics_data['title'])) {
		?>
		<h2 class="ics-calendar-title"><?php echo $ics_data['title']; ?></h2>
		<?php
	}
	if (!empty($ics_data['description'])) {
		?>
		<p class="ics-calendar-description"><?php echo $ics_data['description']; ?></p>
		<?php
	}

	// Empty calendar message
	if (empty($ics_data['events'])) {
		?>
		<p class="ics-calendar-error"><?php _e('No events found.', 'r34ics'); ?></p>
		<?php
	}

	// Display calendar
	else {

		// Actions before rendering calendar wrapper (can include additional template output)
		do_action('r34ics_display_calendar_before_wrapper', $view, $args, $ics_data);

		// Color code key
		echo $R34ICS->color_key_html($args, $ics_data);

		// Build monthly calendars
		$i = 0;
		$skip_i = 0;
		foreach (array_keys((array)$ics_data['events']) as $year) {
			for ($m = 1; $m <= 12; $m++) {
				$month = $m < 10 ? '0' . $m : '' . $m;
				$ym = $year . $month;
				if ($ym < $ics_data['earliest']) { continue; }
				if ($ym > $ics_data['latest']) { break(2); }
				$first_date = gmmktime(0,0,0,$month,1,$year);
				$month_label = ucwords(wp_date($args['formatmonthyear'], gmmktime(0,0,0,$month,1,$year), $R34ICS->tz));

				// Build month's calendar
				if (isset($ics_data['events'][$year][$month])) {
					?>
					<article class="ics-calendar-list-wrapper" data-year-month="<?php echo date('Ym', gmmktime(0,0,0,$month,1,$year)); ?>">

						<!-- <h3 class="ics-calendar-label"> <?php // echo $month_label; ?></h3>-->

						<?php
						foreach ((array)$ics_data['events'][$year][$month] as $day => $day_events) {
							// Skip day entirely if its events are all under the skip limit (to avoid displaying a header with no events)
							if (!empty($args['skip']) && $skip_i + count($day_events) < $args['skip']) {
								$skip_i = $skip_i + count($day_events); continue;
							}


							?>
							<h4><?php echo wp_date($date_format, gmmktime(0,0,0,$month,$day,$year), $R34ICS->tz); ?></h4>
							<dl class="events">
								<?php

								foreach ((array)$day_events as $time => $events) {
									$all_day_indicator_shown = !empty($args['hidealldayindicator']);
									foreach ((array)$events as $event) {
										// Skip event if under the skip limit
										if (!empty($args['skip']) && $skip_i < $args['skip']) {
											$skip_i++; continue;
										}

									$isittoday = date( 'j', current_time( 'timestamp', 0 ) );
									$thisistoday = wp_date('j', gmmktime(0,0,0,$month,$day,$year), $R34ICS->tz);
									if (array_key_exists('multiday_position', $event) && $isittoday == $thisistoday) {
										?><div>
								</div><div class="jedenblok" id="blok_cislo_<?php echo $i++; ?>">
								<dt class="time"><?php
										echo $event['start'];
                    $konci = $event['dtend_time'];
                    echo " - ";
                    echo substr($konci, 0, 2);
                    echo ":";
                    echo substr($konci, 2, 2);



										?> </dt> <?php
										echo $event['label'];
									}


										if ($isittoday >= $thisistoday) {
											//echo "hello";
											$when = date( 'H:i', current_time( 'timestamp', 0 ) );
										if($when > $event['end']) {
											$skip_i; continue;
												}
																		}

										//print_r($event);



										$has_desc = r34ics_has_desc($args, $event);
										if ($time == 'all-day') {
											if (!$all_day_indicator_shown) {
												?>
												<dt class="all-day-indicator" data-feed-key="<?php echo intval($event['feed_key']); ?>"><?php _e('All Day', 'r34ics'); ?></dt>
												<?php
												$all_day_indicator_shown = true;
											}
											?>

											<dd class="<?php echo r34ics_event_css_classes($event, $time, $args); ?>" data-feed-key="<?php echo intval($event['feed_key']); ?>">
												<?php
												// Event label (title)
												echo $R34ICS->event_label_html($args, $event, (!empty($has_desc) ? array('has_desc') : null));

												// Sub-label
												echo $R34ICS->event_sublabel_html($args, $event, null);

												// Description/Location/Organizer
												//echo $R34ICS->event_description_html($args, $event, null, $has_desc);
												?>
											</dd></div>
											<?php
										}
										else {
											if (!empty($event['start'])) {
												?>
												<div class="jedenblok" id="blok_cislo_<?php echo $i++; ?>">
								<dt class="time" data-feed-key="<?php echo intval($event['feed_key']); ?>" ><?php
												echo $event['start'];
					if ($event['multiday_position'] == 1) {
					$konci = $event[dtend_time];
                    echo " - ";
                    echo substr($konci, 0, 2);
                    echo ":";
                    echo substr($konci, 2, 2);
}


												if (!empty($event['end']) && $event['end'] != $event['start']) {

													if (empty($args['showendtimes'])) {
														?>
														<span class="show_on_hover">&#8211; <?php echo $event['end']; ?></span>
														<?php
													}
													else {
														?>
														&#8211; <?php echo $event['end']; ?>
														<?php
													}
												}
												?></dt>
												<?php
											}
											?>



											<dd class="<?php echo r34ics_event_css_classes($event, $time, $args); ?>" data-feed-key="<?php echo intval($event['feed_key']); ?>">
												<?php
												// Event label (title)
												echo $R34ICS->event_label_html($args, $event, (!empty($has_desc) ? array('has_desc') : null));

												// Sub-label
												echo $R34ICS->event_sublabel_html($args, $event, null);

												// Description/Location/Organizer
												echo $R34ICS->event_description_html($args, $event, null, $has_desc);
												?>
											</dd></div>
											<?php
										}
										$i++;
										if (!empty($args['count']) && $i >= intval($args['count'])) { break(5); }
									}
								}
								?>
							</dl>
							<?php
						}
						?>

					</article>
					<?php
				}
			}
		}

		// Actions after rendering calendar wrapper (can include additional template output)
		do_action('r34ics_display_calendar_after_wrapper', $view, $args, $ics_data);
add_filter('r34ics_display_calendar_exclude_event', function($exclude, $event, $args) {
	// Add your logic to determine whether or not to exclude this event (set $exclude value)
	return $exclude;
}, 10, 3);
	}
	?>

</section>
