<?php namespace App\Http\Controllers;

    use App\Exports\CountExport;
    use App\Models\CountHeader;
    use App\Models\CountLine;
use App\Models\CountTempHeader;
use App\Models\CountTempLine;
use App\Models\CountType;
    use App\Models\Item;
use App\Models\User;
use App\Models\UserCategoryTag;
use App\Models\UserPrivilege;
use App\Models\WarehouseCategory;
    use Session;
    use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;
    use Maatwebsite\Excel\Facades\Excel;

	class AdminCountHeadersController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "category_tag_number";
			$this->limit = "20";
			$this->orderby = "category_tag_number,asc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = (CRUDBooster::isSuperAdmin()) ? true : false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "count_headers";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Count Type","name"=>"count_types_id","join"=>"count_types,count_type_description"];
			$this->col[] = ["label"=>"Category Tag Number","name"=>"category_tag_number"];
			$this->col[] = ["label"=>"Warehouse Category","name"=>"warehouse_categories_id","join"=>"warehouse_categories,warehouse_category_description"];
			$this->col[] = ["label"=>"Total Qty","name"=>"total_qty"];
			$this->col[] = ["label"=>"Scanned By","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Verified By","name"=>"updated_by","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"Is Printed","name"=>"print_flag","callback_php" => '($row->print_flag == 1? "YES" : "NO")'];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Count Type','name'=>'count_types_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-5',
                'datatable'=>'count_types,count_type_description'];
			$this->form[] = ['label'=>'Category Tag Number','name'=>'category_tag_number','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Warehouse Category','name'=>'warehouse_categories_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-5',
                'datatable'=>'warehouse_categories,warehouse_category_description'];
			$this->form[] = ['label'=>'Total Qty','name'=>'total_qty','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-5'];
			# END FORM DO NOT REMOVE THIS LINE

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
            $this->addaction[] = ['title'=>'Print','url'=>CRUDBooster::mainpath('print').'/[id]','icon'=>'fa fa-print','color'=>'info','showIf'=>'[print_flag]==0'];

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
            if(CRUDBooster::isUpdate() && CRUDBooster::isSuperadmin()){
				$this->button_selected[] = ['label'=>'Reset Count','icon'=>'fa fa-refresh','name'=>'Reset_Count'];
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
                if(CRUDBooster::isSuperAdmin() || in_array(CRUDBooster::myPrivilegeName(),["Scanner"])){
                    $this->index_button[] = ['label'=>'Scan Items','url'=> route('count.scan'),'icon'=>'fa fa-search','color'=>'info'];
                }
                $this->index_button[] = ['label'=>'Export Count','url'=>"javascript:showCountExport()",'icon'=>'fa fa-download'];
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
            $this->script_js = "
                function showCountExport() {
                    $('#modal-count-export').modal('show');
                }
            ";

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
            $this->post_index_html = "
			<div class='modal fade' tabindex='-1' role='dialog' id='modal-count-export'>
				<div class='modal-dialog'>
					<div class='modal-content'>
						<div class='modal-header'>
							<button class='close' aria-label='Close' type='button' data-dismiss='modal'>
								<span aria-hidden='true'>×</span></button>
							<h4 class='modal-title'><i class='fa fa-download'></i> Export Count</h4>
						</div>

						<form method='post' target='_blank' action=".CRUDBooster::mainpath("export").">
                        <input type='hidden' name='_token' value=".csrf_token().">
                        ".CRUDBooster::getUrlParameters()."
                        <div class='modal-body'>
                            <div class='form-group'>
                                <label>File Name</label>
                                <input type='text' name='filename' class='form-control' required value='Export Count - ".date('Y-m-d H:i:s')."'/>
                            </div>
						</div>
						<div class='modal-footer' align='right'>
                            <button class='btn btn-default' type='button' data-dismiss='modal'>Close</button>
                            <button class='btn btn-primary btn-submit' type='submit'>Submit</button>
                        </div>
                    </form>
					</div>
				</div>
			</div>";


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
            if($button_name == 'Reset_Count'){
                CountHeader::whereIn('id',$id_selected)->delete();
                CountLine::whereIn('count_headers_id',$id_selected)->delete();
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
            if(in_array(CRUDBooster::myPrivilegeName(), ["Scanner","Counter"])){
                $query->where('count_headers.created_by',CRUDBooster::myId());
            }
            if(in_array(CRUDBooster::myPrivilegeName(), ["Verifier"])){
                $query->where('count_headers.updated_by',CRUDBooster::myId());
            }
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

        public function getDetail($id)
        {
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE || $this->button_detail==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Count Details';
            $data['header'] = CountHeader::where('count_headers.id',$id)
            ->join('count_types','count_headers.count_types_id','count_types.id')
            ->join('warehouse_categories','count_headers.warehouse_categories_id','warehouse_categories.id')
            ->leftJoin('cms_users as scanby','count_headers.created_by','scanby.id')
            ->leftJoin('cms_users as verifyby','count_headers.updated_by','verifyby.id')
            ->select(
                'count_headers.id',
                'count_types.count_type_code',
                'count_headers.category_tag_number',
                'warehouse_categories.warehouse_category_description',
                'count_headers.total_qty',
                'scanby.name as scan_by',
                'count_headers.created_at as scan_at',
                'verifyby.name as verify_by',
                'count_headers.updated_at as verify_at'
            )->first();

            $data['items'] = CountLine::where('count_lines.count_headers_id',$id)
            ->join('items','count_lines.item_code','items.digits_code')
            ->join('warehouse_categories','items.warehouse_categories_id','warehouse_categories.id')
            ->select('count_lines.*','items.item_description','warehouse_categories.warehouse_category_description')
            ->get();

            $data['sku_count'] = CountLine::where('count_lines.count_headers_id',$id)->count();
            return view('counter.detail',$data);
        }

        public function getPrint($id)
        {
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Print Count Details';
            $data['header'] = CountHeader::where('count_headers.id',$id)
            ->join('count_types','count_headers.count_types_id','count_types.id')
            ->join('warehouse_categories','count_headers.warehouse_categories_id','warehouse_categories.id')
            ->leftJoin('cms_users as scanby','count_headers.created_by','scanby.id')
            ->leftJoin('cms_users as verifyby','count_headers.updated_by','verifyby.id')
            ->select(
                'count_headers.id',
                'count_types.count_type_code',
                'count_headers.category_tag_number',
                'warehouse_categories.warehouse_category_description',
                'count_headers.total_qty',
                'scanby.name as scan_by',
                'count_headers.created_at as scan_at',
                'verifyby.name as verify_by',
                'count_headers.updated_at as verify_at'
            )->first();

            $data['items'] = CountLine::where('count_lines.count_headers_id',$id)
            ->join('items','count_lines.item_code','items.digits_code')
            ->join('warehouse_categories','items.warehouse_categories_id','warehouse_categories.id')
            ->select('count_lines.*','items.item_description','warehouse_categories.warehouse_category_description')
            ->get();

            $data['sku_count'] = CountLine::where('count_lines.count_headers_id',$id)->count();
            CountHeader::where('count_headers.id',$id)->update(['print_flag'=>1]);
            return view('counter.print',$data);
        }

        public function getScan()
        {
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Scan Items';
            $userName = User::where('id',CRUDBooster::myId())->first();

            $data['count_types'] = CountType::where('status','ACTIVE')->get();

            $data['headers'] = CountTempHeader::where('created_by',CRUDBooster::myId())
                ->whereNull('deleted_at')
                ->orderBy('id','desc')
                ->first();

            $category_tags = UserCategoryTag::where('status','ACTIVE');

            if(CRUDBooster::myPrivilegeName() == "Scanner"){
                $category_tags->where('user_name',$userName->user_name);
            }
            if(empty($data['headers'])){
                $category_tags->where('is_used',0);
            }

            $data['category_tags'] = $category_tags->get()->toArray();

            $data['verifiers'] = User::where('id_cms_privileges',UserPrivilege::withName("Verifier")->id)
                ->where('status','ACTIVE')
                ->orderBy('name','asc')
                ->get();

            $data['categories'] = WarehouseCategory::where('status','ACTIVE')
                ->whereIn('id',array_column($data['category_tags'],'warehouse_categories_id'))
                ->orderBy('warehouse_category_description','asc')
                ->get();

            $data['lines'] = CountTempLine::where('count_temp_lines.count_temp_headers_id', $data['headers']->id)
                ->whereNull('count_temp_lines.deleted_at')
                ->join('items','count_temp_lines.item_code','items.digits_code')
                ->join('warehouse_categories','items.warehouse_categories_id','warehouse_categories.id')
                ->select('count_temp_lines.*','items.item_description','warehouse_categories.warehouse_category_description')
                ->get();


            return view('counter.scan',$data);
        }

        public function saveScan(Request $request)
        {

            $header = CountHeader::firstOrCreate([
                    'category_tag_number' => $request->category_tag,
					'count_types_id' => $request->count_type,
                ],
                [
                    'count_types_id' => $request->count_type,
                    'category_tag_number' => $request->category_tag,
                    'warehouse_categories_id' => $request->warehouse_category,
                    'total_qty' => $request->total_quantity,
                    'updated_by' => $request->verified_by,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            if($request->has('item_code')){
                foreach ($request->item_code as $key => $value) {
                    CountLine::firstOrCreate([
                        'count_headers_id' => $header->id,
                        'item_code' => $value
                    ],[
                        'count_headers_id' => $header->id,
                        'item_code' => $value,
                        'qty' => $request->qty[$key],
                        'revised_qty' => $request->revised_qty[$key],
                        'line_remarks' => $request->remarks[$key],
                        'line_color' => $request->line_color[$key]
                    ]);
                }
            }

            if($request->has('new_item_code')){
                foreach ($request->new_item_code as $keyNewItem => $valueNewItem) {

                    Item::firstOrCreate([
                        'digits_code' => $valueNewItem
                    ],[
                        'digits_code' => $valueNewItem,
                        'item_description' => $request->new_item_description[$keyNewItem],
                        'warehouse_categories_id' => $request->new_item_category[$keyNewItem]
                    ]);

                    CountLine::firstOrCreate([
                        'count_headers_id' => $header->id,
                        'item_code' => $valueNewItem
                    ],[
                        'count_headers_id' => $header->id,
                        'item_code' => $valueNewItem,
                        'qty' => $request->new_item_qty[$keyNewItem],
                        'revised_qty' => $request->new_item_revised_qty[$keyNewItem],
                        'line_remarks' => $request->new_item_remarks[$keyNewItem],
                        'line_color' => $request->new_line_color[$keyNewItem]
                    ]);
                }
            }

            CountTempHeader::where('id',$request->temp_headers_id)->delete();
            CountTempLine::where('count_temp_headers_id',$request->temp_headers_id)->delete();

            return redirect(CRUDBooster::mainpath())->with([
				'message' => 'Count saved!',
				'message_type' => 'success'
			]);
        }

        public function countExport(Request $request)
		{
			$filename = $request->input('filename');
			return Excel::download(new CountExport, $filename.'.xlsx');
		}

	}
