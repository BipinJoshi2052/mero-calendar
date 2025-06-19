<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
// use Illuminate\Http\Client\Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\SendOTP;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $otp = rand(100000, 999999);  // Generate OTP
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Send OTP via email
        Mail::to($user->email)->send(new SendOTP($otp));
        
        // Store email in session for later use in the OTP verification page
        Session::put('email', $user->email);

        // Store OTP temporarily (you can use session or a new column in the User model)
        $user->otp = $otp;
        $user->save();

        return redirect()->route('otp.verify');
    }
    public function showOtpVerificationForm()
    {
        if (!Session::has('email')) {
            // Redirect to home page if not in the session
            return redirect('/');
        }
        return view('auth.verifyOtp');
    }

    public function verifyOtp(Request $request)
    {
        // Check if the email is stored in the session
        if (!Session::has('email')) {
            // Redirect to home page if not in the session
            return redirect('/');
        }
        // Get the stored email from the session
        $email = Session::get('email');

        // Find the user by email
        $user = User::where('email', $email)->first();

        // Check if the user exists and OTP matches
        if ($user && $request->otp == $user->otp) {
            // OTP is correct, mark the email as verified
            $user->email_verified_at = now();
            $user->otp = null;  // Clear OTP after verification
            $user->save();

            // Log the user in
            Auth::login($user);

            // Remove the email from the session after login
            Session::forget('email');

            // Redirect the user to the dashboard
            return redirect()->route('home');
        }

        // OTP is incorrect
        return back()->withErrors(['otp' => 'The OTP is incorrect.']);
    }
}
