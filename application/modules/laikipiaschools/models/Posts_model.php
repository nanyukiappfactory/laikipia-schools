<?php
class Posts_model extends CI_Model
{
    public function get_all_posts($table, $where, $limit, $page, $order, $order_method)
    {
        // $where = "post.deleted = 0";
        $this->db->select("post.*, category.category_name");
        $this->db->from($table);
        $this->db->where($where);
        $this->db->limit($limit, $page);
        $this->db->order_by($order, $order_method);
        return $this->db->get();

	}
	
    public function get_posts_categories($table, $order, $order_method)
    {
        $this->db->select("*");
        $this->db->from($table);
        $this->db->order_by($order, $order_method);
        return $this->db->get();
    }
    public function get_all_categories()
    {
        $this->db->select("*");
        $this->db->from("category");
        $this->db->order_by("category_name");
        $query = $this->db->get();
        return $query;

    }
    public function change_post_status($post_id, $new_post_status)
    {
        $this->db->set('post_status', $new_post_status);
        $this->db->where('post_id', $post_id);
        if ($this->db->update('post')) {
            return true;
        } else {
            return false;
        }
    }
    public function get_single_post($post_id)
    {
        $this->db->where("post_id", $post_id);
        return $this->db->get("post");
    }
    public function countAll()
    {
        return $this->db->get("post")->num_rows();
    }
    public function delete_post($post_id)
    {
        $data = array(
            'deleted' => 1,
            'deleted_by' => 1,
            'deleted_on' => date("Y-m-d H:i:s"),
        );
        $this->db->set($data);
        $this->db->where('post_id', $post_id);
        if ($this->db->update('post')) {
            return true;
        } else {
            return false;
        }
	}

    public function add_post($file_name, $thumb_name)
    {

        // create an array of The data to save
        $data = array(
            "post_title" => $this->input->post("post_title"),
            "post_description" => $this->input->post("post_description"),
            // "post_image_name" => $this->input->post("post_image_name"),
            // "post_views" => $this->input->post("post_views"),
            "post_image_name" => $file_name,
            "post_thumb_name" => $thumb_name,
            "post_status" => $this->input->post("post_status"),
            "post_date" => $this->input->post("post_date"),
            "category_id" => $this->input->post("category_id"),
        );
        if ($this->db->insert("post", $data)) {
            return $this->db->insert_id();
        } else {
            return false;
        }

    }
	
    public function update_post($post_id, $file_name = NULL, $thumb_name = NULL)
    {
        $data = array(
            "post_title" => $this->input->post("post_title"),
            "post_description" => $this->input->post("post_description"),
            "post_status" => $this->input->post("post_status"),
            "post_date" => date('Y-m-d', strtotime($this->input->post("post_date"))),
            "category_id" => $this->input->post("category_id")
		);
		
		if($file_name != NULL)
		{
            $data["post_image_name"] = $file_name;
            $data["post_thumb_name"] = $thumb_name;
		}
        $this->db->where('post_id', $post_id);
        if ( $this->db->update('post', $data)) {
			return true;
        } else {
            return false;
        }
    }

    public function save_image($image_url, $path)
    {
        $image_name = md5(date("Y-m-d H:i:s"));
        $content = file_get_contents($image_url);
        file_put_contents($path . '/' . $image_name . '.jpg', $content);

        return $image_name . '.jpg';
    }

}
