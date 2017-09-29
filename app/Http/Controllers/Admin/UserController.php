<?php

namespace App\Http\Controllers\Admin;

use App\Comment;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RestfulAPIController;
use App\User;
use App\Video;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $users = User::where('role', '!=', 'admin')->where('status', '!=', 'deleted')->get();
        return View('admin.pages.users', compact('users'));

    }

    public function get_user($user_id)
    {
        $user = User::findOrFail($user_id);

        return View('admin.pages.addEditUser', compact('user'));
    }

    public function get_videos($user_id)
    {
        $user = User::findOrFail($user_id);
        $videos = Video::where('user_id', $user_id)->get();

        return View('admin.pages.user-videos', compact('user', 'videos'));
    }

    public function get_achievements($user_id)
    {
        $restfulApiController = new RestfulAPIController();
        $achievements = $restfulApiController->calculateAchievement($user_id);
        return View('admin.pages.user-achievements', compact('achievements', 'user_id'));
    }

    public function get_payments($user_id)
    {
         $transactions = DB::table('transactions')
            ->where('transactions.user_id', $user_id)
            ->leftJoin('users', 'users.id', 'transactions.user_id')
            ->leftJoin('categories', 'categories.category_id', 'transactions.category_id')
            ->select('transactions.transaction_id', 'transactions.value', 'transactions.created_at', 'users.name', 'categories.category_title', 'transactions.user_id')
            ->orderBy('transactions.created_at', 'desc')
            ->get();

        return View('admin.pages.user-payments', compact('transactions', 'user_id'));
    }

    public function active_user($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->status = 'active';
        $user->save();

        return redirect()->back();
    }

    public function inactive_user($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->status = 'inactive';
        $user->save();

        return redirect()->back();
    }

    public function delete_user($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->status = 'deleted';
        $user->save();

        Session::flash('flash_message', 'User Deleted');

        return redirect()->back();
    }
    public function update_user(Request $request)
    {
        $data = $request->all();

        if ($request->get('id')) {
            $role =  [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'birthday' => 'required',
                'gender' => 'required|max:50',
                'role' => 'required|max:50',
                'push_enable' => 'required|numeric',
                'high_video_enable' => 'required|numeric'
            ];
        } else {
            $role =  [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'birthday' => 'required',
                'gender' => 'required|unique'
            ];
        }

        $validator = Validator::make($data, $role);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        if ($request->get('id')) {
            $user = User::findOrFail($request->get('id'));
            $user->fill($data);
            $user->save();
        } else {
            $user = new User();
            $user->fill($data);
            $user->verified = true;
            $user->save();
        }

        if (Input::hasFile('photo')) {
            // Upload  avatar to S3
            try {
                $photo = Input::file('photo');
                $img = Image::make(Input::file('photo'))->encode('png')->resize(100, 100)->stream();
                $fileName = 'avatar' . $user->id . str_random(1) . "." . $photo->extension();
                //$bResult = Storage::disk('S3Avatar')->put($fileName, file_get_contents($photo), 'public');
                $bResult = Storage::disk('S3Avatar')->put($fileName, (string)$img, 'public');
                if ($bResult) {
                    $user->photo = Storage::disk('S3Avatar')->url($fileName);
                    $user->save();
                }
            } catch (Exception $e) {
                return redirect('admin/users')->with('flash_message', 'The user have been updated successfully, but user photo was not uploaded');
            }
        }
        return redirect('admin/users')->with('flash_message', 'The user have been updated successfully');
    }














    public function edit_video($video_id) {
        $video = Video::findOrFail($video_id);
        $user = User::findOrFail($video->user_id);

     return View('admin.pages.user-video-edit', compact('user', 'video'));
    }

    public function delete_video($video_id) {
        $video = Video::findOrFail($video_id);
        $user = User::findOrFail($video->user_id);

        $delete_video_like = DB::table('video_likes')->where('video_id', $video_id)->delete();
        $delete_video_comments = DB::table('comments')->where('video_id', $video_id)->delete();
        $delete_video = DB::table('videos')->where('video_id', $video_id)->delete();

        $videos = Video::where('user_id', $video->user_id)->get();
     return View('admin.pages.user-videos', compact('user', 'videos'));
    }

    public function update_video($video_id) {
        $video = Video::findOrFail($video_id);
        $user = User::findOrFail($video->user_id);
        if (Input::hasFile('thumbnail')) {
           // Upload video and thumbnail to S3
        try {
            $thumbnail = Input::file('thumbnail');
            $img = Image::make($thumbnail)->encode('png')->resize(300, 300)->stream();
            $thumbnailFileName = $video->video_id . '/thumbnail.' . $thumbnail->extension();

            $file = Input::file('hd_video');
            // $videoFileName = $video->video_id . '/video.' . $file->extension();
            $videoFileName = $video->video_id . '/video.mp4';

            Storage::disk('S3Video')->put($videoFileName, file_get_contents($file), 'public');
            Storage::disk('S3Video')->put($thumbnailFileName, (string)$img, 'public');
            $video->thumbnail = Storage::disk('S3Video')->url($thumbnailFileName);
            $video->hd_url = Storage::disk('S3Video')->url($videoFileName);
            $video->ld_url = $video->hd_url;
            $video->save();

        } catch (Exception $e) {
            $video->delete();
            return $this->responseBadRequestError([]);
        }

        }
        $videos = Video::where('user_id', $user->id)->get();

        return View('admin.pages.user-videos', compact('user', 'videos'));
    }

    public function get_comments($video_id) {
        $video = Video::findOrFail($video_id);
        $user = User::findOrFail($video->user_id);
        $comments = Comment::where('video_id', $video_id)
            ->leftJoin('users', 'users.id', 'comments.commentator_id')

            ->select('users.name', 'comments.message', 'comments.created_at', 'comments.comment_id')
            ->get();
        return View('admin.pages.user-video-comments', compact('comments', 'user', 'video_id'));
    }

    public function goto_edit_comment($video_id, $comment_id) {
        $comment = Comment::findOrFail($comment_id);
        return View('admin.pages.editComment', compact('comment', 'video_id'));
    }
    public function update_comment($video_id, $comment_id) {
        $comment = Comment::findOrFail($comment_id);
        $message = Input::get('message');
        $comment->message = $message;
        $comment->save();
        return redirect('admin/users/'.$video_id.'/comments');
    }

    public function delete_comment($comment_id) {
        $result = Comment::where('comment_id', $comment_id)->delete();
        Session::flash('flash_message', 'Comment Deleted');
        return redirect()->back();
    }










    public function ajax_update_connection() {
        $user_id = Input::get('user_id');
        $type = Input::get('type');
        $status = Input::get('status');
        $user = User::findOrFail($user_id);
        if (empty($user)) {
            return "error";
        } else {
            
            if ($type == 'facebook') {
                $user->fb_enable = $status;
            } else {
                $user->google_enable = $status;
            }
            $user->save();
            return 'success';
        }
    }
}
