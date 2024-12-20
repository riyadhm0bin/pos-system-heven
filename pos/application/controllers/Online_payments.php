<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Online_payments extends MY_Controller

{

    function  __construct() {

        parent::__construct();
        $this->load_global();
        $this->load->library('paypal_lib');

        $this->load->model('online_payments_model','online_payments');

    }

    

    

    function index(){
        redirect(base_url('subscription'),'refresh');

    }

    public function free_package($pid=''){

        redirect(base_url('online_payments/paymentSuccess/Free/'.$pid),'refresh');
    }

    function buy_package($gateway='paypal',$pid){
        //IF EMPTY ID
        if(empty($pid)){
            echo "Something went wrong";exit;
        }

        //Find package exist or not
        $q1 = $this->db->select("*")->where("id",$pid)->get("db_package");
        if($q1->num_rows()==0){
            echo "There is no package available";exit;
        }

        //Validate is PayPal & Instamojo configured or not
        if($gateway=='paypal'){
            if(!$this->online_payments->validatePaypalRecords()){
                echo "PayPal Payment ID not exist, contact Admin!";exit;
            }    
        }
        else{
            if(!$this->online_payments->validateInstamojoRecords()){
                echo "Instamojo Payment Gateway not configured, contact Admin!";exit;
            }    
        }


        $this->load->model('package_model');
        $package_rec = $this->package_model->get_package_list($pid);
        
        $package_name = $package_rec['package_list'][1]['package_name'];
        $monthly_price = $package_rec['package_list'][1]['monthly_price'];
        $annual_price = $package_rec['package_list'][1]['annual_price'];

        $package_price= ($monthly_price>0) ? $monthly_price : $annual_price;
        
        $store_id = get_current_store_id(); //current store id

        if($gateway=='paypal'){
            //Set variables for paypal form

            $returnURL = base_url().'online_payments/paymentSuccess/Paid'; //payment success url

            $failURL = base_url().'online_payments/paymentFail'; //payment fail url

            $notifyURL = base_url().'online_payments/ipn'; //ipn url

            //get particular product data

            

            

            $logo = base_url().'Your_logo_url';

             

            $this->paypal_lib->add_field('return', $returnURL);

            $this->paypal_lib->add_field('fail_return', $failURL);

            $this->paypal_lib->add_field('notify_url', $notifyURL);

            $this->paypal_lib->add_field('item_name', $package_name);

            $this->paypal_lib->add_field('custom', $store_id);

            $this->paypal_lib->add_field('item_number',  $pid);

            $this->paypal_lib->add_field('amount',  $package_price);        

            $this->paypal_lib->image(base_url()."/uploads/admin.png");

             

            $this->paypal_lib->paypal_auto_form();
        }
        else{
            //Instamojo
            $this->load->library('instamojo');
            $pay = $this->instamojo->pay_request(
                $amount = $package_price,
                $purpose = $package_name,
                $buyer_name = $this->session->userdata('inv_username'),
                $email = $this->session->userdata('email'),
                $phone = "",
                $send_email = 'TRUE',
                $send_sms = 'FALSE',
                $repeated = 'FALSE',
                $redirect_url = base_url("online_payments/paymentSuccess/Paid/".$pid."/instamojo")
            );

            //$redirect_url = $pay['longurl'];

            redirect($pay['longurl'], 'refresh');
        }
    }

    public function save(){
        $this->form_validation->set_rules('package_id', 'Package Name', 'trim|required');
        $this->form_validation->set_rules('category', 'Package Category', 'trim|required');
        $this->form_validation->set_rules('package_count', 'Package Count', 'trim|required');
        $this->form_validation->set_rules('total', 'Total', 'trim|required');
        //$this->form_validation->set_rules('status', 'Status', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            $package_id = $this->input->post('package_id');
            $result = $this->paymentSuccess($package_type="Paid",$pid=$package_id,$gateway="Manual");
            echo $result;
        } else {
            echo validation_errors();
        }
    }

    public function bank_transfer($pid=''){
        $data=$this->data;
        $data['page_title']=$this->lang->line('bankTransfer');
        $this->load->view('bank_details',$data);
    }
     public function paymentSuccess($package_type='Paid',$pid='',$gateway=''){

        //get the transaction data
        extract($this->data);


        //Manual Payment mean Super admin will allocate manually
        if($package_type=='Paid' && $gateway=='Manual'){
            $store_id = $this->input->post('store_id');
            $total = $this->input->post('total');
            $package_id = $this->input->post('package_id');
            $description = $this->input->post('description');
            $package_count = $this->input->post('package_count');
            $category = $this->input->post('category');//Monthly or Annually
            $payment_type = $this->input->post('payment_type');

            $store_details = get_store_details($store_id);
            $user = get_user_details($store_details->user_id);

            $data['payment_by'] = 'Manual';
            $data['payer_email'] = $user->email;

            $data['store_id'] = $store_id;
            
            $data['txn_id'] = '';//for PayPal
            $data['payment_id'] = ''; //for Instamojo

            $data['payment_gross'] = $total;
            $data['currency_code'] = '';
            $data['payment_status'] = "success";

            $data['package_id'] = $package_id; 
            $data['package_count'] = $package_count; 
            $data['payment_type'] = $payment_type; 
        }

        else if($package_type=='Paid' && $gateway=='instamojo'){
            $store_id = get_current_store_id();
            $this->load->library('instamojo');
            $paymentId =  $this->input->get('payment_request_id');
            $instamojo     = $this->instamojo->status($paymentId);
            //echo "<pre>";print_r($instamojo);exit;
            $data['payment_by'] = 'Instamojo';
            $data['payer_email'] = $instamojo['email'];
            $data['store_id'] = $store_id;
            
            $data['txn_id'] = '';//of paypal
            $data['payment_id'] = $instamojo["id"];//of instamojo

            $data['payment_gross'] = $instamojo['payments'][0]["amount"];
            $data['currency_code'] = $instamojo['payments'][0]["currency"];
            $data['payment_status'] = $instamojo["status"];

            $data['package_id'] = $pid; 
        }
        else if($package_type=='Paid' && $gateway!='Manual'){

            $store_id = $paypalInfo['custom'];

            $paypalInfo = $this->input->post();
            //print_r($paypalInfo);exit;
            
            $data['payment_by'] = 'PayPal';
            $data['payer_email'] = $paypalInfo["payer_email"];

            $data['store_id'] = $paypalInfo['custom']; 

            $data['package_id'] = $paypalInfo['item_number']; 

            

            $data['txn_id'] = $paypalInfo["txn_id"];//tx

            $data['payment_gross'] = $paypalInfo["mc_gross"];

            $data['currency_code'] = $paypalInfo["mc_currency"];

            $data['payment_status'] = $paypalInfo["payment_status"];
        }
        else{
            $store_id = get_current_store_id();
            //Find is it first time or second time ?
            $prev_package_type='';
            $q1 = $this->db->select("package_type")->where("package_id",$pid)->where("store_id",$store_id)->get("db_subscription");
            if($q1->num_rows()>0){
                $prev_package_type = $q1->row()->package_type;
            }
            if($prev_package_type=='Free'){
                $this->session->set_flashdata('warning', 'Sorry!! This Package already used to your account!');
                redirect(base_url('subscription'),'refresh');
                exit;
            }
            $data['payment_by'] = 'Manual';
            $data['payer_email'] = '';//
            $data['store_id'] =$store_id;
            $data['package_id'] = $pid; 
            
            $data['txn_id'] = '';
            $data['payment_gross'] = 0;
            $data['currency_code'] = '';
            $data['payment_status'] = '';
        }

        $data['subscription_date']    = $CUR_DATE;
        $data['created_date']    = $CUR_DATE;
        $data['created_time']    = $CUR_TIME;
        $data['created_by']    = $CUR_USERNAME;
        $data['system_ip']    = $SYSTEM_IP;
        $data['system_name']    = $SYSTEM_NAME;


        //GET PACKAGE DETAILS
        $this->load->model('package_model');
        $package_rec = $this->package_model->get_package_list($data['package_id']);
        $data['package_name'] = $package_rec['package_list'][1]['package_name'];
        $data['trial_days'] = $package_rec['package_list'][1]['trial_days'];

        $data['max_users'] = ($package_rec['package_list'][1]['max_users']=='∞') ? '-1' : $package_rec['package_list'][1]['max_users'];

        $data['max_warehouses'] = ($package_rec['package_list'][1]['max_warehouses']=='∞') ? '-1' : $package_rec['package_list'][1]['max_warehouses'];

        $data['max_items'] = ($package_rec['package_list'][1]['max_items']=='∞') ? '-1' : $package_rec['package_list'][1]['max_items'];

        $data['max_invoices'] = ($package_rec['package_list'][1]['max_invoices']=='∞') ? '-1' : $package_rec['package_list'][1]['max_invoices'];

        //$data['max_warehouses'] = $package_rec['package_list'][1]['max_warehouses'];
        //$data['max_items'] = $package_rec['package_list'][1]['max_items'];
        //$data['max_invoices'] = $package_rec['package_list'][1]['max_invoices'];
        $data['package_type'] = $package_rec['package_list'][1]['package_type'];
        $monthly_price = $package_rec['package_list'][1]['monthly_price'];
        $annual_price = $package_rec['package_list'][1]['annual_price'];



        $data['description'] = ($gateway=='Manual') ? $description : $package_rec['package_list'][1]['description'];
        
        if($gateway=='Manual'){//Manual adjustments
            if($category=="monthly_price"){                
                $expire_date = date("Y-m-d",strtotime("+".$package_count." month"));
            }
            else{//Annually
                $expire_date = date("Y-m-d",strtotime("+".$package_count." year"));
            }
        }
        else if($data['package_type']=='Free'){
            $expire_date = date("Y-m-d",strtotime("+".$data['trial_days']." days"));
        }
        else{
            if($monthly_price>0){
                $expire_date = date("Y-m-d",strtotime("+1 month"));
            }
            else{
                $expire_date = date("Y-m-d",strtotime("+1 year"));
            }
        }
        
        $data['expire_date'] = $expire_date;


        $this->db->insert("db_subscription",$data);

        //update the store
        $subscription_plan_id = $this->db->insert_id();
        $this->db->where("id",$store_id)->set("current_subscriptionlist_id",$subscription_plan_id)->update("db_store");
        //calculate expire date
        //end

        //pass the transaction data to view
        $this->session->set_flashdata('success', 'Congratulations!! New Subscription Added Successfully!');

        if($gateway=='Manual'){
            return 'success';
        }
        else{
            redirect(base_url('subscription'),'refresh');
        }

     }

      

     function paymentFail(){

        //if transaction cancelled

        $this->session->set_flashdata('failed', 'Sorry!! Payment Failed, try again!');
        redirect(base_url('subscription'),'refresh');

     }

      

     function ipn(){

        //paypal return transaction details array

        $paypalInfo    = $this->input->post();

 

        $data['user_id'] = $paypalInfo['custom'];

        $data['product_id']    = $paypalInfo["item_number"];

        $data['txn_id']    = $paypalInfo["txn_id"];

        $data['payment_gross'] = $paypalInfo["mc_gross"];

        $data['currency_code'] = $paypalInfo["mc_currency"];

        $data['payer_email'] = $paypalInfo["payer_email"];

        $data['payment_status']    = $paypalInfo["payment_status"];
        

 

        $paypalURL = $this->paypal_lib->paypal_url;        

        $result    = $this->paypal_lib->curlPost($paypalURL,$paypalInfo);

         

        //check whether the payment is verified
        if(preg_match("/VERIFIED/i",$result)){
            //insert the transaction data into the database
            $this->online_payments->storeTransaction($data);

        }

    }

}