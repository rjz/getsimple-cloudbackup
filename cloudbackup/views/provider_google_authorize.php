<ol>
	<li>Authorize Google Drive
<a href="#" class="btn authorize">Authorize Google Drive</a>
	</li>
	<li>Paste Auth token
		<form method="post">
			<p>
				<label for="auth_token">Authorization Token</label>
				<input type="text" id="auth_token" name="auth_token" value="" />
			</p>

			<div class="form-actions">
				<input type="submit" value="Save Auth Token" />
			</div>
		</form>
	</li>
</ol>

<script>
(function ($) {
	var url = '<?php echo $auth_url; ?>';
	$('.authorize').click(function (e) {
		e.preventDefault();
		window.open(url, '_blank', 'width=600,height=400,menubar=0,toolbar=0,status=0,scrollbars=0');
	});
})(window.jQuery);
</script>
