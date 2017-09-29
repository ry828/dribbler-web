<?php
namespace App\Http\Controllers;

use App\Comment;
use App\Dribbler;
use App\Follow;
use App\Transaction;
use App\Trick_tag;
use App\User_Category;
use App\Video;
use App\Video_Like;
use App\User;
use App\Category;
use App\Trick;
use App\Tag;
use App\Unlock_rule;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;



require '../vendor/autoload.php';
use Mailgun\Mailgun;

class RestfulAPIController extends Controller
{
    public function __construct()
    {

    }

    /***********************************************************
     *  API - /users
     * *********************************************************
     */

    public function login()
    {
        $credentials = Input::all();
        $email = Input::get('email');
        $password = Input::get('password');
        $facebook_id = Input::get('facebook_id');
        $google_id = Input::get('google_id');

        $roles = [];
        if (Input::has('email')) {
            $roles = [
                'email' => 'required | email',
                'password' => 'required'
            ];
        } else if (Input::has('facebook_id')) {
            $roles = [
                'facebook_id' => 'required',
            ];
        } else if (Input::has('google_id')) {
            $roles = [
                'google_id' => 'required',
            ];
        }

        $validator = Validator::make($credentials, $roles);
        if ($validator->fails()) {
            return $this->responseValidationError($validator);
        }

        if (!empty($facebook_id)) { // facebook login
            $user = User::where('facebook_id', $facebook_id)->first();
            if (empty($user)) {
                return $this->responseBadRequestError('Bad Profile');
            }

        } else if (!empty($google_id)) { // Google login
            $user = User::where('google_id', $google_id)->first();
            if (empty($user)) {
                return $this->responseBadRequestError('Bad Profile');
            }

        } else { // Basic Login
            $user = User::where('email', $email)->first();
            if (empty($user)) {
                return $this->responseBadRequestError('Email or Password is incorrect');
            }
            $hashedPassword = $user->password;
            if (!Hash::check($password, $hashedPassword)) {
                return $this->responseBadRequestError('Email or Password is not correct');
            }
            if ($user->status != 'active') {
                return $this->responseAccessDeniedError('Account is disabled or deleted');
            }
        }

        // Create Token
        $token = JWTAuth::fromUser($user);
        $response = array_merge($user->toArray(), ['token' => $token]);

        return $this->responseSuccess($response);
    }

