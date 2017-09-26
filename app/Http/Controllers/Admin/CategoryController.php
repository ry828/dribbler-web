<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $categories = Category::where('active', '!=', '2')->get();

        return view('admin.pages.categories', compact('categories'));
    }

    public function goto_add_category() {
        return view('admin.pages.addEditCategory');
    }

    public function goto_edit_category($category_id) {
        $category = Category::findOrFail($category_id);
        return view('admin.pages.addEditCategory', compact('category'));
    }

    public function create_category()
    {
        $inputs = Input::all();

        $roles = [
            'category_title' => 'required | max:255',
            'lock' => 'required | boolean',
            'price' => 'required | numeric',
            'thumbnail' => 'required | file'
        ];

        $validator = Validator::make($inputs, $roles);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $category = new Category();
        $category->fill($inputs);

        if (Input::hasFile('thumbnail')) {
           // Upload thumbnail to S3
            try {
                $thumbnail = Input::file('thumbnail');
                $img = Image::make($thumbnail)->encode('png')->resize(300, 300)->stream();
                $thumbnailFileName = 'category/'.$category->category_title . '/thumbnail.' . $thumbnail->extension();

                Storage::disk('S3Video')->put($thumbnailFileName, (string)$img, 'public');
                $category->thumbnail = Storage::disk('S3Video')->url($thumbnailFileName);

            } catch (Exception $e) {
                $category->delete();
                return $this->responseBadRequestError([]);
            }        
        }
        $category->save();

        Session::flash('flash_message', 'Added');

        return redirect('/admin/categories');
    }

    public function update_category($category_id)
    {
        $inputs = Input::all();

        $roles = [
            'category_id' => 'required | numeric',
            'category_title' => 'required | max:255',
            'lock' => 'required | boolean',
            'price' => 'required | numeric',
            'thumbnail' => 'required | file'
        ];

        $validator = Validator::make($inputs, $roles);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $category = Category::findOrFail($category_id);
        $category->fill($inputs);
        $category->save();

        Session::flash('flash_message', 'Changes Saved');

        return redirect('/admin/categories');
    }

    public function active_category($category_id)
    {
        $category = Category::findOrFail($category_id);
        $category->active = 1;
        $category->save();

        return redirect('/admin/categories');
    }
    public function inactive_category($category_id)
    {
        $category = Category::findOrFail($category_id);
        $category->active = 0;
        $category->save();

        return redirect('/admin/categories');
    }

    public function delete_category($category_id)
    {
        $category = Category::findOrFail($category_id);
        $category->active = 2;
        $category->save();

        Session::flash('flash_message', 'Deleted');

        return redirect('/admin/categories');
    }











    public function getTags()
    {
        $tags = Tag::where('active', '1')->get();

        return view('admin.pages.tags', compact('tags'));
    }

    public function goto_add_tag() {
        return view('admin.pages.addEditTag');
    }

    public function goto_edit_tag($tag_id) {
        $tag = Tag::findOrFail($tag_id);
        return view('admin.pages.addEditTag', compact('tag'));
    }


    public function create_tag()
    {
        $inputs = Input::all();

        $roles = [
            'tag_name' => 'required | max:50',
        ];

        $validator = Validator::make($inputs, $roles);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $tag = new Tag();
        $tag->fill($inputs);
        $tag->save();

        Session::flash('flash_message', 'Added');

        return redirect('/admin/tags');
    }

    public function update_tag($tag_id)
    {
        $inputs = Input::all();

        $roles = [
            'tag_name' => 'required | max:50',
        ];

        $validator = Validator::make($inputs, $roles);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $tag = Tag::findOrFail($tag_id);
        $tag->fill($inputs);
        $tag->save();

        Session::flash('flash_message', 'Changes Saved');

        return redirect('/admin/tags');
    }

    public function delete_tag($tag_id)
    {
        $tag = Tag::findOrFail($tag_id);
        $tag->active = 0;
        $tag->save();

        return redirect('/admin/tags');
    }








    public function ajax_get_category()
    {
        $id = Input::get('category_id');
        $category = Category::findOrFail($id);

        return response()->json($category);
    }

    public function ajax_next_id()
    {
        $statement = DB::select("show table status like 'categories'");

        return response()->json(['category_id' => $statement[0]->Auto_increment]);
    }

    public function ajax_next_tag_id()
    {
        $statement = DB::select("show table status like 'tags'");

        return response()->json(['tag_id' => $statement[0]->Auto_increment]);
    }

    public function ajax_get_tag()
    {
        $tag_id = Input::get('tag_id');
        $tag = Tag::find($tag_id);

        return response()->json($tag);
    }

}
