<?php namespace App\Http\Controllers;

    use App\Models\CountType;
    use Session;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;

	class AdminCountTypesController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "count_type_code";
			$this->limit = "20";
			$this->orderby = "count_type_description,asc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = true;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "count_types";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Count Type Code","name"=>"count_type_code"];
			$this->col[] = ["label"=>"Count Type Description","name"=>"count_type_description"];
            $this->col[] = ["label"=>"Count Passcode","name"=>"count_passcode","visible"=>(CRUDBooster::isSuperAdmin())?true:false];
			$this->col[] = ["label"=>"Status","name"=>"status"];
			$this->col[] = ["label"=>"Created By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Created Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Updated By","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Updated Date","name"=>"updated_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Count Type Code','name'=>'count_type_code','type'=>'text','validation'=>'required|min:1|max:50','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Count Type Description','name'=>'count_type_description','type'=>'text','validation'=>'required|min:1|max:150','width'=>'col-sm-5'];
            if(CRUDBooster::isSuperAdmin()){
                $this->form[] = ['label'=>'Count Passcode','name'=>'count_passcode','type'=>'text','validation'=>'required|min:1|max:150','width'=>'col-sm-5'];
            }
            if(in_array(CRUDBooster::getCurrentMethod(),['getEdit','postEditSave','getDetail'])) {
				$this->form[] = ['label'=>'Status','name'=>'status','type'=>'select','validation'=>'required','width'=>'col-sm-5','dataenum'=>'ACTIVE;INACTIVE'];
			}
            # END FORM DO NOT REMOVE THIS LINE

	    }

	    public function hook_after_add($id) {
	        //Your code here
            $postdata['created_by']=CRUDBooster::myId();
	    }

	    public function hook_before_edit(&$postdata,$id) {
	        //Your code here
            $postdata['updated_by']=CRUDBooster::myId();
	    }

        public function getPassCode(Request $request)
        {
            return json_encode(CountType::where('count_type_code',$request->count_type)
                ->where('count_passcode',$request->count_passcode)
                ->select('count_passcode')->first());
        }

	}
