<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Exception;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Response Helper Methods
     */

    protected function responseSuccess($response = [])
    {
        // $response['result'] = 'success';
        return response()->json($response);
    }

    protected function responseFail($errorMessage)
    {
        $response = [
            'result' => 'fail',
            'message' => $errorMessage,
        ];
        return response()->json($response)->setStatusCode(200);
    }

    protected function responseError($errorCode = 200, $errorMessage)
    {
        $response = [
            'message' => $errorMessage,
            'error' => $errorMessage,
        ];
        return response()->json($response)->setStatusCode($errorCode);
    }

    protected function responseBadRequestError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('400');
    }

    protected function responseUnauthorizedError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('401');
    }

    protected function responseAccessDeniedError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('403');
    }

    protected function responseNotFoundError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('404');
    }

    protected function responseMethodNotAllowedError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('405');
    }

    protected function responseNotAcceptableError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('406');
    }

    protected function responseConflictError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('409');
    }

    protected function responseGoneError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('410');
    }

    protected function responseLengthRequiredError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('411');
    }

    protected function responsePreconditionFailedError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('412');
    }

    protected function responseUnsupportedMediaTypeError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('415');
    }

    protected function responseValidationError($validator)
    {
        $response = [
            'error' => $validator->errors()->all(),
        ];
        return response()->json($response)->setStatusCode('422');
    }

    protected function responsePreconditionRequiredError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('428');
    }

    protected function responseTooManyRequestsError($message)
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('429');
    }

    protected function responseInternalServerError($message = 'Internal Server Error')
    {
        $response = [
            'error' => $message,
        ];
        return response()->json($response)->setStatusCode('500');
    }


    /**
     * Image Process Helper
     */

    protected $avatarPath = "/upload/avatars/";

    protected function resizeImage($file, $width, $height)
    {
        $resizedImg = Image::make($file)->resize($width, $height, function ($img) {
            $img->aspectRatio();
            $img->upsize();
        });

        return $resizedImg;
    }

    protected function getAvatarFilePath($fileName)
    {
        return public_path() . $this->avatarPath . $fileName;
    }

    /**
     * Email Helper Methods
     */
    protected function sendVerificationEmail($email, $subject, $confirmation_code)
    {
        try {
            Mail::send('emails.verification', compact('email', 'confirmation_code'), function ($message) use ($email, $subject) {
                $message->from('support@dribbler.com')
                    ->to($email)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
        }
    }

    protected function sendEmail($email, $subject, $content)
    {
        try {
            Mail::send('emails.common_email', compact('email', 'content'), function ($message) use ($email, $subject) {
                $message->from('support@dribbler.com')
                    ->to($email)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
        }
    }
}
