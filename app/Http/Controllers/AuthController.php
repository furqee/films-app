<?php

namespace App\Http\Controllers;

use App\Mail\UserCreated;
use App\Models\User;
use App\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Register user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request) :JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first()
                ],
                403
            );
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);

        if ($user->save()) {
            $userVerification = new UserVerification;
            $userVerification->user_id = $user->id;
            $userVerification->uuid = Str::orderedUuid();
            $userVerification->expired_at = now()->addHour()->timestamp;
            $userVerification->save();
            Mail::to($user)->send(new UserCreated($user));
            return response()->json([
                'message' => 'You have been signed up successfully.'
            ]);
        }

        return response()->json(
            [
                'message' => 'Something went wrong. Contact administrator.'
            ],
            404
        );
    }

    /**
     * Register user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request) :JsonResponse
    {
        if ($request->user()) {
            return response()->json([
                'user' => $request->user()
            ]);
        }

        return response()->json([
            'message' => 'User not found.'
        ], 404);
    }

    /**
     * Login user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request) :JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first()
                ],
                403
            );
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if ($request->user()->email_verified_at) {
                $data = [
                    'token' =>  $request->user()->createToken(config('app.name'))->plainTextToken,
                    'user' =>  $request->user(),
                    'message' =>  'login successfully.'
                ];

                return response()->json($data);
            }
            return response()->json(
                [
                    'message' => 'Please verify your email.'
                ],
                403
            );
        }

        return response()->json(
            [
                'message' => 'login failed.'
            ],
            403
        );
    }

    /**
     * Log out user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request) :JsonResponse
    {
        if ($request->user()->tokens()->delete()) {
            Auth::guard('web')->logout();
            return response()->json(['message' => 'Log out successfully.']);
        }

        return response()->json(['message' => 'Log out error.'], 400);
    }

    /**
     * Log out user.
     *
     * @param int $id
     * @param string $uuid
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function verify(int $id, string $uuid)
    {
        $validator = Validator::make([
            'user_id' => $id,
            'uuid' => $uuid,
        ], [
            'user_id' => [
                'required',
                Rule::exists('user_verifications')->where(function ($query) use ($uuid) {
                    return $query->where('uuid', $uuid);
                        //->where('expired_at', '>=', now()->timestamp);
                })
            ]
        ]);

        if ($validator->fails()) {
            return abort(404);
        }

        $user = User::find($id);
        $user->email_verified_at = now()->format('Y-m-d H:i:s');

        if ($user->save()) {
            $userVerification = UserVerification::where('user_id', $id)->first();
            $userVerification->delete();

            return redirect(config('app.url').'/login');
        }
    }
}
