<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FirebaseAuthController extends Controller
{
    // Define admin emails that should be in admins table
    private $adminEmails = [
        'jhastingithub@gmail.com',
        // Add more admin emails here
    ];

    // Define doctor emails
    private $doctorEmails = [
        // Add doctor emails here
    ];

    // Define receptionist emails
    private $receptionistEmails = [
        // Add receptionist emails here
    ];

    /**
     * Handle Firebase login requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'uid' => 'required|string',
                'email' => 'required|email',
                'name' => 'nullable|string|max:255',
                'role' => 'nullable|string|in:user,admin,doctor,patient,receptionist'
            ]);

            // Log the login attempt (for debugging)
            Log::info('Firebase login attempt', ['email' => $validated['email']]);

            // Check role based on email lists first
            $email = $validated['email'];

            // Check if this is an admin email (goes to admins table)
            if (in_array($email, $this->adminEmails)) {
                return $this->handleAdminLogin($validated);
            }
            
            // Check if this is a doctor email
            if (in_array($email, $this->doctorEmails)) {
                return $this->handleDoctorLogin($validated);
            }
            
            // Check if this is a receptionist email
            if (in_array($email, $this->receptionistEmails)) {
                return $this->handleReceptionistLogin($validated);
            }

            // Default to regular user (goes to users table)
            return $this->handleUserLogin($validated);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Firebase login validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Firebase login error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle regular user (patient) login - Uses users table
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleUserLogin($data)
    {
        // Find or create user in users table
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'] ?? explode('@', $data['email'])[0],
                'password' => Hash::make(Str::random(16)),
                'firebase_uid' => $data['uid'],
                'role' => 'patient', // Default role for users
                'email_verified_at' => now()
            ]
        );

        // Update firebase_uid if it changed
        if ($user->firebase_uid !== $data['uid']) {
            $user->firebase_uid = $data['uid'];
            $user->save();
        }

        // Log the user in with the default 'web' guard
        Auth::guard('web')->login($user, true);

        // Generate dashboard URL based on role
        $redirectUrl = $this->getUserDashboardUrl($user->role);

        return response()->json([
            'success' => true,
            'redirect' => $redirectUrl,
            'role' => $user->role,
            'message' => 'Login successful as ' . $user->role
        ]);
    }

    /**
     * Handle admin login - Uses admins table
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleAdminLogin($data)
    {
        // Check if admin exists in admins table
        $admin = Admin::where('email', $data['email'])->first();

        // If admin doesn't exist, create them in admins table
        if (!$admin) {
            Log::info('Creating new admin from Google Sign-In', ['email' => $data['email']]);
            
            $admin = Admin::create([
                'name' => $data['name'] ?? explode('@', $data['email'])[0],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(16)),
                'firebase_uid' => $data['uid'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            // Update firebase_uid for existing admin
            $admin->firebase_uid = $data['uid'];
            if (isset($data['name']) && empty($admin->name)) {
                $admin->name = $data['name'];
            }
            $admin->save();
        }

        // Log the admin in with the admin guard
        Auth::guard('admin')->login($admin, true);

        return response()->json([
            'success' => true,
            'redirect' => route('admin.dashboard'),
            'role' => 'admin',
            'message' => 'Admin login successful'
        ]);
    }

    /**
     * Handle doctor login - Uses doctors table
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleDoctorLogin($data)
    {
        // Check if doctor exists in doctors table
        $doctor = Doctor::where('email', $data['email'])->first();

        if (!$doctor) {
            Log::warning('Doctor login attempt with non-doctor email', ['email' => $data['email']]);
            return response()->json([
                'success' => false,
                'message' => 'Doctor account not found. Please contact support.'
            ], 404);
        }

        // Update firebase_uid
        $doctor->firebase_uid = $data['uid'];
        if (isset($data['name']) && empty($doctor->name)) {
            $doctor->name = $data['name'];
        }
        $doctor->save();

        // Log the doctor in with the doctor guard
        Auth::guard('doctor')->login($doctor, true);

        return response()->json([
            'success' => true,
            'redirect' => route('doctor.dashboard'),
            'role' => 'doctor',
            'message' => 'Doctor login successful'
        ]);
    }

    /**
     * Handle receptionist login - Uses users table with receptionist role
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleReceptionistLogin($data)
    {
        // Check if receptionist exists in users table with receptionist role
        $receptionist = User::where('email', $data['email'])
                            ->where('role', 'receptionist')
                            ->first();

        if (!$receptionist) {
            Log::warning('Receptionist login attempt with non-receptionist email', ['email' => $data['email']]);
            return response()->json([
                'success' => false,
                'message' => 'Receptionist account not found. Please contact support.'
            ], 404);
        }

        // Update firebase_uid
        $receptionist->firebase_uid = $data['uid'];
        $receptionist->save();

        // Log the receptionist in with the web guard
        Auth::guard('web')->login($receptionist, true);

        return response()->json([
            'success' => true,
            'redirect' => route('user.dashboard'),
            'role' => 'receptionist',
            'message' => 'Receptionist login successful'
        ]);
    }

    /**
     * Get dashboard URL based on user role
     *
     * @param string $role
     * @return string
     */
    private function getUserDashboardUrl($role)
    {
        switch ($role) {
            case 'admin':
                return route('admin.dashboard');
            case 'doctor':
                return route('doctor.dashboard');
            case 'receptionist':
                return route('user.dashboard');
            case 'patient':
            default:
                return route('user.dashboard');
        }
    }

    /**
     * Handle Firebase logout
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $guard = $this->getCurrentGuard();
            
            if ($guard) {
                Auth::guard($guard)->logout();
            }
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
                'redirect' => route('login')
            ]);

        } catch (\Exception $e) {
            Log::error('Logout error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the current authentication guard
     *
     * @return string|null
     */
    private function getCurrentGuard()
    {
        if (Auth::guard('admin')->check()) {
            return 'admin';
        }
        if (Auth::guard('doctor')->check()) {
            return 'doctor';
        }
        if (Auth::guard('web')->check()) {
            return 'web';
        }
        return null;
    }

    /**
     * Get current authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser()
    {
        try {
            $guard = $this->getCurrentGuard();
            
            if (!$guard) {
                return response()->json([
                    'authenticated' => false,
                    'message' => 'Not authenticated'
                ]);
            }

            $user = Auth::guard($guard)->user();
            
            return response()->json([
                'authenticated' => true,
                'guard' => $guard,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? $guard,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'authenticated' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}