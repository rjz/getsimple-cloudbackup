<h3>Services</h3>
<p>Please choose the service to use for backups:</p>
<ul class="provider-list">
<?php foreach ($providers as $provider => $details): ?>
	<li class="provider">
		<a href="?id=cloudbackup&action=authorize_<?php echo $provider; ?>">
		<span class="icon"><img src="<?php echo $details['icon']; ?>" alt="<?php echo $details['title']; ?>" /></span>
		<span class="details">
			<span class="title"><?php echo $details['title']; ?></span>
			<span class="description"><?php echo $details['description']; ?></span>
		</span>
		</a>
	</li>
<?php endforeach; ?>
</ul>
