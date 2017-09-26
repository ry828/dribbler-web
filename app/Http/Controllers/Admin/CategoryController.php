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
        $categories = Category::all();

        return view('admin.pages.categories', compact('categories'));
    }

    public function getTags()
    {
        $tags = Tag::all();

        return view('admin.pages.tags', compact('tags'));
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

        return redirect()->back();
    }

    public function edit_category()
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

        $category = Category::findOrFail(Input::get(category_id));
        $category->fill($inputs);
        $category->save();

        Session::flash('flash_message', 'Changes Saved');

        return redirect()->back();
    }

    public function delete_category()
    {
        $category_id = Input::get('category_id');

        $category = Category::findOrFail($category_id);
        $category->delete();

        Session::flash('flash_message', 'Deleted');

        return redirect()->back();
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

    public function edit_tag()
    {
        $inputs = Input::all();

        $roles = [
            'tag_id' => 'required | numeric',
            'tag_name' => 'required | max:50',
        ];

        $validator = Validator::make($inputs, $roles);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $tag = Tag::findOrFail(Input::get('tag_id'));
        $tag->fill($inputs);
        $tag->save();

        Session::flash('flash_message', 'Changes Saved');

        return redirect()->back();
    }

    public function delete_tag()
    {
        $tag_id = Input::get('tag_id');
        $result = DB::table('trick_tag')->where('tag_id', $tag_id)->delete();
        $tag = Tag::findOrFail($tag_id);
        $tag->delete();

        Session::flash('flash_message', 'Deleted');

        return redirect()->back();
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
