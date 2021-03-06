<?php
//echo json_encode($categories->result());die();
$validation_errors = validation_errors();
if (!empty($validation_errors)) {
    echo $validation_errors;
}
?>
 <?php if ($query->num_rows() > 0) {
     foreach ($query->result() as $row) {
              $donation = $query->row();?>
<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h3 class="form-group row ml-5">Edit Donation</h3>
	</div>
	<?php echo form_open($this->uri->uri_string()); ?>
	<div class="card-body">
   
		<div class="form-group row mt-5">
			
			<label for="donation_amount" class="col-sm-2 col-form-label">Donation Amount</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="donation_amount" id="donation_amount" value="<?php echo $donation->donation_amount; ?>">
			</div>
		</div>

		<div class="form-group row">
			<label for="donation_amount" class="col-sm-2 col-form-label">Donor</label>
			<div class="col-sm-10">
				<select class="custom-select my-1 mr-sm-2" name="post_id">
					<option selected>Choose Donor...</option>

					<?php
    foreach ($categories->result() as $row1) {
		$category_name = preg_replace('/\s/', '', $row1->category_name);
		if (strtolower($category_name) == strtolower("Donor")) {?>
					<option value="<?php echo $row1->post_id; ?>">
						<?php echo $row1->post_title; ?>
					</option>
					<?php }
    }

    ?>

				</select>
			</div>
		</div>

		<div class="form-group row">
			<label for="donation_amount" class="col-sm-2 col-form-label">School</label>
			<div class="col-sm-10">
				<select class="custom-select my-1 mr-sm-2" name="school_id">
					<option selected>Choose School...</option>
					<?php
if ($schools->num_rows() > 0) {
        foreach ($schools->result() as $row2) {?>
					<option value="<?php echo $row->school_id; ?>" <?php echo $row2->school_id == $donation->school_id ? "selected" :
						""; ?>>
						<?php echo $row2->school_name; ?>
					</option>
					<?php
}
    }
    ?>
				</select>
			</div>
		</div>
		<div class="col-auto">
			<button type="submit" class="btn btn-primary mb-2">Update</button>
		</div>
		<?php form_close();?>

	</div>
</div>
<?php
}}?>
