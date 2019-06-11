<?php
	global $lumise;

	$section = 'shape';
	$fields = $lumise_admin->process_data(array(
		array(
			'type' => 'input',
			'name' => 'name',
			'label' => $lumise->lang('Name'),
			'required' => true
		),
		array(
			'type' => 'shape',
			'name' => 'content',
			'label' => $lumise->lang('Content'),
			'desc' => $lumise->lang('Only accept SVG under plain text')
		),
		array(
			'type' => 'input',
			'name' => 'order',
			'type_input' => 'number',
			'label' => $lumise->lang('Order'),
			'default' => 0,
			'desc' => $lumise->lang('Ordering of item with other.')
		),
		array(
			'type' => 'toggle',
			'name' => 'active',
			'label' => $lumise->lang('Active'),
			'default' => 'yes',
			'value' => null
		),
	), 'shapes');

?>

<div class="lumise_wrapper" id="lumise-<?php echo $section; ?>-page">
	<div class="lumise_content">
		<?php
			$lumise->views->detail_header(array(
				'add' => $lumise->lang('Add new shape'),
				'edit' => $fields[0]['value'],
				'page' => $section
			));
		?>
		<form action="<?php echo $lumise_router->getURI(); ?>lumise-page=<?php
			echo $section.(isset($_GET['callback']) ? '&callback='.$_GET['callback'] : '');
		?>" id="lumise-clipart-form" method="post" class="lumise_form" enctype="multipart/form-data">

			<?php $lumise->views->tabs_render($fields); ?>

			<div class="lumise_form_group lumise_form_submit">
				<input type="submit" value="<?php echo $lumise->lang('Save Shape'); ?>"/>
				<input type="hidden" name="do" value="action" />
				<a class="lumise_cancel" href="<?php echo $lumise_router->getURI();?>lumise-page=<?php echo $section; ?>s">
					<?php echo $lumise->lang('Cancel'); ?>
				</a>
				<input type="hidden" name="lumise-section" value="<?php echo $section; ?>">
			</div>
		</form>
	</div>
</div>
