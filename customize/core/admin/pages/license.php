<?php
	global $lumise;
	$key = $lumise->get_option('purchase_key');
	$key_valid = ($key === null || empty($key) || strlen($key) != 36 || count(explode('-', $key)) != 5) ? false : true;
?>

<div class="lumise_wrapper">

	<div id="lumise-license">
		<h1>
			<?php echo $lumise->lang('License verification'); ?>
		</h1>
		<?php if ($key_valid) { ?>
		<div class="lumise-update-notice success">
			<?php echo $lumise->lang('Your license has been verified, now your Lumise will be updated automatically and have access to all features'); ?>.
		</div>
		<?php } else { ?>
		<div class="lumise-update-notice">
			<?php echo $lumise->lang('You must verify your purchase code before updating and access to all features'); ?>.	
		</div>
		<?php } ?>
		<?php $lumise->views->header_message(); ?>
		<form action="" method="POST" id="lumise-license-form">
			<?php if ($key_valid) { ?>
			<input type="password" name="key" readonly size="58" value="<?php echo $key; ?>" placeholder="<?php echo $lumise->lang('Enter your purchase code'); ?>" />
			<input type="hidden" name="do_action" value="revoke-license" />
			<button type="submit" class="lumise_btn danger">
				<?php echo $lumise->lang('Revoke this license'); ?>
			</button>
			<script type="text/javascript">
				jQuery('#lumise-license-form').on('submit', function(e) {
					if (!confirm("<?php echo $lumise->lang('Are you sure? After revoking the license you can use it to verify another domain but you will not be able to use it to verify this domain again'); ?>.")) {
						e.preventDefault();
					} else {
						jQuery('#lumise-license-form button.lumise_btn').html('<i style="font-size: 16px;" class="fa fa-circle-o-notch fa-spin fa-fw"></i> please wait..');
					}
				});
			</script>
			<?php } else { ?>
			<input type="password" name="key" size="58" value="<?php echo $key; ?>" placeholder="<?php echo $lumise->lang('Enter your purchase code'); ?>" />
			<input type="hidden" name="do_action" value="verify-license" />
			<button type="submit" class="lumise_btn primary loaclik">
				<?php echo $lumise->lang('Verify Now'); ?>
			</button>
			&nbsp; 
			<a class="lumise_btn" href="https://www.lumise.com/pricing/?utm_source=client-site&utm_medium=text&utm_campaign=license-page&utm_term=links&utm_content=<?php echo $lumise->connector->platform; ?>" target=_blank>
				<?php echo $lumise->lang('Buy a license'); ?>
			</a>
			<?php } ?>
		</form>

		<h3 class="mb0"><?php echo $lumise->lang('More details'); ?></h3>
		<hr>
		<ul>
			<li><?php echo $lumise->lang('The license key is the purchase code which was created at Envato after purchasing the product'); ?>.</li>
			<li><?php echo $lumise->lang('You can not use a license for more than one domain, but you can revoke it from an unused domain to verify the new domain'); ?>.</li>
			<li><?php echo $lumise->lang('Once you have revoked your license at a domain, you will not be able to use it to verify that domain again'); ?>.</li>
			<li><?php echo $lumise->lang('Each license can only be verified up to 3 times, including your localhost and excluding subdomains or subfolders'); ?>.</li>
			<li>
				<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code" target=_blank><?php echo $lumise->lang('How to find the purchase code'); ?>?</a> 
				<?php echo $lumise->lang('If you do not have a license yet'); ?> 
				<a href="https://www.lumise.com/pricing/?utm_source=client-site&utm_medium=text&utm_campaign=license-page&utm_term=links&utm_content=<?php echo $lumise->connector->platform; ?>" target=_blank><?php echo $lumise->lang('Buy Lumise to get a purchase code'); ?>.</a>
			</li>
		</ul>
	</div>

</div>
