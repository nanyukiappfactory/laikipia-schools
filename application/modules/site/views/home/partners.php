<!-- Partners -->

<section class="schools-brief" id="partner"> 
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2 text-center">
				<div class="section-title">
					<h2 class="text-uppercase">Our <span class="text-theme">Partners</span></h2>
					<h4>We have partnered with this amazing organisations to make our initiative successful</h4>
				</div>
			</div>
		</div>
		<div id="partnerCarousel" class="owl-carousel">
			<?php
            foreach ($partners->result() as $row) {
                if (strtolower($row->category_name) == strtolower("Partners")) {?>
			<div class="card">
				<img style="max-width:100%;" src="<?php echo base_url() . 'assets/uploads/' . $row->post_image_name; ?>" class="d-block w-10" />
				<div class="img-overlayer"></div>
				<div class="card-body">
					<h5 class="card-title text-center">
						<?php echo $row->post_title; ?>
					</h5>
					<div class="center-button">
						<a href="#" class="btn btn-default btn-theme" target="_blank">View More</a>
					</div>
				</div>
			</div>
			<?php }}?>
		</div>
	</div>
</section>
<!-- End Schools -->
