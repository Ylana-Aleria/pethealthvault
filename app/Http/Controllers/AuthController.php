<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pet;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Vaccination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

   
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if ($user->role_id == 1) {
                return redirect()->intended('admin');
            } elseif ($user->role_id == 2) {
                return redirect()->route('owner.dashboard');
            }

            return abort(403, 'Unauthorized access.');
        }

        return back()->withErrors([
            'password' => 'The password you entered is incorrect.',
        ])->withInput();
    }

    public function index()
    {
        $userId = Auth::id();
        $pets = Pet::with([
            'diagnosis.medication',  
            'vaccination',
            'appointment',

        ])
            ->where('UserID', $userId)
            ->get();

        foreach ($pets as $pet) {
            $pet->latestAppointment = Appointment::where('PetID', $pet->id)
                ->latest('AppointmentDate')
                ->first();

            $pet->latestVaccination = Vaccination::where('PetID', $pet->id)
                ->latest('RecordDate')
                ->first();

            $pet->latestDiagnosis = $pet->diagnosis()->latest('RecordDate')->first();
        }

        return view('owner.dashboard', compact('pets'));
    }


    /* FORGOT PASSWORD */
    function forgotPassword()
    {
        return view("auth.forgot-password");
    }

    function forgotPasswordPost(Request $request)
    {

        $request->validate([
            'email' => "required|email|exists:users",
        ]);

        $token = Str::random(length: 64);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        try {
            Mail::send("emails.forgot-password-email", ['token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject("Reset Password");
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Mail sending failed: ' . $e->getMessage());
        }


        return redirect()->to(route("forgot.password"))
            ->with("success", "We have send an email to reset password.");
    }

    function resetPassword($token)
    {
        return view("auth.new-password", compact('token'));
    }

    function resetPasswordPost(Request $request)
    {
        $request->validate([
            "email" => "required|email|exists:users",
            "password" => "required|string|min:6|confirmed",
            "password_confirmation" => "required"
        ]);

        $updatePassword = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$updatePassword) {
            return redirect()->to(route("reset.password", ['token' => $request->token]))
                ->with("error", "Invalid or expired token");
        }


        User::where("email", $request->email)
            ->update(["password" => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->to(route("login"))
            ->with("success", "Password reset successful");
    }


     public function store(Request $request)
    {
        $request->validate([
            'OwnerEmail' => 'required|email|exists:users,email',
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'AppointmentDate' => 'required|date',
            'AppointmentTime' => 'required',
            'Description' => 'nullable|string',
        ]);

        $appointment = new Appointment();
        $appointment->FirstName = $request->FirstName;
        $appointment->LastName = $request->LastName;
        $appointment->OwnerEmail = $request->OwnerEmail;
        $appointment->AppointmentDate = $request->AppointmentDate;
        $appointment->AppointmentTime = $request->AppointmentTime;
        $appointment->Description = $request->Description;
        $appointment->Status = 'Pending';
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment scheduled successfully.')->withFragment('appointments-' . $request->PetID);
    }


    /* CHANGE PASSWORD */
    public function changePassword()
    {
        return view('owner.change-password');
    }

    public function changePasswordUpdate(Request $request)
    {
        $request->validate([
            "old_password" => "required|string",
            "new_password" => "required|string|min:6|confirmed",
            "new_password_confirmation" => "required"
        ]);

        if (!Hash::check($request->old_password, Auth::user()->password)) {
            return redirect()->route('change.password')
                ->withErrors(['old_password' => 'Old password is incorrect.']);
        }

        if ($request->new_password !== $request->new_password_confirmation) {
            return redirect()->route('change.password')
                ->withErrors(['new_password' => 'New password and confirmation do not match.']);
        }

        if (strlen($request->new_password) < 6) {
            return redirect()->route('change.password')
                ->withErrors(['new_password' => 'New password must be at least 6 characters long.']);
        }

        try {
            $user = User::find(Auth::id());
            $user->password = Hash::make($request->new_password);
            $user->save();
        } catch (\Exception $e) {
            return redirect()->route('change.password')
                ->withErrors(['general' => 'Failed to update password. Please try again.']);
        }

        return redirect()->route('owner.dashboard')
            ->with('success', 'Password changed successfully.');
    }


    /* LOGOUT */
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
