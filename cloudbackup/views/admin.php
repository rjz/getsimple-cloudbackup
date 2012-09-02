<?php
/**
 *	View for admin form
 */
?>
<h3 class="floated">Backup Settings</h3>

<div class="edit-nav">
	<a href="#details" role="expander">More?</a>
	<div class="clear"></div>
</div>

<div id="details">
Tell me more.
</div>

<form method="post" action="?id=cloudbackup">

	<input type="hidden" name="_action" value="backup" />
	<input type="hidden" name="_nonce" value="<?php echo get_nonce('backup', $info['id']); ?>" />

	<p>Take a moment to make sure everything's set up. Look good? Create a backup to make sure it's working:</p>

	<div class="form-actions">
		<input class="submit" type="submit" value="Make Backup Now" />
	</div>

</form>

<br />
<br />
<br />

<!-- Begin Settings form -->

<form method="post" action="?id=cloudbackup">

	<!-- Messages -->

	<?php if (isset($error)): ?>
	<div class="error">
		<p><?php echo $error; ?></p>
	</div>
	<?php elseif (isset($updated)): ?>
	<div class="updated">
		<p><?php echo $updated; ?></p>
	</div>
	<?php endif; ?>

	<input type="hidden" name="_action" value="settings" />
	<input type="hidden" name="_nonce" value="<?php echo get_nonce('settings', $info['id']); ?>" />

	<h3>Providers</h3>
	<table>
	<thead>
		<tr>
			<th width="5%"></th>
			<th width="15%">Service</th>
			<th width="55%">Description</th>
			<th width="25%">Status</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($providers as $provider => $details): ?>
		<tr>
			<td>
				<input class="radio" type="radio" name="provider" id="provider_<?php echo $provider; ?>" value="<?php echo $provider; ?>"<?php echo $settings['provider'] == $provider ? 'checked ' : '' ?><?php if (!$details['instance']->is_authorized()) echo ' disabled'; ?>/>
			</td>
			<td>
				<label for="provider_<?php echo $provider; ?>" class="control-label">
					<?php echo $details['title']; ?>
				</label>
			</td>
			<td>
				<?php echo $details['description']; ?>
			</td>
			<td>
				<div class="verify">
					<?php if (isset($settings[$provider . '_access_token'])): ?>
					<span class="authorized">Authorized</span> | <a role="remove" title="Remove <?php echo $details['title']; ?> account?" href="?id=cloudbackup&action=unauthorize_<?php echo $provider; ?>">Remove</a>
					<?php else: ?>
					<a href="?id=cloudbackup&action=authorize_<?php echo $provider; ?>" class="btn">Authorize account</a>
					<?php endif; ?>
				</div>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
	</table>

	<h3>Archivers</h3>
	<table>
	<thead>
		<tr>
			<th width="5%"></th>
			<th width="15%">Service</th>
			<th width="80%">Description</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($archivers as $archiver => $details): ?>
		<tr>
			<td>
				<input class="radio" type="radio" name="archiver" id="archiver_<?php echo $archiver; ?>" value="<?php echo $archiver; ?>"<?php echo $settings['archiver'] == $archiver ? 'checked ' : '' ?>/>
			</td>
			<td>
				<label for="archiver_<?php echo $archiver; ?>" class="control-label">
					<?php echo $details['title']; ?>
				</label>
			</td>
			<td>
				<?php echo $details['description']; ?>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
	</table>
<!--
	<h3>Files to Backup</h3>
	<ul>
		<li>
		<label for="include_everything">
			<input type="checkbox" class="checkbox" name="include_everything" id="include_everything"<?php echo $settings['include_everything'] == $archiver ? 'checked ' : '' ?>/>
			<strong>Everything</strong>
		</label>

		<li>
		<label for="include_gsconfig">
			<input type="checkbox" class="checkbox" name="include_gsconfig" id="include_gsconfig" />
			/gsconfig.php
		</label>

		<li>
		<label for="include_data">
			<input type="checkbox" class="checkbox" name="include_data" id="include_data" />
			/data
		</label>

		<li>
		<label for="include_theme">
			<input type="checkbox" class="checkbox" name="include_theme" id="include_theme" />
			/theme
		</label>

		<li>
		<label for="include_plugins">
			<input type="checkbox" class="checkbox" name="include_plugins" id="include_plugins" />
			/plugins
		</label>
	</ul>
-->
	<h3>Scheduling</h3>

	<?php if ($settings['schedule_enabled']): ?>
	<p class="updated">Next backup scheduled: <?php echo strftime('%A, %e %b %Y', $settings['schedule_next']); ?></p>
	<?php endif; ?>

	<table>
	<thead>
		<tr>
			<th>Enabled</th>
			<th><label for="schedule_frequency" class="control-label">Frequency</label></th>
			<th><label for="schedule_start" class="control-label">Start on</label></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<label for="schedule_enabled_true" class="control-label">
					<input class="radio" type="radio" name="schedule_enabled" id="schedule_enabled_true" value="1"<?php echo $settings['schedule_enabled'] == '1' ? 'checked ' : '' ?>/>
					Yes
				</label>
				
				<label for="schedule_enabled_false" class="control-label">
					<input class="radio" type="radio" name="schedule_enabled" id="schedule_enabled_false" value="0"<?php echo $settings['schedule_enabled'] == '0' ? 'checked ' : '' ?>/>
					No
				</label>
			</td>
			<td>
				<p>
					<select id="schedule_frequency" name="schedule_frequency">
						<?php foreach ($frequencies as $title => $value): ?>
							<option value="<?php echo $value; ?>"<?php echo $settings['schedule_frequency'] == $value ? ' selected' : '' ?>><?php echo $title; ?></option>
						<?php endforeach; ?>
					</select>
				</p>
			</td>
			<td>
				<p>
					<input type="text" id="schedule_start" name="schedule_start" value="<?php echo $settings['schedule_start'] ?>" />
					<a href="#" role="schedule_start_today">Use today's date</a>
				</p>
			</td>
		</tr>
	</tbody>
	</table>

	<h3>Notifications</h3>

	<p>
		<label for="notifier_email">E-mail address</label>
		<input type="email" name="notifier_email" id="notifier_email" placeholder="you@example.com" value="<?php echo $settings['notifier_email']; ?>" />

	<div class="form-actions">
		<input class="submit" type="submit" value="Save Settings" />
	</div>

</form>
