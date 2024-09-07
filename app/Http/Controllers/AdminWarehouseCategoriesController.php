<?php namespace App\Http\Controllers;

    use App\Models\WarehouseCategory;
    use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class AdminWarehouseCategoriesController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "warehouse_category_code";
			$this->limit = "20";
			$this->orderby = "warehouse_category_description,asc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = true;
			$this->button_edit = true;
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "warehouse_categories";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Warehouse Category Code","name"=>"warehouse_category_code"];
			$this->col[] = ["label"=>"Warehouse Category Description","name"=>"warehouse_category_description"];
            $this->col[] = ["label"=>"Warehouse Category Group","name"=>"warehouse_category_group"];
			$this->col[] = ["label"=>"Status","name"=>"status"];
			$this->col[] = ["label"=>"Is Restricted","name"=>"is_restricted","callback_php" => '($row->is_restricted == 1? "YES" : "NO")'];
            $this->col[] = ["label"=>"Created Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Updated Date","name"=>"updated_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Warehouse Category Code','name'=>'warehouse_category_code','type'=>'text','validation'=>'required|min:1|max:50','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Warehouse Category Description','name'=>'warehouse_category_description','type'=>'text','validation'=>'required|min:1|max:150','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Is Restricted','name'=>'is_restricted','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-5','dataenum'=>'0|No;1|Yes'];
            $this->form[] = ['label'=>'Warehouse Category Group','name'=>'warehouse_category_group','type'=>'select2-multiple','validation'=>'required|min:1|max:50','width'=>'col-sm-5',
                'multiple'=>'multiple','datatable'=>'warehouse_categories,warehouse_category_description'];
			if(in_array(CRUDBooster::getCurrentMethod(),['getEdit','postEditSave','getDetail'])) {
				$this->form[] = ['label'=>'Status','name'=>'status','type'=>'select','validation'=>'required','width'=>'col-sm-5','dataenum'=>'ACTIVE;INACTIVE'];
			}
            # END FORM DO NOT REMOVE THIS LINE

	        $this->button_selected = array();
            if(CRUDBooster::isSuperadmin()){
                $this->button_selected[] = ["label"=>"Set Status ACTIVE ","icon"=>"fa fa-check-circle","name"=>"set_status_ACTIVE"];
                $this->button_selected[] = ["label"=>"Set Status INACTIVE","icon"=>"fa fa-times-circle","name"=>"set_status_INACTIVE"];
            }

	    }

	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
            $status='ACTIVE';
            switch ($button_name) {
                case 'set_status_ACTIVE':
                    $status='ACTIVE';
                    break;
                case 'set_status_INACTIVE':
                    $status='INACTIVE';
                    break;
                default:
                    # code...
                    break;
            }
            WarehouseCategory::whereIn('id',$id_selected)->update([
                'status'=>$status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
	    }

	    public function hook_before_add(&$postdata) {
	        //Your code here
            $postdata['warehouse_category_group'] = implode(",",$postdata['warehouse_category_group']);
	    }

	    public function hook_before_edit(&$postdata,$id) {
	        //Your code here
            $postdata['warehouse_category_group'] = implode(",",$postdata['warehouse_category_group']);
	    }

	}
