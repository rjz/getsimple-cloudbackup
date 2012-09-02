<h3>Authorize FTP Account</h3>

<?php if (isset($error)): ?>
<div class="error">
	<p><?php echo $error; ?></p>
</div>
<?php endif; ?>

<form method="post" action="?id=backup&action=authorize_ftp">

	<input type="hidden" name="_action" value="authorize_ftp" />
	<input type="hidden" name="_nonce" value="<?php echo get_nonce('authorize_ftp', $info['id']); ?>" />

	<p>
		<label for="ftp_server">Server</label>
		<input type="text" id="ftp_server" name="ftp_server" value="" />
	</p>

	<p>
		<label for="ftp_username">Username</label>
		<input type="text" id="ftp_username" name="ftp_username" value="" />
	</p>

	<p>
		<label for="ftp_password">Password</label>
		<input type="password" id="ftp_password" name="ftp_password" value="" />
	</p>

	<div class="form-actions">
		<input class="submit" type="submit" value="Authorize" />
	</div>

</form>
