<?php
class Schools_model extends CI_Model
{
    public function add_school($file_name, $thumb_name)
    {
        // create an array of The data to save
        $data = array(
            "school_name" => $this->input->post("school_name"),
            "school_write_up" => $this->input->post("school_write_up"),
            "school_boys_number" => $this->input->post("school_boys_number"),
            "school_girls_number" => $this->input->post("school_girls_number"),
            "school_latitude" => $this->input->post("school_latitude"),
            "school_longitude" => $this->input->post("school_longitude"),
            "school_location_name" => $this->input->post("school_location_name"),
            "school_zone" => $this->input->post("school_zone"),
            "school_image_name" => $file_name,
            "school_thumb_name" => $thumb_name,
            'school_status' => 1,

        );

        if ($this->db->insert("school", $data)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    
    public function insert_csv($data)
    {
        // create an array of The data to save
        $data = array(
            "school_name" => $this->input->post("school_name"),
            "school_write_up" => $this->input->post("school_write_up"),
            "school_boys_number" => $this->input->post("school_boys_number"),
            "school_girls_number" => $this->input->post("school_girls_number"),
            "school_latitude" => $this->input->post("school_latitude"),
            "school_longitude" => $this->input->post("school_longitude"),
            "school_location_name" => $this->input->post("school_location_name"),
            "school_zone" => $this->input->post("school_zone"),
            "school_image_name" => $file_name,
            "school_thumb_name" => $thumb_name,
            "school_status" => $this->input->post("school_status"),
        );

        if ($this->db->insert("school", $data)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }
    public function get_all_zones()
    {
        $this->db->distinct('school_zone');
        $this->db->select("school_zone");
        $this->db->from("school");
        $query = $this->db->get();
        // $sql = '';
        // foreach ($query->result() as $key => $value)
        // {
        //     $sql .= 'UPDATE school SET school_zone ="' . $value->school_location_name . '" WHERE school_id = ' . $value->school_id . ';';
        // }

        // echo $sql;die();

        // echo json_encode($query->result());die();
        return $query;

    }

    public function get_all_schools($table, $where, $start, $limit, $page, $order, $order_method)
    {
        // var_dump($where);die();
        // $where = "school.deleted = 0";
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($where);
        $this->db->limit($limit, $page);
        $this->db->order_by($order, $order_method);
        return $this->db->get();

    }
    public function get_schools()
    {
        // var_dump($where);die();
        // $where = "school.deleted = 0";
        $this->db->select("*");
        $this->db->from('school');
        $this->db->where('school.deleted != 1');
        $this->db->order_by('school_name', 'DESC');
        return $this->db->get();

    }

    public function get_other_images()
    {
        $this->db->select("*");
        $this->db->from("school_images");
        return $this->db->get();
    }
    public function get_single_school($school_id)
    {
        $this->db->where("school_id", $school_id);
        return $this->db->get("school");
    }
    public function countAll()
    {
        return $this->db->get("school")->num_rows();
    }
    public function delete_school($school_id)
    {
        $data = array(
            'deleted' => 1,
            'deleted_by' => 1,
            'deleted_on' => date("Y-m-d H:i:s"),
        );
        $this->db->set($data);
        $this->db->where('school_id', $school_id);
        if ($this->db->update('school')) {
            return true;
        } else {
            return false;
        }
    }

    public function update_school($school_id, $file_name = false, $thumb_name = false)
    {
        $data = array(
            "school_name" => $this->input->post("school_name"),
            "school_write_up" => $this->input->post("school_write_up"),
            "school_zone" => $this->input->post("school_zone"),
            "school_boys_number" => $this->input->post("school_boys_number"),
            "school_girls_number" => $this->input->post("school_girls_number"),
            "school_location_name" => $this->input->post("school_location_name"),
            "school_latitude" => $this->input->post("school_latitude"),
            "school_longitude" => $this->input->post("school_longitude"),
            "school_status" => $this->input->post("school_status"),
        );

        if ($file_name != false) {
            $data["school_thumb_name"] = $thumb_name;
            $data["school_image_name"] = $file_name;
        }

        $this->db->set($data);
        $this->db->where('school_id', $school_id);
        if ($this->db->update('school')) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_school_image($school_image_id)
    {
        $data = array(
            'school_image_id' => $school_image_id,
        );

        $this->db->delete_where($data, 'school_image_id', $school_image_id);
        // $this->db->where();
        if ($this->db->update('school_images')) {
            return true;
        } else {
            return false;
        }
    }
    public function get_images()
    {
        $this->db->select('school_images.*, school.school_id,school.school_name');
        $this->db->from('school_images');
        $this->db->join('school', 'school_images.school_id=school.school_id', 'left');
        return $this->db->get();

    }
    public function change_school_status($school_id, $new_school_status)
    {
        $this->db->set('school_status', $new_school_status);
        $this->db->where('school_id', $school_id);
        if ($this->db->update('school')) {
            return true;
        } else {
            return false;
        }
    }
    public function save_school_flow($table, $data)
    {
        // var_dump($data);die();
        if ($this->db->insert($table, $data)) {
            return $this->db->insert_id();
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