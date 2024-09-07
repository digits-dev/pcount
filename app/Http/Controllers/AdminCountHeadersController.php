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

	        $this->addaction = array();
            $this->addaction[] = ['title'=>'Print','url'=>CRUDBooster::mainpath('print').'/[id]','icon'=>'fa fa-print','color'=>'info','showIf'=>'[print_flag]==0'];

	        $this->button_selected = array();
            if(CRUDBooster::isUpdate() && CRUDBooster::isSuperadmin()){
				$this->button_selected[] = ['label'=>'Reset Count','icon'=>'fa fa-refresh','name'=>'Reset_Count'];
			}

	        $this->index_button = array();
            if(CRUDBooster::getCurrentMethod() == 'getIndex'){
                if(CRUDBooster::isSuperAdmin() || in_array(CRUDBooster::myPrivilegeName(),["Scanner"])){
                    $this->index_button[] = ['label'=>'Scan Items','url'=> route('count.scan'),'icon'=>'fa fa-search','color'=>'info'];
                }
                $this->index_button[] = ['label'=>'Export Count','url'=>"javascript:showCountExport()",'icon'=>'fa fa-download'];
            }

	        $this->script_js = NULL;
            $this->script_js = "
                function showCountExport() {
                    $('#modal-count-export').modal('show');
                }
            ";

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

	    }

	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
            if($button_name == 'Reset_Count'){
                CountHeader::whereIn('id',$id_selected)->delete();
                CountLine::whereIn('count_headers_id',$id_selected)->delete();
            }
	    }

	    public function hook_query_index(&$query) {
	        //Your code here
            if(in_array(CRUDBooster::myPrivilegeName(), ["Scanner","Counter"])){
                $query->where('count_headers.created_by',CRUDBooster::myId());
            }
            if(in_array(CRUDBooster::myPrivilegeName(), ["Verifier"])){
                $query->where('count_headers.updated_by',CRUDBooster::myId());
            }
	    }

        public function getDetail($id)
        {
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE || $this->button_detail==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Count Details';
            $data['details'] = CountHeader::getDetail($id)->with([
                'countType',
                'warehouseCategory',
                'lines',
                'lines.item',
                'lines.item.itemWarehouseCategory'
            ])->first();

            return view('counter.detail',$data);
        }

        public function getPrint($id)
        {
            if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            $data = [];
            $data['page_title'] = 'Print Count Details';
            $data['details'] = CountHeader::getDetail($id)->with([
                'countType',
                'warehouseCategory',
                'lines',
                'lines.item',
                'lines.item.itemWarehouseCategory'
            ])->first();

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
                ->select('count_temp_lines.*','items.item_description','items.upc_code','warehouse_categories.warehouse_category_description')
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