    public function register()
    {
        $credentials = Input::all();

        $validator = Validator::make($credentials, [
            'email' => 'required | email | unique:users',
            'password' => 'required | max:30',
            'first_name' => 'required | max:50',
            'last_name' => 'required | max:50',
            'gender' => 'required',
            'birthday' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseValidationError($validator);
        }

        $user = User::where('email', $credentials['email'])->first();
        if (!empty($user)) {
            return $this->responseBadRequestError('Email is already exist');
        }

        // Create new user
        $user = new User();
        $user->fill($credentials);
        if (Input::has('facebook_id') || Input::has('google_id')) {
            $user->verified = true;
        } else {
            $user->verified = false;
        }
        $user->subscribe = false;
        $user->status = "active";
        $user->name = $user->first_name.' '.$user->last_name;
        $user->save();

        // Upload avatar to S3
        if (Input::hasFile('photo')) {
            $photo = Input::file('photo');
            $img = Image::make(Input::file('photo'))->encode('png')->resize(100, 100)->stream();
            $fileName = 'avatar' . $user->id . str_random(1) . "." . $photo->extension();

            //$bResult = Storage::disk('S3Avatar')->put($fileName, file_get_contents($photo), 'public');
            $bResult = Storage::disk('S3Avatar')->put($fileName, (string)$img, 'public');
            if ($bResult) {
                $user->photo = Storage::disk('S3Avatar')->url($fileName);
                $user->save();
            }
        }

        // if user didn't sign up with social networks, send verification email.
        // if (!$user->verified) {
        //    $user->confirmation_code = str_random(16);
        //    $user->save();
        //    $this->sendEmail($user->email, 'Verification Email', $user->confirmation_code);
        // }


        // Create Token
        $token = JWTAuth::fromUser($user);
        $response = array_merge($user->toArray(), ['token' => $token]);

        return $this->responseSuccess($response);
    }
    function sendEmail($email, $text, $code) {
        $mailgun_key = 'key-e5e3b19a2cab708fe15e59f1a48adb8f';
        $domain = 'mg.dribbler.org';

        $mg = new Mailgun($mailgun_key);
        $email_result = $mg->sendMessage($domain, array(
                'from'    => 'info@dribbler.org',
                'to'      => $email,
                'subject' => $text,
                'text'    => $code));
        $http_response_code = $email_result->http_response_code;
        if ($http_response_code == 200) {
            return $this->responseSuccess();
        } else {
            return $this->responseValidationError();
        }
        
    }

    public function forgot_password()
    {
        $email = Input::get('email');
        $validator = Validator::make([
            'email' => $email
        ], [
            'email' => 'required | email'
        ]);

        if ($validator->fails()) {
            return $this->responseValidationError($validator);
        }

        $user = User::where('email', $email)->first();
        if (empty($user)) {
            return $this->responseBadRequestError("Email is not exist");
        }

        // Send Email For Reset Password
        $rand = random_int(10000, 99999);
        $message = "Try to login with " . $rand . ". After login, please reset password for security.";
        $this->sendEmail($email, "Forgot Password?", $message);

        $user->password = $rand;
        $user->save();

        return $this->responseSuccess();
    }

    public function postProfile()
    {
        $inputs = Input::all();
        $me = JWTAuth::parseToken()->authenticate();
        $me->fill($inputs);
        $me->name = $me->first_name.' '.$me->last_name;
        $me->save();

        // Upload avatar to S3
        if (Input::hasFile('photo')) {
            $photo = Input::file('photo');
            $img = Image::make(Input::file('photo'))->encode('png')->resize(100, 100)->stream();
            $fileName = 'avatar' . $me->id . str_random(1) . "." . $photo->extension();

            $bResult = Storage::disk('S3Avatar')->put($fileName, (string)$img, 'public');
            if ($bResult) {
                $me->photo = Storage::disk('S3Avatar')->url($fileName);
                $me->save();
            }
        }

        return $this->responseSuccess($me);
    }

    public function deleteProfile()
    {

    }

    public function logout()
    {

    }

    /*************************************************************
     * Profile
     * ***********************************************************
     */
     public function updateProfiles()
    {
        $users = User::get();
        foreach ($users as $user) {
            $this->updateProfile($user);
        }
        //update overall ranking
        $users = User::orderBy('dribble_score', 'desc')->get();
        $index = 0;
        for ($index = 0; $index < count($users); $index ++) { 
            $user = $users[$index];
            $user->overall_ranking = $index + 1;
            $user->save();
        }
        return $this->responseSuccess(['result' => 'success']);
    }
    public function updateProfile($user)            
    {
        //calculate dribble score
        $videos = Video::where('user_id', $user->id)->get();
        $like_count = 0;
        $view_count = 0;
        foreach ($videos as $video) {
            $like_count += $video->likes;
            $view_count += $video->views;
        }
        if ($view_count == 0) {
            $user->dribble_score = 0;
        } else {
            $user->dribble_score = $like_count / $view_count * 100;
        }
        //calculate video count
        $user->video_count = count($videos);
        //calculate trick completion count
        $completed_trick_count = 0;
        $tricks = Trick::get();
        foreach ($tricks as $trick) {
            $dribblers = Dribbler::where('user_id', $user->id)->where('trick_id', $trick->trick_id)->get();
            $total_try_on = 0;
                foreach ($dribblers as $dribbler) {
                    $total_try_on += $dribbler->try_on;          
                }
                if (count($dribblers) > 0) {
                    $average = $total_try_on / count($dribblers);
                } else {
                    $average = 0;
                }
                if ($average >= 9) {
                    $completed_trick_count += 1;
                }
        }
        $total_dribblers = Dribbler::where('user_id', $user->id)->get();
        if (count($total_dribblers) > 0) {
            $user->trick_completion_count = $completed_trick_count;
        }
        //calculate medal
        $achievements = $this->calculateAchievement($user->id);
        $total_count = $achievements['bronze_count'] + $achievements['silver_count'] + $achievements['gold_count'];
        if ($total_count > 0) {
            if ($achievements['gold_count'] / $total_count * 100 >= 90) {
                $user->dribble_medal = 3;
            } else if ($achievements['silver_count'] / $total_count * 100 >= 80) {
                $user->dribble_medal = 2;
            } else if ($achievements['bronze_count'] / $total_count * 100 >= 70) {
                $user->dribble_medal = 1;
            } else {
                $user->dribble_medal = 0;
            }
        } else {

        }
        
        $user->save();
        $this->check_unlock($user);
    }
    //check auto unlock
    function check_unlock($user) {
        ///get unlock rule
        $unlock_rules = Unlock_rule::get();
        //init variables
        $uploaded_video_count_per_trick_amature = 1000;
        $uploaded_video_count_per_trick_advanced = 1000;
        $view_count_per_video_amature = 1000;
        $view_count_per_video_advanced = 1000;
        $dribbler_average_amature = 1000;
        $try_count_amature = 1000;
        $dribbler_average_advanced = 1000;
        $try_count_advanced = 1000;
        $gold_medal_count = 0;
        $follower_count = 0;
        $following_count = 0;
        //
        $tricks = Trick::where('category_id', '1')->get();
        foreach ($tricks as $trick) {

            $videos = Video::where('user_id', $user->id)->where('trick_id', $trick->trick_id)->get();
            $uploaded_video_count_per_trick_amature = min($uploaded_video_count_per_trick_amature, count($videos));

            $total_like_count = 0;
            foreach ($videos as $video) {
                $view_count_per_video_amature = min($view_count_per_video_amature, $video->views);
                $total_like_count += $video->likes;
            }

            $dribblers = Dribbler::where('user_id', $user->id)->where('trick_id', $trick->trick_id)->get();
            $try_count_amature = min($try_count_amature, count($dribblers));

            $total_try_on = 0;
            foreach ($dribblers as $dribbler) {
                $total_try_on += $dribbler->try_on;
            }
            if (count($dribblers) > 0) {
                $average = $total_try_on / count($dribblers);
            } else {
                $average = 0;
            }
            $dribbler_average_amature = min($dribbler_average_amature, $average);
           
            if ($average >= 9 && $total_like_count >= 60) {
                $gold_medal_count += 1;
            }
        }
        //
        $tricks = Trick::where('category_id', '2')->get();
        foreach ($tricks as $trick) {

            $videos = Video::where('user_id', $user->id)->where('trick_id', $trick->trick_id)->get();
            $uploaded_video_count_per_trick_advanced = min($uploaded_video_count_per_trick_advanced, count($videos));

            $total_like_count = 0;
            foreach ($videos as $video) {
                $view_count_per_video_advanced = min($view_count_per_video_advanced, $video->views);
                $total_like_count += $video->likes;
            }

            $dribblers = Dribbler::where('user_id', $user->id)->where('trick_id', $trick->trick_id)->get();
            $try_count_advanced = min($try_count_advanced, count($dribblers));

            $total_try_on = 0;
            foreach ($dribblers as $dribbler) {
                $total_try_on += $dribbler->try_on;
            }
            if (count($dribblers) > 0) {
                $average = $total_try_on / count($dribblers);
            } else {
                $average = 0;
            }
            $dribbler_average_advanced = min($dribbler_average_advanced, $average);
           
            if ($average >= 9 && $total_like_count >= 60) {
                $gold_medal_count += 1;
            }
        }

        $followers = Follow::where('user_id', $user->id)->where('follow_status', '1')->get();
        $follower_count = count($followers);

        $followings = Follow::where('follower_id', $user->id)->where('follow_status', '1')->get();
        $following_count = count($followings);

        if ($unlock_rules[0]->facebook_connect <= $user->enable_facebook 
            && $unlock_rules[0]->google_connect <= $user->enable_google
            && $unlock_rules[0]->upload_video_count_per_trick_amature <= $uploaded_video_count_per_trick_amature
            && $unlock_rules[0]->view_count_per_video_amature <= $view_count_per_video_amature
            && $unlock_rules[0]->try_count_amature <= $try_count_amature
            && $unlock_rules[0]->dribbler_average_amature <= $dribbler_average_amature
            && $unlock_rules[0]->gold_medal_count <= $gold_medal_count
            && $unlock_rules[0]->follower_count <= $follower_count
            && $unlock_rules[0]->following_count <= $following_count ) {
            //unlock advanced
            $user_cateogry = new User_Category();
            $user_cateogry->user_id = $user->id;
            $user_cateogry->category_id = 2;
            $user_cateogry->type = 1;//payment, 1: unlock rule
            $user_cateogry->save();
        }
        if ($unlock_rules[1]->facebook_connect <= $user->enable_facebook 
            && $unlock_rules[1]->google_connect <= $user->enable_google
            && $unlock_rules[1]->upload_video_count_per_trick_amature <= $uploaded_video_count_per_trick_amature
            && $unlock_rules[1]->view_count_per_video_amature <= $view_count_per_video_amature
            && $unlock_rules[1]->try_count_amature <= $try_count_amature
            && $unlock_rules[1]->dribbler_average_amature <= $dribbler_average_amature
            && $unlock_rules[1]->uploaded_video_count_per_trick_advanced <= $uploaded_video_count_per_trick_advanced
            && $unlock_rules[1]->view_count_per_video_advanced <= $view_count_per_video_advanced
            && $unlock_rules[1]->try_count_advanced <= $try_count_advanced
            && $unlock_rules[1]->dribbler_average_advanced <= $dribbler_average_advanced
            && $unlock_rules[1]->dribble_score <= $user->dribble_score
            && $unlock_rules[1]->gold_medal_count <= $gold_medal_count
            && $unlock_rules[1]->follower_count <= $follower_count
            && $unlock_rules[1]->following_count <= $following_count ) {
            //unlock advanced
            $user_cateogry = new User_Category();
            $user_cateogry->user_id = $user->id;
            $user_cateogry->category_id = 2;
            $user_cateogry->type = 1;//payment, 1: unlock rule
            $user_cateogry->save();
            //unlock professional
            $user_cateogry = new User_Category();
            $user_cateogry->user_id = $user->id;
            $user_cateogry->category_id = 3;
            $user_cateogry->type = 1;//payment, 1: unlock rule
            $user_cateogry->save();


        }
        $user->save();
    }
    public function calculateAchievement($user_id) {
        $bronze_count = 0;
        $silver_count = 0;
        $gold_count = 0;
        $tricks = Trick::get();
        $arr_data = array();
        foreach ($tricks as $trick) {
            $data = array();
            $data['trick'] = $trick;

            $dribblers = Dribbler::where('user_id', $user_id)->where('trick_id', $trick->trick_id)->get();
            $videos = Video::where('user_id', $user_id)->where('trick_id', $trick->trick_id)->get();
            $total_like_count = 0;
            foreach ($videos as $video) {
                $total_like_count += $video->likes;
            }
            $total_try_on = 0;
            if (count($dribblers) >= 10) {
                
                foreach ($dribblers as $dribbler) {
                    $total_try_on += $dribbler->try_on;          
                }
                $average = $total_try_on / count($dribblers);
                if ($average >= 9 && $total_like_count >= 60) {
                    $gold_count += 1;
                    $data['achievement'] = 3;
                } else if ($average >= 7  && $total_like_count >= 40) {
                    $silver_count += 1;
                    $data['achievement'] = 2;
                } else if ($average >= 5 && $total_like_count >= 20) {
                    $bronze_count += 1;
                    $data['achievement'] = 1;
                } 

            } else {
                $data['achievement'] = 0;
            }
            if (count($dribblers) > 0) {
                $data['average'] = $total_try_on / count($dribblers);
            } else {
                $data['average'] = 0;
            }
            array_push($arr_data, $data);
        }
        $result['bronze_count'] = $bronze_count;
        $result['silver_count'] = $silver_count;
        $result['gold_count'] = $gold_count;
        $result['data'] = $arr_data;

        return $result;
    }

    public function getAchievements($user_id) {
        return $this->responseSuccess($this->calculateAchievement($user_id));
    }

    public function getProfileStatistic($user_id)
    {
        //get overview statistic
        $overview_statistic = array();
        ////
        $categories = Category::where('active', '1')->get();
        foreach ($categories as $category) {
            $tricks = Trick::where('category_id', $category->category_id)->get();
            $average_per_category = 0;
            $total_average_per_category = 0;
            foreach ($tricks as $trick) {
                $average_per_trick = 0;
                $total_try_on = 0;
                $dribblers = Dribbler::where('trick_id', $trick->trick_id)->where('user_id', $user_id)->get();
                foreach ($dribblers as $dribbler) {
                    $total_try_on += $dribbler->try_on;
                }
                if (count($dribblers) == 0) {
                    $average_per_trick = 0;
                } else {
                    $average_per_trick = $total_try_on / count($dribblers);
                }
                $total_average_per_category += $average_per_trick;
            }
            if (count($tricks) == 0) {
                $average_per_category = 0;
            } else {
                $average_per_category = $total_average_per_category / count($tricks);
            }
            $statistic = array();
            $statistic['title'] = $category->category_title;
            $statistic['score'] = round($average_per_category, 1);
            array_push($overview_statistic, $statistic);

        }
        $result['overview'] = $overview_statistic;
        //get tag statistic
        $tag_statistic = array();
        ///get all tags
        $tags = Tag::get();
        foreach ($tags as $tag) {
            ///get tricks per tag
            $all_tricks = Trick::get();
            $tricks = array();
            foreach ($all_tricks as $trick) {
                $trick_tags =   explode(",", $trick->trick_tags);
                if (in_array($tag->tag_name, $trick_tags)) {
                    array_push($tricks, $trick);
                }
            }
            $average_per_tag = 0;
            $total_average_per_tag = 0;
            foreach ($tricks as $trick) {
                $average_per_trick = 0;
                $total_try_on = 0;
                ///get dribbler per trick
                $dribblers = Dribbler::where('trick_id', $trick->trick_id)->where('user_id', $user_id)->get();
                foreach ($dribblers as $dribbler) {
                    $total_try_on += $dribbler->try_on;
                }
                if (count($dribblers) == 0) {
                    $average_per_trick = 0;
                } else {
                    $average_per_trick = $total_try_on / count($dribblers);
                }
                $total_average_per_tag += $average_per_trick;
            }        
            if (count($tricks) == 0) {
                $average_per_tag = 0;
            } else {
                $average_per_tag = $total_average_per_tag / count($tricks);
            }
            $tag_object['tag'] = $tag;
            $tag_object['average'] = round($average_per_tag, 1);
            array_push($tag_statistic, $tag_object);
        }
        $result['tag_statistic'] = $tag_statistic;
        return $this->responseSuccess($result);
    }

    public function getProfileStatus($user_id)
    {
        $user = User::find($user_id);

        $tags = DB::table('tags')
            ->select('tags.*', DB::raw('Round(avg(try_on), 1) as score'))
            ->leftJoin('trick_tag', 'trick_tag.tag_id', 'tags.tag_id')
            ->leftJoin('dribblers', function ($join) use ($user) {
                $join->on('dribblers.trick_id', 'trick_tag.trick_id')
                    ->where('dribblers.user_id', $user->id);
            })
            ->groupBy('tags.tag_id')
            ->orderBy('score', 'desc')
            ->get();

        $categories = DB::table('categories')
            ->where('categories.active', '1')
            ->select('categories.*', DB::raw('Round(avg(try_on), 1) as score'))
            ->leftJoin('tricks', 'tricks.category_id', 'categories.category_id')
            ->leftJoin('dribblers', function ($join) use ($user) {
                $join->on('dribblers.trick_id', 'tricks.trick_id')
                    ->where('dribblers.user_id', $user->id);
            })
            ->groupBy('categories.category_id')
            ->orderBy('score', 'desc')
            ->get();

        $tricks = DB::table('tricks')
            ->select('tricks.trick_id', 'tricks.trick_title', DB::raw('Round(avg(try_on), 1) as score'))
            ->leftJoin('dribblers', function ($join) use ($user) {
                $join->on('dribblers.trick_id', 'tricks.trick_id')
                    ->where('dribblers.user_id', $user->id);
            })
            ->groupBy('tricks.trick_id')
            ->orderBy('score', 'desc')
            ->get();

        return $this->responseSuccess(['tag' => $tags, 'category' => $categories, 'trick' => $tricks]);
    }

    public function getOtherUserProfile($user_id)
    {
        $me = JWTAuth::parseToken()->authenticate();
        // $user = DB::table('users')
        //     ->select('following_count', 'follower_count', 'video_count', 'overall_ranking', 'dribble_score', 'dribble_medal', 'trick_completion_count', 'id',
        //         DB::raw('EXISTS(select * from follows where follower_id = '. $user_id .' AND user_id  = '. $me->id .' AND follow_status = 1) as isFollowing'))
        //     ->where('id', $user_id)
        //     ->first();
        $user = DB::table('users')
            ->select('users.*', DB::raw('EXISTS(select * from follows where follower_id = '. $user_id .' AND user_id  = '. $me->id .' AND follow_status = 1) as isFollowing'))
            ->where('id', $user_id)
            ->first();
        $this->updateProfile(User::where('id', $user_id)->first());
        return $this->responseSuccess($user);
    }

    public function get_users($user_id)
    {
        $me = JWTAuth::parseToken()->authenticate();
        $size = Input::get('per_page');
        if (empty($size)) {
            $size = 100;
        }
        $search_query = Input::get('query');
       
        $users = User::where('users.name', 'like', '%'.$search_query.'%')
            ->select('*', DB::raw('EXISTS(SELECT * FROM follows WHERE follows.user_id = users.id AND follows.follower_id = '.$me->id.') as isFollowing'))
            ->orderBy('users.created_at', 'desc')
            ->paginate($size);
        return $this->responseSuccess($users);
    }
    
    public function follow_user($user_id)
    {
        $follow_status = Input::get('follow_status');

        $validator = Validator::make([
            'follow_status' => $follow_status
        ], [
            'follow_status' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->responseValidationError($validator);
        }

        $me = JWTAuth::parseToken()->authenticate();
        $follower = User::findOrFail($user_id);

        $follow = Follow::where('user_id', $me->id)
            ->where('follower_id', $user_id)
            ->first();
        if (empty($follow)) {
            $follow = new Follow();
        }

        $follow->user_id = $me->id;
        $follow->follower_id = $follower->id;
        $follow->follow_status = $follow_status;
        $follow->save();

        $me->following_count = Follow::where('user_id', $me->id)->where('follow_status', true)->count();
        $me->save();
        $follower->follower_count = Follow::where('follower_id', $user_id)->where('follow_status', true)->count();
        $follower->save();

        return $this->responseSuccess($follow);
    }

    public function get_following_list($user_id)
    {
        $user = User::findOrFail($user_id);
        $inputs = Input::all();
        $pageSize = Input::get('per_page');

        if (empty($pageSize)) {
            $pageSize = 100;
        }

        $followers = DB::table('follows')
            ->join('users', 'users.id', '=', 'follows.follower_id')
            ->select('first_name', 'last_name', 'photo', 'id')
            ->where('follow_status', true)
            ->where('follows.user_id', $user->id)
            ->orderBy('follows.created_at', 'desc')
            ->paginate($pageSize)
            ->appends($inputs);
        return $this->responseSuccess($followers);
    }
    public function get_follower_list($user_id)
    {
        $user = User::findOrFail($user_id);
        $inputs = Input::all();
        $pageSize = Input::get('per_page');

        if (empty($pageSize)) {
            $pageSize = 100;
        }

        $followers = DB::table('follows')
            ->join('users', 'users.id', '=', 'follows.user_id')
            ->select('first_name', 'last_name', 'photo', 'id', DB::raw('EXISTS(SELECT * FROM follows WHERE follows.follower_id = users.id AND follows.follow_status = 1 AND follows.user_id = ' . $user->id . ') as follow_status'))
            ->where('follow_status', true)
            ->where('follows.follower_id', $user->id)
            ->orderBy('follows.created_at', 'desc')
            ->paginate($pageSize)
            ->appends($inputs);

        return $this->responseSuccess($followers);
    }

    public function get_video_followers($video_id)
    {

        $followers = DB::table('video_likes')
            ->select('id', 'first_name', 'last_name', 'photo')
            ->leftJoin('users', 'users.id', 'video_likes.user_id')
            ->where('video_id', $video_id)
            ->get();

        return $this->responseSuccess($followers);
    }

    /*************************************************************
     * Categories and Videos
     * ***********************************************************
     */

    public function get_categories()
    {
        $categories = Category::select('*', DB::raw('EXISTS(SELECT * FROM user_category WHERE categories.category_id = user_category.category_id) as unlocked'))
            ->where('categories.active', '1')
            ->get();

        return $this->responseSuccess($categories);
    }
    public function unlock_category($category_id) {
        $user = JWTAuth::parseToken()->authenticate();
        //add subscribed user
        $user->subscribe = 1;
        $user->save();

        //add transaction
        $category = Category::findOrFail($category_id);
        //check previous history
        $histories= Transaction::where('user_id', $user->id)->where('category_id', $category_id)->get();
        if (count($histories) == 0) {
            $transaction = new Transaction();
            $transaction->category_id = $category_id;
            $transaction->user_id = $user->id;
            $transaction->value = $category->price;
            $transaction->ref_id = 0;
            $transaction->save();
        }

        //add unlocked category
        $unlocked = User_Category::where('user_id', $user->id)->where('category_id', $category_id)->get();
        if (count($unlocked) == 0) {
            $user_cateogry = new User_Category();
            $user_cateogry->user_id = $user->id;
            $user_cateogry->category_id = $category_id;
            $user_cateogry->type = 0;//payment, 1: unlock rule
            $user_cateogry->save();

        }

        return $this->responseSuccess();
    }
    public function get_tags()
    {
        $tags = Tag::get();

        return $this->responseSuccess($tags);
    }


    /*************************************************************
     * Tricks
     * ***********************************************************
     */

    public function get_tricks_by_category($category_id) {
        $tricks = Trick::where('category_id', $category_id)
            ->where('active', '1')
            ->get();
        foreach ($tricks as $trick) {
            $tags = Tag::leftJoin('trick_tag', 'trick_tag.tag_id', 'tags.tag_id')
                ->where('trick_tag.trick_id', $trick->trick_id)
                ->pluck('tags.tag_name')
                ->toArray();
            $trick->trick_tags = implode(',', $tags);
        }
        return $this->responseSuccess($tricks);
    }

    public function get_trick_statistics($trick_id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $try_on = DB::table('dribblers')
            ->select('try_on', 'trick_id', 'dribbler_id', 'created_at')
            ->where('user_id', $user->id)
            ->where('trick_id', $trick_id)
            ->orderBy('try_on', 'asc')
            ->limit(10)
            ->get();

        return $this->responseSuccess($try_on);
    }

    public function get_trick_users($trick_id)
    {
        $inputs = Input::all();
        $pageSize = Input::get('per_page');

        if (empty($pageSize)) {
            $pageSize = 60;
        }

        $dribblers = DB::table('dribblers')
            ->join('users', 'users.id', '=', 'dribblers.user_id')
            ->select('first_name', 'last_name', 'photo', 'id', 'following_count', 'follower_count', 'video_count', 'overall_ranking', 'dribble_score', 'trick_completion_count', 'dribble_medal')
            ->where('trick_id', $trick_id)
            ->orderBy('users.dribble_score', 'desc')
            ->distinct()
            ->paginate($pageSize)
            ->appends($inputs);

        return $this->responseSuccess($dribblers);
    }

    public function get_trick_videos($trick_id)
    {
        $inputs = Input::all();
        $filter = Input::get('type');
        $pageSize = Input::get('per_page');
        $user = JWTAuth::parseToken()->authenticate();

        if (empty($pageSize)) {
            $pageSize = 60;
        }

        if ($filter == "me") {
            $videos = Video::with(['user' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'photo');
            }])
                ->select('*', DB::raw('EXISTS(SELECT * FROM video_likes WHERE video_likes.video_id = videos.video_id AND like_type = true AND video_likes.user_id = ' . $user->id . ') as favorite'))
                ->where('trick_id', $trick_id)
                ->where('user_id', $user->id)
                ->orderBy('likes', 'desc')
                ->paginate($pageSize)
                ->appends($inputs);
        } else if ($filter == "other") {
            $videos = Video::with(['user' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'photo');
            }])
                ->select('*', DB::raw('EXISTS(SELECT * FROM video_likes WHERE video_likes.video_id = videos.video_id AND like_type = true AND video_likes.user_id = ' . $user->id . ') as favorite'))
                ->where('trick_id', $trick_id)
                ->where('user_id', '!=', $user->id)
                ->orderBy('likes', 'desc')
                ->paginate($pageSize)
                ->appends($inputs);
        }

        return $this->responseSuccess($videos);
    }


    /*************************************************************
     * Dribblers
     * ***********************************************************
     */
    public function post_dribbler()
    {
        $try_on = Input::get('try_on');
        $trick_id = Input::get('trick_id');
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make([
            'try_on' => $try_on,
            'trick_id' => $trick_id
        ], [
            'try_on' => 'required | numeric',
            'trick_id' => 'required | numeric'
        ]);

        if ($validator->fails()) {
            return $this->responseValidationError($validator);
        }

        $dribbler = New Dribbler();
        $dribbler->trick_id = $trick_id;
        $dribbler->try_on = $try_on;
        $dribbler->user_id = $user->id;
        $dribbler->save();

        $dribblers = DB::table('dribblers')
            ->select('try_on', 'trick_id', 'dribbler_id', 'created_at')
            ->where('user_id', $user->id)
            ->where('trick_id', $trick_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return $this->responseSuccess($dribblers);
    }


    /*************************************************************
     * Video
     * ***********************************************************
     */
    public function get_my_video()
    {
        $inputs = Input::all();
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = Input::get('user_id');
        $pageSize = Input::get('per_page');
        if (empty($pageSize)) {
            $pageSize = 60;
        }

        $videos = Video::where('user_id', $user_id)
            ->select('*', DB::raw('EXISTS(SELECT * FROM video_likes WHERE video_likes.video_id = videos.video_id AND like_type = true AND video_likes.user_id = ' . $user->id . ') as favorite'))
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize)
            ->appends($inputs);

        return $this->responseSuccess($videos);
    }

    public function get_video($video_id)
    {
        $me = JWTAuth::parseToken()->authenticate();
        $video = Video::where('video_id', $video_id)
            ->select('*', DB::raw('EXISTS(SELECT * FROM video_likes WHERE video_likes.video_id = videos.video_id AND like_type = true AND video_likes.user_id = ' . $me->id . ') as favorite'))
            ->first();

        return $this->responseSuccess($video);
    }

    public function post_video()
    {
        $inputs = Input::all();
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($inputs, [
            'trick_id' => 'required | numeric',
            'thumbnail' => 'file | required',
            'video' => 'file | required'
        ]);

        if ($validator->fails()) {
            return $this->responseValidationError($validator);
        }

        $video = new Video();
        $video->fillable($inputs);
        $video->user_id = $user->id;
        $video->trick_id = Input::get('trick_id');
        $video->save();

        // Upload video and thumbnail to S3
        try {
            $thumbnail = Input::file('thumbnail');
            $img = Image::make($thumbnail)->encode('png')->resize(300, 300)->stream();
            $thumbnailFileName = $video->video_id . '/thumbnail.' . $thumbnail->extension();

            $file = Input::file('video');
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

        // recount uploaded videos
        $user->video_count = Video::where('user_id', $user->id)->count();
        $user->save();

        return $this->responseSuccess($video);
    }

    public function like_video($video_id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $likeType = Input::get('type');

        $validator = Validator::make(['type' => $likeType], ['type' => 'required | boolean']);
        if ($validator->fails()) {
            return $this->responseValidationError($validator);
        }

        $video = Video::find($video_id);
        if (empty($video)) {
            return $this->responseNotFoundError("Video is not exist anymore");
        }

        $like = Video_Like::where('user_id', $user->id)->where('video_id', $video_id)->first();
        if (empty($like)) {
            $like = new Video_Like();
        }

        $like->user_id = $user->id;
        $like->video_id = $video_id;
        $like->like_type = $likeType;
        $like->save();

        $video->likes = Video_Like::where('video_id', $video_id)->where('like_type', true)->count();
        $video->save();

        return $this->responseSuccess($like);
    }

    public function view_video($video_id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $video = Video::find($video_id);
        if (empty($video)) {
            return $this->responseNotFoundError("Video is not exist anymore");
        }

        $view = Video_Like::where('user_id', $user->id)->where('video_id', $video_id)->first();
        if (empty($view)) {
            $view = new Video_Like();
            $view->view_count = 1;
            $view->video_id = $video_id;
            $view->user_id = $user->id;
        } else {
            $view->view_count += 1;
        }
        $view->save();

        $video->views = Video_Like::where('video_id', $video_id)->where('view_count', '!=', 0)->count();
        $video->save();

        return $this->responseSuccess($video);
    }


    /*************************************************************
     * Feeds
     * ***********************************************************
     */
    public function get_global_feeds()
    {
        $me = JWTAuth::parseToken()->authenticate();
        $size = Input::get('per_page');
        if (empty($size)) {
            $size = 30;
        }
        $search_query = Input::get('query');
        $tag_id_string = Input::get('tag_ids');
        $trick_ids = array();
        if (strlen($tag_id_string) > 0) {
            $tag_ids = explode(',', $tag_id_string);
            $trick_ids = DB::table('trick_tag')
                ->whereIn('tag_id', $tag_ids)
                ->pluck('trick_id');

        }
        // var_dump($trick_ids);
        $feeds = Video::join('users', function ($join) {
                $join   ->on('users.id', '=', 'videos.user_id')
                        ->select('users.photo', 'users.first_name', 'users.last_name');
            })
            ->where('users.name', 'like', '%'.$search_query.'%')
            ->whereIn('videos.trick_id', $trick_ids)
            ->select('*', DB::raw('EXISTS(SELECT * FROM video_likes WHERE video_likes.video_id = videos.video_id AND like_type = true AND video_likes.user_id = ' . $me->id . ') as favorite'))
            ->orderBy('videos.created_at', 'desc')
            ->paginate($size);
        return $this->responseSuccess($feeds);
    }

    public function get_follower_feeds()
    {
        $me = JWTAuth::parseToken()->authenticate();

        $size = Input::get('per_page');
        if (empty($size)) {
            $size = 30;
        }
        $search_query = Input::get('query');
        $tag_id_string = Input::get('tag_ids');
        $trick_ids = array();
       if (strlen($tag_id_string) > 0) {
           $tag_ids = explode(',', $tag_id_string);
           $trick_ids = DB::table('trick_tag')
               ->whereIn('tag_id', $tag_ids)
               ->pluck('trick_id');
        }
        $followers = Follow::where('user_id', $me->id)
            ->where('follow_status', true)
            ->pluck('follower_id');
        // var_dump($followers);exit;

        $feeds = Video::join('users', function ($join) {
                $join   ->on('users.id', '=', 'videos.user_id')
                        ->select('users.photo', 'users.first_name', 'users.last_name');
            })
            ->where('users.name', 'like', '%'.$search_query.'%')
            ->whereIn('videos.trick_id', $trick_ids)
            ->select('*', DB::raw('EXISTS(SELECT * FROM video_likes WHERE video_likes.video_id = videos.video_id AND like_type = true AND video_likes.user_id = ' . $me->id . ') as favorite'))
            ->whereIn('videos.user_id', $followers)
            ->orderBy('videos.created_at', 'desc')
            ->paginate($size);

        return $this->responseSuccess($feeds);
    }


    /**
     * Get and Post Comments
     */
    public function get_comments($video_id)
    {
        $inputs = Input::all();
        $pageSize = Input::get('per_page');

        $video = Video::find($video_id);
        if (empty($video)) {
            return $this->responseNotFoundError('video is not exist');
        }

        $comments = Comment::where('video_id', $video_id)
            ->with(['commentator' => function ($query) {
                $query->select('id', 'photo', 'first_name', 'last_name');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize)
            ->appends($inputs);

        return $this->responseSuccess($comments);
    }

    public function post_comment($video_id)
    {
        $message = Input::get('message');
        $me = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make([
            'message' => $message
        ], [
            'message' => 'required | min:1',
        ]);

        if ($validator->fails()) {
            return $this->responseValidationError($validator);
        }

        $video = Video::find($video_id);
        if (empty($video)) {
            return $this->responseNotFoundError('video is not exist');
        }

        $comment = new Comment();
        $comment->video_id = $video_id;
        $comment->commentator_id = $me->id;
        $comment->message = $message;
        $comment->save();

        $video->comments = DB::table('comments')
            ->where('video_id', $video_id)
            ->count();
        $video->save();

        return $this->responseSuccess(Comment::with('commentator')->where('comment_id', $comment->comment_id)->first());
    }


    /**
     * Get and Post a reply of the comment
     */
    public function get_reply()
    {

    }

    public function post_reply()
    {

    }

}
