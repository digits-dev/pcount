<?php namespace App\Http\Controllers;

    use App\Exports\ExcelTemplateExport;
    use App\Imports\UserCategoryTagImport;
    use App\Models\User;
    use App\Models\UserCategoryTag;
    use Session;
	use DB;
	use CRUDBooster;
    use Illuminate\Http\Request;
    use Maatwebsite\Excel\HeadingRowImport;
    use Maatwebsite\Excel\Imports\HeadingRowFormatter;
	use Maatwebsite\Excel\Facades\Excel;

	class AdminUserCategoryTagsController extends \crocodicstudio\crudbooster\controllers\CBController {

        private const SCANNER = 5;

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
                'dataquery' => "select user_name AS value, user_name AS label from cms_users where status='ACTIVE' and id_cms_privileges=".self::SCANNER." order by label asc"];
			$this->form[] = ['label'=>'Category Tag Number','name'=>'category_tag_number','type'=>'text','validation'=>'required|min:1|max:50','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Warehouse Category','name'=>'warehouse_categories_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-5',
                'datatable'=>'warehouse_categories,warehouse_category_description'];
			$this->form[] = ['label'=>'Is Used','name'=>'is_used','type'=>'radio','validation'=>'required|integer','width'=>'col-sm-5','dataenum'=>'0|No;1|Yes'];
			if(in_array(CRUDBooster::getCurrentMethod(),['getEdit','postEditSave','getDetail'])) {
				$this->form[] = ['label'=>'Status','name'=>'status','type'=>'select','validation'=>'required','width'=>'col-sm-5','dataenum'=>'ACTIVE;INACTIVE'];
			}
            # END FORM DO NOT REMOVE THIS LINE

	        $this->button_selected = array();
            if(CRUDBooster::isSuperadmin()){
                $this->button_selected[] = ["label"=>"Reset Used Count Tags","icon"=>"fa fa-check-circle","name"=>"reset_used_count_tags"];
                $this->button_selected[] = ["label"=>"Set Status ACTIVE","icon"=>"fa fa-check-circle","name"=>"set_status_ACTIVE"];
                $this->button_selected[] = ["label"=>"Set Status INACTIVE","icon"=>"fa fa-times-circle","name"=>"set_status_INACTIVE"];
            }

	        $this->index_button = array();
            if(CRUDBooster::getCurrentMethod() == 'getIndex'){
                $this->index_button[] = ['label'=>'Import Count Tags','url'=> route('count-tags.get-import'),'icon'=>'fa fa-upload','color'=>'info'];
            }

	    }

	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
            $details = ['updated_at' => date('Y-m-d H:i:s')];
            switch ($button_name) {
                case 'reset_used_count_tags':
                    $details['is_used']= 0;
                    break;
                case 'set_status_ACTIVE':
                    $details['status']='ACTIVE';
                    break;
                case 'set_status_INACTIVE':
                    $details['status']='INACTIVE';
                    break;
                default:
                    # code...
                    break;
            }
            UserCategoryTag::whereIn('id',$id_selected)->update($details);
	    }

	    public function hook_before_add(&$postdata) {
	        //Your code here
            $postdata['created_by']=CRUDBooster::myId();
	    }
	    public function hook_before_edit(&$postdata,$id) {
	        //Your code here
            $postdata['updated_by']=CRUDBooster::myId();
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
