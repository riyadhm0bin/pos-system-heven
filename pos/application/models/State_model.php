<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class State_model extends CI_Model {

	var $table = 'db_states';
	var $column_order = array('state','country','status','store_id'); //set column field database for datatable orderable
	var $column_search = array('state','country','status','store_id'); //set column field database for datatable searchable 
	var $order = array('id' => 'desc'); // default order 

	private function _get_datatables_query()
	{
		
		$this->db->from($this->table);
		//if not admin
		//if(!is_admin()){
		//	$this->db->where("store_id",get_current_store_id());
		//}

		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->where("store_id",get_current_store_id());
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function verify_and_save(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));

		//Validate This state already exist or not
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();	
		$query=$this->db->query("select * from db_states where upper(state)=upper('$state') && upper(country)=upper('$country') and store_id=$store_id");
		if($query->num_rows()>0){
			return "State Name already Exist in $country.";
		}
		else{
			$info = array(
		    				'state' 		=> $state,
		    				'country' 			=> $country,
		    				'status' 				=> 1,
		    			);
			
			$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();	

			$q1 = $this->db->insert('db_states', $info);
			if ($q1){
					$this->session->set_flashdata('success', 'Success!! New State Name Added Successfully!');
			        return "success";
			}
			else{
			        return "failed";
			}
		}
	}

	//Get state_details
	public function get_details($id){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));

		//Validate This state already exist or not
		$query=$this->db->query("select * from db_states where upper(id)=upper('$id')");
		if($query->num_rows()==0){
			show_404();exit;
		}
		else{
			$query=$query->row();
			$data['q_id']=$query->id;
			$data['country']=$query->country;
			$data['state']=$query->state;
			$data['store_id']=$query->store_id;
			return $data;
		}
	}
	public function update_state(){
		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));

		//Validate This state already exist or not
		$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();	
		$query=$this->db->query("select * from db_states where upper(state)=upper('$state') and upper(country)=upper('$country') and id<>$q_id and store_id=$store_id");
		if($query->num_rows()>0){
			return "State Name already exist in $country.";			
		}
		else{		
			$info = array(
		    				'state' 		=> $state,
		    				'country' 			=> $country,
		    			);
			$info['store_id']=(store_module() && is_admin()) ? $store_id : get_current_store_id();
			$q1 = $this->db->where('id',$q_id)->update('db_states', $info);
			if ($q1){
					$this->session->set_flashdata('success', 'Success!! State Name Updated Successfully!');
			        return "success";			}
			else{
			        return "failed";
			}
		}
	}
	public function update_status($id,$status){
       $this->db->where("id",$id);
	    $this->db->set("status",$status);
	    $query1=$this->db->update('db_states');
        if ($query1){
            return "success";
        }
        return "failed";
	}

	public function delete_state($id){

		$this->db->where("id=$id");
		//if not admin
		/*if(!is_admin() && !store_module()){
			$this->db->where("store_id",get_current_store_id());
		}*/

		$query1=$this->db->delete("db_states");
        if ($query1){
            echo "success";
        }
        else{
            echo "failed";
        }
	}


}
