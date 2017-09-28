<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Tag;
use App\Trick;
// use Illuminate\Http\Request;
use App\Trick_tag;
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
            ->where('active', '1')
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
        $categories = Category::where('active', '1')->get();
        $tags = Tag::all();
        return View('admin.pages.addEditTrick', compact( 'categories', 'tags'));
    }

    public function goto_edit_trick($trick_id) {
        $categories = Category::where('active', '1')->get();
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
        $trick->category_id = Input::get('category_id');
        $trick->trick_title =Input::get('trick_title');

        if (Input::hasFile('thumbnail') && Input::hasFile('hd_video')) {
            // Upload video and thumbnail to S3
            try {
                $thumbnail = Input::file('thumbnail');
                $img = Image::make($thumbnail)->encode('png')->resize(300, 300)->stream();
                $thumbnailFileName = $trick->category_id . '/'.$trick->trick_title.'/thumbnail.' . $thumbnail->extension();

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

        //add description
        $description_titles = Input::get('title');
        $description_descriptions = Input::get('description');
        $descriptions = array();
        if (Input::hasFile('picture')) {
            $pictures = Input::file('picture');
            for ($i = 0; $i < count($pictures); $i ++) {

                try {
                    $thumbnail = Input::file('thumbnail');
                    $img = Image::make($thumbnail)->encode('png')->resize(300, 300)->stream();
                    $thumbnailFileName = $trick->category_id . '/'.$trick->trick_title.'/description_'.$i .'.'. $thumbnail->extension();
                    Storage::disk('S3Video')->put($thumbnailFileName, (string)$img, 'public');

                    $desc = array();
                    $desc['title'] = $description_titles[$i];
                    $desc['description'] = $description_descriptions[$i];
                    $desc['thumbnail'] = Storage::disk('S3Video')->url($thumbnailFileName);

//                    $description = json_encode($desc);

                    array_push($descriptions, $desc);
                } catch (Exception $e) {
                    $trick->delete();
                    return $this->responseBadRequestError([]);
                }
            }
        } else {

        }
        $trick->trick_description = json_encode($descriptions);
        //save trick
        $trick->save();

        //add tag
        $tag_ids =Input::get('trick_tags');
        foreach ($tag_ids as $tag_id) {
            $trick_tag = new Trick_tag();
            $trick_tag->tag_id = $tag_id;
            $trick_tag->trick_id = $trick->trick_id;
            $trick_tag->save();
        }

        return redirect('admin/tricks');
    }

    public function update_trick($trick_id) {
        $trick = Trick::findOrFail($trick_id);
        $trick->category_id = Input::get('category_id');
        $trick->trick_title =Input::get('trick_title');
        $trick->save();
        if (Input::hasFile('thumbnail')) {
            // Upload  thumbnail to S3
            try {
                $thumbnail = Input::file('thumbnail');
                $img = Image::make($thumbnail)->encode('png')->resize(300, 300)->stream();
                $thumbnailFileName = $trick->category_id . '/'.$trick->trick_title.'/thumbnail.' . $thumbnail->extension();
                Storage::disk('S3Video')->put($thumbnailFileName, (string)$img, 'public');
                $trick->trick_thumbnail = Storage::disk('S3Video')->url($thumbnailFileName);
                $trick->save();
            } catch (Exception $e) {
                $trick->delete();
                return $this->responseBadRequestError([]);
            }
        }

        if ( Input::hasFile('hd_video')) {
            // Upload video  to S3
            try {
                $file = Input::file('hd_video');
                // $videoFileName = $video->video_id . '/video.' . $file->extension();
                $videoFileName = $trick->category_id . '/'.$trick->trick_title . '/video.mp4';
                Storage::disk('S3Video')->put($videoFileName, file_get_contents($file), 'public');
                $trick->hd_video_url = Storage::disk('S3Video')->url($videoFileName);
                $trick->ld_video_url = $trick->hd_video_url;
                $trick->save();
            } catch (Exception $e) {
                $trick->delete();
                return $this->responseBadRequestError([]);
            }
        }

        //add description
        $description_titles = Input::get('title');
        $description_descriptions = Input::get('description');
        $descriptions = array();
        if (Input::hasFile('picture')) {
            $pictures = Input::file('picture');
            for ($i = 0; $i < count($pictures); $i ++) {

                try {
                    $thumbnail = Input::file('thumbnail');
                    $img = Image::make($thumbnail)->encode('png')->resize(300, 300)->stream();
                    $thumbnailFileName = $trick->category_id . '/'.$trick->trick_title.'/description_'.$i .'.'. $thumbnail->extension();
                    Storage::disk('S3Video')->put($thumbnailFileName, (string)$img, 'public');

                    $desc = array();
                    $desc['title'] = $description_titles[$i];
                    $desc['description'] = $description_descriptions[$i];
                    $desc['thumbnail'] = Storage::disk('S3Video')->url($thumbnailFileName);

//                    $description = json_encode($desc);

                    array_push($descriptions, $desc);
                } catch (Exception $e) {
                    $trick->delete();
                    return $this->responseBadRequestError([]);
                }
            }
        } else {

        }
        $trick->trick_description = json_encode($descriptions);
        //save trick
        $trick->save();

        //add tag
        $tag_ids =Input::get('trick_tags');
        foreach ($tag_ids as $tag_id) {
            $trick_tags = Trick_tag::where('trick_id', $trick_id)->where('tag_id', $tag_id)->get();
            if (count($trick_tags) == 0) {
                $trick_tag = new Trick_tag();
                $trick_tag->tag_id = $tag_id;
                $trick_tag->trick_id = $trick->trick_id;
                $trick_tag->save();

            }
        }

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
