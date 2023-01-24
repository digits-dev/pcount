<?php namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use CRUDbooster;
use crocodicstudio\crudbooster\controllers\CBController;

class AdminCmsUsersController extends CBController {


	public function cbInit() {
		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->table               = 'cms_users';
		$this->primary_key         = 'id';
		$this->title_field         = "name";
		$this->button_action_style = 'button_icon';
		$this->button_import 	   = FALSE;
		$this->button_export 	   = FALSE;
		# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = array();
		$this->col[] = array("label"=>"Name","name"=>"name");
        $this->col[] = array("label"=>"User Name","name"=>"user_name");
		$this->col[] = array("label"=>"Email","name"=>"email");
		$this->col[] = array("label"=>"Privilege","name"=>"id_cms_privileges","join"=>"cms_privileges,name");
		$this->col[] = array("label"=>"Photo","name"=>"photo","image"=>1);
        $this->col[] = array("label"=>"Status","name"=>"status");
		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = array();
		$this->form[] = array("label"=>"Name","name"=>"name", 'width'=>'col-sm-5', 'validation'=>'required|alpha_spaces|min:3');
        $this->form[] = array("label"=>"User Name","name"=>"user_name", 'width'=>'col-sm-5', 'validation'=>'required|min:3');
		$this->form[] = array("label"=>"Email","name"=>"email", 'width'=>'col-sm-5', 'type'=>'email','validation'=>'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId());
		$this->form[] = array("label"=>"Photo","name"=>"photo", 'width'=>'col-sm-5', "type"=>"upload","help"=>"Recommended resolution is 200x200px",'validation'=>'required|image|max:1000','resize_width'=>90,'resize_height'=>90);
		$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges", 'width'=>'col-sm-5', "type"=>"select","datatable"=>"cms_privileges,name");
		$this->form[] = array("label"=>"Password","name"=>"password", 'width'=>'col-sm-5', "type"=>"password","help"=>"Please leave empty if not changed");
		# END FORM DO NOT REMOVE THIS LINE

	}

	public function getProfile() {

		$this->button_addmore = FALSE;
		$this->button_cancel  = FALSE;
		$this->button_show    = FALSE;
		$this->button_add     = FALSE;
		$this->button_delete  = FALSE;
		$this->hide_form 	  = ['id_cms_privileges','user_name'];

		$data['page_title'] = cbLang("label_button_profile");
		$data['row']        = CRUDBooster::first('cms_users',CRUDBooster::myId());

        return $this->view('crudbooster::default.form',$data);
	}
	public function hook_before_edit(&$postdata,$id) {

	}
	public function hook_before_add(&$postdata) {

	}
}
