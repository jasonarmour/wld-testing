<?php
$forms = (array) get_sub_field( 'forms' ); // Use field "Forms" select multiple values
?>
<div class="forms-tabs">
	<div class="inner">
		<div class="wrap">
			<?php if ( $forms ) : ?>
				<div class="tabs-nav">
					<?php foreach ( $forms as $form ) : ?>
						<div class="tab-link"><?php echo $form['title']; ?></div>
					<?php endforeach; ?>
				</div>
				<div class="tabs-content">
					<?php foreach ( $forms as $form_id ) : ?>
						<div class="tab">
							<div class="form">
								<?php if ( $form_id ) : ?>
									<?php
									gravity_form(
										$form_id,
										false,
										false,
										false,
										null,
										true,
										false,
										false
									);
									?>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
