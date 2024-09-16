<?php namespace App\Http\Controllers;

    use App\Models\Item;
    use App\Traits\ItemTrait;
    use Session;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;
    use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

	class AdminItemsController extends \crocodicstudio\crudbooster\controllers\CBController {

        use ItemTrait;

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "digits_code";
			$this->limit = "20";
			$this->orderby = "digits_code,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = true;
			$this->button_action_style = "button_icon";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = true;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "items";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Digits Code","name"=>"digits_code"];
			$this->col[] = ["label"=>"Upc Code","name"=>"upc_code"];
			$this->col[] = ["label"=>"Upc Code2","name"=>"upc_code2"];
			$this->col[] = ["label"=>"Upc Code3","name"=>"upc_code3"];
			$this->col[] = ["label"=>"Upc Code4","name"=>"upc_code4"];
			$this->col[] = ["label"=>"Upc Code5","name"=>"upc_code5"];
			$this->col[] = ["label"=>"Item Description","name"=>"item_description"];
            $this->col[] = ["label"=>"Model","name"=>"model"];
            $this->col[] = ["label"=>"Brand","name"=>"brands_id","join"=>"brands,brand_description"];
            $this->col[] = ["label"=>"Warehouse Category","name"=>"warehouse_categories_id","join"=>"warehouse_categories,warehouse_category_description"];

			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Digits Code','name'=>'digits_code','type'=>'text','validation'=>'required|min:1|max:10','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Upc Code','name'=>'upc_code','type'=>'text','validation'=>'required|min:1|max:60','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Upc Code2','name'=>'upc_code2','type'=>'text','validation'=>'required|min:1|max:60','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Upc Code3','name'=>'upc_code3','type'=>'text','validation'=>'required|min:1|max:60','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Upc Code4','name'=>'upc_code4','type'=>'text','validation'=>'required|min:1|max:60','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Upc Code5','name'=>'upc_code5','type'=>'text','validation'=>'required|min:1|max:60','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Item Description','name'=>'item_description','type'=>'text','validation'=>'required|min:1|max:150','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Model','name'=>'model','type'=>'text','validation'=>'required|min:1|max:150','width'=>'col-sm-5'];
			$this->form[] = ['label'=>'Brand','name'=>'brands_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-5',
                'datatable'=>'brands,brand_description'];
			$this->form[] = ['label'=>'Warehouse Category','name'=>'warehouse_categories_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-5',
                'datatable'=>'warehouse_categories,warehouse_category_description'];
			# END FORM DO NOT REMOVE THIS LINE

	        $this->index_button = array();
            $this->index_button[] = ["label"=>"Pull New Items","url"=>"javascript:pullNewItems()","icon"=>"fa fa-download","color"=>"warning"];
            $this->index_button[] = ["label"=>"Update Items","url"=>route('items.pull-update-item'),"icon"=>"fa fa-refresh","color"=>"info"];

            $this->script_js = NULL;
			$this->script_js = "
				function pullNewItems() {
					$('#modal-pull-new-items').modal('show');
				}
                $(document).ready(function() {
                    $('.dateInput').datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true
                    }).on('changeDate', function(e) {
                        const date = e.format('yyyy-mm-dd');
                        console.log(date);
                    });
                });
			";

	        $this->post_index_html = null;
			$this->post_index_html = "
			<div class='modal fade' tabindex='-1' role='dialog' id='modal-pull-new-items'>
				<div class='modal-dialog'>
					<div class='modal-content'>
						<div class='modal-header'>
							<button class='close' aria-label='Close' type='button' data-dismiss='modal'>
								<span aria-hidden='true'>×</span></button>
							<h4 class='modal-title'><i class='fa fa-download'></i> Pull New Items</h4>
						</div>

						<form method='get' target='_blank' action=".route('items.pull-new-item').">
                        <input type='hidden' name='_token' value=".csrf_token().">
                        ".CRUDBooster::getUrlParameters()."
                        <div class='modal-body'>
                            <div class='form-group'>
                                <label>Date From</label>
                                <input type='text' name='datefrom' class='form-control dateInput' required />
                            </div>
                            <div class='form-group'>
                                <label>Date To</label>
                                <input type='text' name='dateto' class='form-control dateInput' required />
                            </div>
						</div>
						<div class='modal-footer' align='right'>
                            <button class='btn btn-default' type='button' data-dismiss='modal'>Close</button>
                            <button class='btn btn-primary btn-submit' type='submit'>Submit</button>
                        </div>
                    </form>
					</div>
				</div>
			</div>
			";

	    }

        public function getItem(Request $request) {
            return json_encode(Item::getItem($request->item_code)->get());
        }

        public function getNewItem(Request $request){

            $validation = Validator::make($request->all(), [
                'datefrom' => ['required', 'date_format:Y-m-d', 'before:dateto'],
                'dateto'   => ['required', 'date_format:Y-m-d', 'after:datefrom'],
            ], [
                'datefrom.before' => 'The datefrom must be before the dateto.',
                'dateto.after'    => 'The dateto must be after the datefrom.',
            ]);

            if($validation->fails()){
                return redirect()->back()->with([
                    'message_type'=>"danger",
                    'message'=> $validation->errors()
                ]);
            }
            //pull new items from api
            $newItems = $this->getApiData(config('item-api.api_create_item_url'), [
                'datefrom' => $request->datefrom,
                'dateto'   => $request->dateto,
            ]);

            foreach ($newItems['data'] ?? [] as $key => $value) {
                try {
                    Item::firstOrCreate(['digits_code'=>$value['digits_code']],
                        $value);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }

            Log::info("Pull new items done!");
            return redirect()->back()->with([
                'message' => 'Pull new items done!',
                'message_type' => 'info'
            ]);
        }

        public function getUpdateItem(){
            //pull updated items from api
            $updatedItems = $this->getApiData(config('item-api.api_update_item_url'));

            foreach ($updatedItems['data'] ?? [] as $key => $value) {
                try {
                    Item::where('digits_code',$value->digits_code)
                        ->update($value);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }

            Log::info("Update items done!");
            return redirect()->back()->with([
                'message' => 'Update items done!',
                'message_type' => 'info'
            ]);
        }

	}
