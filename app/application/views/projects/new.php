<h3>
	<?php echo __('tinyissue.create_a_new_project');?>
	<span><?php echo __('tinyissue.create_a_new_project_description');?></span>
</h3>

<div class="pad">

	<form method="post" action="" id="submit-project" enctype="multipart/form-data">
		<table class="form" style="width: 80%;">
			<tr>
				<th style="width: 10%;"><?php echo __('tinyissue.name');?></th>
				<td><input type="text" name="name"   value="<?php echo Input::old('name')?>" style="width: 90%;" /></td>
			</tr>
            <tr>
                <th><?php echo __('tinyissue.image'); ?></th>
                <td>
                    <input type="file"  value="<?php echo Input::old('image')?>"  name="image"  />
                </td>
            </tr>
		</table>

		<ul class="assign-users" style="display: none">
			<li class="project-user<?php echo Auth::user()->id; ?>">
				<a href="javascript:void(0);" onclick="$('.project-user<?php echo Auth::user()->id; ?>').remove();" class="delete"><?php echo __('tinyissue.remove');?></a>
				<?php echo Auth::user()->firstname . ' ' . Auth::user()->lastname; ?>
				<input type="hidden" name="user[]" value="<?php echo Auth::user()->id; ?>" />
			</li>
		</ul>
	</form>

	<table class="form" style="width: 80%;">
		<tr>
			<th style="width: 10%;"><?php echo __('tinyissue.assign_users');?></th>
			<td>
				<input type="text" id="add-user-project" style="margin: 0;" placeholder="Assign a user" />

				<ul class="assign-users" style="width: 218px;">
					<li class="project-user<?php echo Auth::user()->id; ?>">
						<a href="javascript:void(0);" onclick="$('.project-user<?php echo Auth::user()->id; ?>').remove();" class="delete">Remove</a>
						<?php echo Auth::user()->firstname . ' ' . Auth::user()->lastname; ?>
						<input type="hidden" name="user[]" value="<?php echo Auth::user()->id; ?>" />
					</li>
				</ul>
			</td>
		</tr>
		<tr>
			<th></th>
			<td><input type="submit" onclick="$('#submit-project').submit();" value="<?php echo __('tinyissue.create_project');?>"  /></td>
		</tr>
	</table>

</div>