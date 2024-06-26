<?php namespace App\Http\Controllers;

    use App\Models\Item;
    use App\Traits\ItemTrait;
    use Session;
	use Illuminate\Http\Request;
	use DB;
	use CRUDBooster;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;

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
            $this->index_button[] = ["label"=>"Pull New Items","url"=>route('items.pull-new-item'),"icon"=>"fa fa-download","color"=>"warning"];
            $this->index_button[] = ["label"=>"Update Items","url"=>route('items.pull-update-item'),"icon"=>"fa fa-refresh","color"=>"info"];

	    }

        public function getItem(Request $request)
        {
            return json_encode(Item::getItem($request->item_code)->get());
        }

        public function getNewItem(){
            //pull new items from api
            $newItems = $this->getApiData(config('item-api.api_create_item_url'));

            foreach ($newItems['data'] as $key => $value) {
                try {
                    Item::create($value);
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
            $newItems = $this->getApiData(config('item-api.api_update_item_url'));

            foreach ($newItems['data'] as $key => $value) {
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
