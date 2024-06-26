<?php namespace App\Http\Controllers;

    use App\Exports\ExcelTemplateExport;
    use App\Imports\UserCategoryTagImport;
use App\Models\User;
use App\Models\UserCategoryTag;
    use Session;
	use DB;
	use CRUDBooster;
    use Illuminate\Http\Request;
    // use Illuminate\Support\Facades\Input;
    use Maatwebsite\Excel\HeadingRowImport;
    use Maatwebsite\Excel\Imports\HeadingRowFormatter;
	use Maatwebsite\Excel\Facades\Excel;

	class AdminUserCategoryTagsController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "user_name";
			$this->limit = "20";
			$this->orderby = "category_tag_number,asc";
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
			$this->table = "user_category_tags";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"User Name","name"=>"user_name"];
			$this->col[] = ["label"=>"Category Tag Number","name"=>"category_tag_number"];
			$this->col[] = ["label"=>"Warehouse Category","name"=>"warehouse_categories_id","join"=>"warehouse_categories,warehouse_category_description"];
			$this->col[] = ["label"=>"Is Used","name"=>"is_used","callback_php" => '($row->is_used == 1? "YES" : "NO")'];
            $this->col[] = ["label"=>"Status","name"=>"status"];
			$this->col[] = ["label"=>"Created By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Created Date","name"=>"created_at"];
			$this->col[] = ["label"=>"Updated By","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Updated Date","name"=>"updated_at"];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'User Name','name'=>'user_name','type'=>'select','validation'=>'required','width'=>'col-sm-5',
                'dataquery' => "select user_name AS value, user_name AS label from cms_users where status='ACTIVE' and id_cms_privileges!='1' order by label asc"];
			$this->form[] = ['label'=>'Category Tag Number','name'=>'category_tag_number','type'=>'text','validation'=>'required|min:1|max:50','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Warehouse Category','name'=>'warehouse_categories_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-5',
                'datatable'=>'warehouse_categories,warehouse_category_description'];
			$this->form[] = ['label'=>'Is Used','name'=>'is_used','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-5','dataenum'=>'0|No;1|Yes'];
			if(in_array(CRUDBooster::getCurrentMethod(),['getEdit','postEditSave','getDetail'])) {
				$this->form[] = ['label'=>'Status','name'=>'status','type'=>'select','validation'=>'required','width'=>'col-sm-5','dataenum'=>'ACTIVE;INACTIVE'];
			}# END FORM DO NOT REMOVE THIS LINE

			/*
	        | ----------------------------------------------------------------------
	        | Sub Module
	        | ----------------------------------------------------------------------
			| @label          = Label of action
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        |
	        */
	        $this->sub_module = array();


	        /*
	        | ----------------------------------------------------------------------
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------
	        | @label       = Label of action
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        |
	        */
	        $this->addaction = array();


	        /*
	        | ----------------------------------------------------------------------
	        | Add More Button Selected
	        | ----------------------------------------------------------------------
	        | @label       = Label of action
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button
	        | Then about the action, you should code at actionButtonSelected method
	        |
	        */
	        $this->button_selected = array();
            if(CRUDBooster::isSuperadmin()){
                $this->button_selected[] = ["label"=>"Reset Used Count Tags","icon"=>"fa fa-check-circle","name"=>"reset_used_count_tags"];
                $this->button_selected[] = ["label"=>"Set Status ACTIVE","icon"=>"fa fa-check-circle","name"=>"set_status_ACTIVE"];
                $this->button_selected[] = ["label"=>"Set Status INACTIVE","icon"=>"fa fa-times-circle","name"=>"set_status_INACTIVE"];
            }


	        /*
	        | ----------------------------------------------------------------------
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------
	        | @message = Text of message
	        | @type    = warning,success,danger,info
	        |
	        */
	        $this->alert = array();



	        /*
	        | ----------------------------------------------------------------------
	        | Add more button to header button
	        | ----------------------------------------------------------------------
	        | @label = Name of button
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        |
	        */
	        $this->index_button = array();
            if(CRUDBooster::getCurrentMethod() == 'getIndex'){
                $this->index_button[] = ['label'=>'Import Count Tags','url'=> route('count-tags.get-import'),'icon'=>'fa fa-upload','color'=>'info'];
            }


	        /*
	        | ----------------------------------------------------------------------
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.
	        |
	        */
	        $this->table_row_color = array();


	        /*
	        | ----------------------------------------------------------------------
	        | You may use this bellow array to add statistic at dashboard
	        | ----------------------------------------------------------------------
	        | @label, @count, @icon, @color
	        |
	        */
	        $this->index_statistic = array();



	        /*
	        | ----------------------------------------------------------------------
	        | Add javascript at body
	        | ----------------------------------------------------------------------
	        | javascript code in the variable
	        | $this->script_js = "function() { ... }";
	        |
	        */
	        $this->script_js = NULL;


            /*
	        | ----------------------------------------------------------------------
	        | Include HTML Code before index table
	        | ----------------------------------------------------------------------
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;



	        /*
	        | ----------------------------------------------------------------------
	        | Include HTML Code after index table
	        | ----------------------------------------------------------------------
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;



	        /*
	        | ----------------------------------------------------------------------
	        | Include Javascript File
	        | ----------------------------------------------------------------------
	        | URL of your javascript each array
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();



	        /*
	        | ----------------------------------------------------------------------
	        | Add css style at body
	        | ----------------------------------------------------------------------
	        | css code in the variable
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;



	        /*
	        | ----------------------------------------------------------------------
	        | Include css File
	        | ----------------------------------------------------------------------
	        | URL of your css each array
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();


	    }


	    /*
	    | ----------------------------------------------------------------------
	    | Hook for button selected
	    | ----------------------------------------------------------------------
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
            switch ($button_name) {
                case 'reset_used_count_tags':
                    UserCategoryTag::whereIn('id',$id_selected)->update([
                        'is_used'=> 0,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    break;
                case 'set_status_ACTIVE':
                    UserCategoryTag::whereIn('id',$id_selected)->update([
                        'status'=>'ACTIVE',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    break;
                case 'set_status_INACTIVE':
                    UserCategoryTag::whereIn('id',$id_selected)->update([
                        'status'=>'INACTIVE',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    break;
                default:
                    # code...
                    break;
            }
	    }


	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate query of index result
	    | ----------------------------------------------------------------------
	    | @query = current sql query
	    |
	    */
	    public function hook_query_index(&$query) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate row of index table html
	    | ----------------------------------------------------------------------
	    |
	    */
	    public function hook_row_index($column_index,&$column_value) {
	    	//Your code here
	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate data input before add data is execute
	    | ----------------------------------------------------------------------
	    | @arr
	    |
	    */
	    public function hook_before_add(&$postdata) {
	        //Your code here
            $postdata['created_by']=CRUDBooster::myId();
	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command after add public static function called
	    | ----------------------------------------------------------------------
	    | @id = last insert id
	    |
	    */
	    public function hook_after_add($id) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for manipulate data input before update data is execute
	    | ----------------------------------------------------------------------
	    | @postdata = input post data
	    | @id       = current id
	    |
	    */
	    public function hook_before_edit(&$postdata,$id) {
	        //Your code here
            $postdata['updated_by']=CRUDBooster::myId();
	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------
	    | @id       = current id
	    |
	    */
	    public function hook_after_edit($id) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------
	    | @id       = current id
	    |
	    */
	    public function hook_before_delete($id) {
	        //Your code here

	    }

	    /*
	    | ----------------------------------------------------------------------
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------
	    | @id       = current id
	    |
	    */
	    public function hook_after_delete($id) {
	        //Your code here

	    }

        public function getImport()
        {
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Import User Count Tags';
            $data['uploadRoute'] = route('count-tags.import');
            $data['uploadTemplate'] = route('count-tags.get-template');
            return view('count-tags.upload',$data);
        }

        public function getTemplate()
        {
            $header = array();
            $header[0] = ['USER NAME', 'CATEGORY TAG', 'WAREHOUSE CATEGORY', 'IS USED', 'STATUS'];
            $header[1] = ['TEST USER','ACC-1-1-1','ACCESSORIES','NO','ACTIVE'];
            $header[2] = ['TEST USER 2','ACC-1-1-2','ACCESSORIES','YES','INACTIVE'];
            $export = new ExcelTemplateExport([$header]);
            return Excel::download($export, 'count-tags-'.date("Ymd").'-'.date("h.i.sa").'.csv');
        }

        public function importCountTags(Request $request)
        {
            $errors = array();
			$path_excel = $request->file('import_file')->store('temp');
			$path = storage_path('app').'/'.$path_excel;
            HeadingRowFormatter::default('none');
            $headings = (new HeadingRowImport)->toArray($path);
			$excelData = Excel::toArray(new UserCategoryTagImport, $path);

            $header = array('USER NAME', 'CATEGORY TAG', 'WAREHOUSE CATEGORY', 'IS USED', 'STATUS');

            for ($i=0; $i < sizeof($headings[0][0]); $i++) {
				if (!in_array($headings[0][0][$i], $header)) {
					$unMatch[] = $headings[0][0][$i];
				}
			}

			if(!empty($unMatch)) {
                return redirect()->back()->with(['message_type' => 'danger', 'message' => 'Failed ! Please check template headers, mismatched detected.']);
			}

            $users = array_unique(array_column($excelData[0], "user_name"));
            $tags = array_unique(array_column($excelData[0], "category_tag"));
            $uploaded_tags = array_column($excelData[0], "category_tag");

            if(count((array)$uploaded_tags) != count((array)$tags)){
                array_push($errors, 'duplicate item found!');
            }

            foreach ($users as $key => $value) {
                $userExist = DB::table('cms_users')->where('user_name',$value)->first();

                if(!is_null($userExist)){
                    array_push($errors, 'no user found!');
                }
            }

            if(!empty($errors)){
				return redirect(CRUDBooster::mainpath())->with(['message_type' => 'danger', 'message' => 'Failed ! Please check '.implode(", ",$errors)]);
			}

            HeadingRowFormatter::default('slug');
			Excel::import(new UserCategoryTagImport, $path);
			return redirect(CRUDBooster::mainpath())->with(['message_type' => 'success', 'message' => 'Upload complete!']);
        }

        public function getCategoryTagByCategory(Request $request)
        {
            $userName = User::where('id',CRUDBooster::myId())->first();
            $category_tags = UserCategoryTag::where('warehouse_categories_id',$request->category)
                ->where('is_used',0)
                ->where('status','ACTIVE');

            if(CRUDBooster::myPrivilegeName() == "Scanner"){
                $category_tags->where('user_name',$userName->user_name);
            }

            $category_tags->select('category_tag_number')->orderBy('category_tag_number','ASC');
            return $category_tags->get();
        }

        public function setUsedCategoryTag(Request $request)
        {
            return UserCategoryTag::where('warehouse_categories_id',$request->category)
                ->where('category_tag_number',$request->category_tag)
                ->update([
                    'is_used' => 1,
                    'updated_by' => CRUDBooster::myId()
                ]);
        }

	}
