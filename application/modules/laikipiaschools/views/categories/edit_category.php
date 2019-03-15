
    <!-- <div class="container"> -->
    <div class = "container">
    <?php json_encode($categories->result());die();
    

$validation_errors = validation_errors();
if (!empty($validation_errors)) {
    echo $validation_errors;
}
?>
    <!-- dynamically generating a form in brackets where to submit data to-->
    <div class="card-header py-3">
        <h3 class="form-group row ml-5">Edit Categories</h3>
	</div>
    <div class="container">
    <?php echo form_open($this->uri->uri_string()); ?>
    <div class="form-group">
    <label for="category_parent">Parent</label>
    
    <select id="inputState" class="form-control" name="category_parent">
     <?php 
       $cat_arr = array();
            foreach ($categories->result() as $category) {
                if(!in_array($category->category_parent, $cat_arr)){
                foreach($categories->result() as $cat){ 
                    if($category->category_parent == $cat->category_id){
                    array_push($cat_arr, $category->category_parent); ?>
                    <option value="<?php echo $cat->category_id; ?>" <?php echo $category->category_parent == $category_parent ? "selected" : ""; ?>><?php echo $cat->category_name; ?></option>
                <?php 
               // echo json_encode($cat_arr->result());die();
                }}}}
        ?> 

    </select>
    </div>
    <div class="form-group">
    <label for="category_name">Name</label>
    <input type="category_name" class="form-control" name="category_name" id="category_name" naria-describedby="emailHelp" placeholder="Enter Name" value=" <?php echo $category_name; ?>">
    </div>
    
    <button type="submit" class="btn btn-primary">Update</button>

    <?php echo form_close() ?>
    </div>



