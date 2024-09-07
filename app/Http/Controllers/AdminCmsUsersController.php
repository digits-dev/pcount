<?php namespace App\Http\Controllers;

use App\Exports\ExcelTemplateExport;
use App\Imports\UserImport;
use App\Models\User;
use Session;
use Illuminate\Http\Request;
use DB;
use CRUDBooster;
use crocodicstudio\crudbooster\controllers\CBController;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

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

        $this->index_button = array();
        if(CRUDBooster::getCurrentMethod() == 'getIndex'){
            $this->index_button[] = ['label'=>'Import Users','url'=> route('users.get-import'),'icon'=>'fa fa-upload','color'=>'info'];
        }

        $this->button_selected = array();
        if(CRUDBooster::isSuperadmin()){
            $this->button_selected[] = ["label"=>"Set Status ACTIVE ","icon"=>"fa fa-check-circle","name"=>"set_status_ACTIVE"];
            $this->button_selected[] = ["label"=>"Set Status INACTIVE","icon"=>"fa fa-times-circle","name"=>"set_status_INACTIVE"];
        }

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
    public function actionButtonSelected($id_selected,$button_name) {
        //Your code here
        $status = 'ACTIVE';
        switch ($button_name) {
            case 'set_status_ACTIVE':
                $status = 'ACTIVE';
                break;
            case 'set_status_INACTIVE':
                $status = 'INACTIVE';
                break;
            default:
                # code...
                break;
        }

        User::whereIn('id',$id_selected)->update([
            'status'=>$status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getImport()
    {
        if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {
            CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
        }

        $data = [];
        $data['page_title'] = 'Import Users';
        $data['uploadRoute'] = route('users.import');
        $data['uploadTemplate'] = route('users.get-template');
        return view('users.upload',$data);
    }

    public function getTemplate()
    {
        $header = array();
        $header[0] = ['NAME','USER NAME', 'EMAIL ADDRESS', 'PRIVILEGE', 'STATUS'];
        $header[1] = ['USER','ACC-1-1-1','user@pcount.com','SCANNER','ACTIVE'];
        $export = new ExcelTemplateExport([$header]);
        return Excel::download($export, 'users-'.date("Ymd").'-'.date("h.i.sa").'.csv');
    }

    public function importUsers(Request $request)
    {
        $errors = array();
        $path_excel = $request->file('import_file')->store('temp');
        $path = storage_path('app').'/'.$path_excel;
        HeadingRowFormatter::default('none');
        $headings = (new HeadingRowImport)->toArray($path);
        $excelData = Excel::toArray(new UserImport, $path);

        $header = array('NAME','USER NAME', 'EMAIL ADDRESS', 'PRIVILEGE', 'STATUS');

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
        Excel::import(new UserImport, $path);
        return redirect(CRUDBooster::mainpath())->with(['message_type' => 'success', 'message' => 'Upload complete!']);
    }
}
