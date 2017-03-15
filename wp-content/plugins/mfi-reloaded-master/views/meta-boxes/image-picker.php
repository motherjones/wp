<div class="mfi-reloaded-image-picker"
		data-mfi-reloaded-image-id="<?php printf('%d', $image_id); ?>"
		data-mfi-reloaded-name="<?php esc_attr_e($image_picker_name); ?>"
		data-mfi-reloaded-select="<?php esc_attr_e($image_picker_args['labels']['popup_select']); ?>"
		data-mfi-reloaded-title="<?php esc_attr_e($image_picker_args['labels']['popup_title']); ?>">

	<div class="mfi-reloaded-image-picker-preview"><?php echo $image; ?></div>

	<a class="mfi-reloaded-image-picker-remove" href="#"><?php esc_html_e($image_picker_args['labels']['remove']); ?></a>
	<a class="mfi-reloaded-image-picker-set" href="#"><?php esc_html_e($image_picker_args['labels']['set']); ?></a>
</div>