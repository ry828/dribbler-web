<?php
namespace App\Http\Controllers\Admin;
use App\Unlock_rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Validator;

class UnlockController extends Controller
{
    public function __construct()
    {
    }
    public function index(Request $request)
    {
        $unlock_rules = DB::table('unlock_rule')
                ->leftJoin('categories', 'unlock_rule.category_id', 'categories.category_id')
                ->select('categories.category_title', 'unlock_rule.*')
                ->get();
        $rules = json_encode($unlock_rules);
        return View('admin.pages.unlock_management', compact('unlock_rules', 'rules'));

    }

    public function update_unlock_rule() {
        $data = Input::all();
        $category_id = $data['category_id'];
        $rule = Unlock_rule::where('category_id', $category_id)->first();
        $rule->fill($data);
        $rule->save();
        $unlock_rules = DB::table('unlock_rule')
                ->leftJoin('categories', 'unlock_rule.category_id', 'categories.category_id')
                ->select('categories.category_title', 'unlock_rule.*')
                ->get();
        return View('admin.pages.unlock_management', compact('unlock_rules'));
    }
}
