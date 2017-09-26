<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Tag;
use App\Trick;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class TrickController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $tricks = DB::table('tricks')
            ->select('trick_id', 'trick_title', 'category_title')
            ->join('categories', 'categories.category_id', 'tricks.category_id')
            ->get();

        foreach ($tricks as $trick) {
            $tag_names = Tag::leftJoin('trick_tag', 'trick_tag.tag_id', 'tags.tag_id')
                ->where('trick_tag.trick_id', $trick->trick_id)
                ->pluck('tags.tag_name')
                ->toArray();
            $trick_tags = implode(', ', $tag_names);
            $trick->trick_tags = $trick_tags;
        }


        return View('admin.pages.tricks', compact('tricks'));
    }
    public function goto_add_trick() {
        $categories = Category::all();
        $tags = Tag::all();
        return View('admin.pages.addEditTrick', compact( 'categories', 'tags'));
    }

    public function goto_edit_trick($trick_id) {
        $categories = Category::all();
        $tags = Tag::all();
        $trick = Trick::findOrFail($trick_id);
        $trickTags = DB::table('tags')
            ->join('trick_tag', 'tags.tag_id', 'trick_tag.tag_id')
            ->where('trick_tag.trick_id', $trick_id)
            ->get();

        return View('admin.pages.addEditTrick', compact('trick', 'categories', 'tags', 'trickTags'));
    }

    public function create_trick() {
        $trick = new Trick();
        $this->save_trick($trick);
    }

    public function update_trick($trick_id) {
        $trick = Trick::findOrFail($trick_id);
        $this->save_trick($trick);
    }
    public function getTrick($id = null)
    {
        $categories = Category::all();
        $tags = Tag::all();

        if (empty($id)) {
            return View('admin.pages.addEditTrick', compact('categories', 'tags'));
        }

        $trick = Trick::findOrFail($id);
        $trickTags = DB::table('tags')
            ->join('trick_tag', 'tags.tag_id', 'trick_tag.tag_id')
            ->where('trick_tag.trick_id', $id)
            ->get();

        return View('admin.pages.addEditTrick', compact('trick', 'categories', 'tags', 'trickTags'));
    }

    public function save_trick($trick) {

        $trick->category_id = Input::get('category_id');
        $trick->trick_title =Input::get('trick_title');
        $trick->trick_tags =Input::get('trick_tags');
        // $trick->trick_description = Input::get('trick_description');
        $trick->trick_description = '[{"title": "First", "thumbnail": "https://s3.eu-central-1.amazonaws.com/dribbler.org-category/beginner/Ultimate+Best+Football+Tricks+Skills/descriptioin/step1.png", "description": "LSLSs work with infants and children who are deaf or hard of hearing and their families seeking a listening and spoken language outcome in a variety of settings"}, {"title": "Second Step", "thumbnail": "https://s3.eu-central-1.amazonaws.com/dribbler.org-category/beginner/Ultimate+Best+Football+Tricks+Skills/descriptioin/step2.png", "description": "LSLSs work with infants and children who are deaf or hard of hearing and their families seeking a listening and spoken language outcome in a variety of settings"}, {"title": "Third Step", "thumbnail": "https://s3.eu-central-1.amazonaws.com/dribbler.org-category/beginner/Ultimate+Best+Football+Tricks+Skills/descriptioin/step3.png", "description": "LSLSs work with infants and children who are deaf or hard of hearing and their families seeking a listening and spoken language outcome in a variety of settings"}, {"title": "Firth Step", "thumbnail": "https://s3.eu-central-1.amazonaws.com/dribbler.org-category/beginner/Ultimate+Best+Football+Tricks+Skills/descriptioin/step4.png", "description": "LSLSs work with infants and children who are deaf or hard of hearing and their families seeking a listening and spoken language outcome in a variety of settings"}]';
        if (Input::hasFile('thumbnail') && Input::hasFile('hd_video')) {
           // Upload video and thumbnail to S3
            try {
                $thumbnail = Input::file('thumbnail');
                $img = Image::make($thumbnail)->encode('png')->resize(300, 300)->stream();
                $thumbnailFileName = $trick->category_id . '/'.$trick->trick_title.'/thumbnail' . $thumbnail->extension();

                $file = Input::file('hd_video');
                // $videoFileName = $video->video_id . '/video.' . $file->extension();
                $videoFileName = $trick->category_id . '/'.$trick->trick_title . '/video.mp4';

                Storage::disk('S3Video')->put($videoFileName, file_get_contents($file), 'public');
                Storage::disk('S3Video')->put($thumbnailFileName, (string)$img, 'public');
                $trick->trick_thumbnail = Storage::disk('S3Video')->url($thumbnailFileName);
                $trick->hd_video_url = Storage::disk('S3Video')->url($videoFileName);
                $trick->ld_video_url = $trick->hd_video_url;

            } catch (Exception $e) {
                $trick->delete();
                return $this->responseBadRequestError([]);
            }
        }
        $trick->save();


        return redirect('admin/tricks');
    }

    public function deleteTrick($trick_id)
    {
        $trick = Trick::findOrFail($trick_id);
        $trick->active = 2;
        $trick->save();

        return redirect('admin/tricks')->with(['Deleted successfully']);
    }
}
