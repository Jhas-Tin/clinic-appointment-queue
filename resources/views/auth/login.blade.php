<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Appointment System - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Firebase SDK -->
    <script type="module" src="https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js"></script>
    <script type="module" src="https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js"></script>
    <script type="module" src="https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="h-screen bg-cover bg-center bg-no-repeat"
      style='background-image: url("https://www.shutterstock.com/image-vector/online-doctor-appointment-booking-system-600nw-2655423645.jpg")'>

<div class="w-full h-full bg-blue-900/30 flex flex-col">

    <!-- HEADER -->
    <header class="flex items-center justify-center py-7 px-6 relative">
        <div class="flex items-center gap-3 text-white text-3xl font-semibold drop-shadow-lg">
            <span>🏥</span>
            <span>Clinic Appointment System</span>
        </div>
        <a href="{{ route('register.form') }}"
           class="absolute right-6 top-6 bg-white text-blue-600 px-5 py-2 rounded-lg font-semibold shadow-md hover:bg-gray-100 transition">
            Register
        </a>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex flex-1 items-center justify-center px-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl px-8 py-10">
            <h2 class="text-3xl font-bold text-blue-600 mb-6 text-center">
                Login
            </h2>

            <!-- Laravel Validation Errors -->
            @if ($errors->any())
                <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Session Status -->
            @if (session('status'))
                <div class="bg-green-100 text-green-600 p-3 rounded mb-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Firebase Error Messages -->
            <div id="errorContainer" class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm hidden"></div>
            
            <!-- Success Messages -->
            <div id="successContainer" class="bg-green-100 text-green-600 p-3 rounded mb-4 text-sm hidden"></div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="hidden text-center mb-4">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>

            <!-- Laravel Traditional Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="text-sm text-gray-600 block mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400 text-lg">📧</span>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Enter your email"
                               required
                               autofocus
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-500 @enderror">
                    </div>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-sm text-gray-600 block mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400 text-lg">🔒</span>
                        <input type="password"
                               name="password"
                               placeholder="Enter your password"
                               required
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('password') border-red-500 @enderror">
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600" {{ old('remember') ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Laravel Login Button -->
                <button type="submit"
                        class="w-full mt-3 py-3 bg-gradient-to-r from-blue-600 to-blue-400 text-white rounded-lg font-semibold shadow-md hover:opacity-90 transition">
                    Log In with Email
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                </div>
            </div>

            <!-- Firebase Google Sign In -->
            <button type="button"
                    id="googleSignIn"
                    class="w-full py-3 bg-white border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition flex items-center justify-center gap-2 mb-4">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Sign in with Google
            </button>

            

            <!-- Register Link -->
            {{-- <p class="mt-6 text-center text-sm text-gray-600">
                Don't have an account?
                <a href="{{ route('register.form') }}" class="text-blue-600 hover:underline font-medium">Sign up</a>
            </p> --}}
        </div>
    </main>
</div>

<script type="module">
    // Import Firebase modules
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
    import { 
        getAuth, 
        signInWithPopup,
        GoogleAuthProvider
    } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

    // Your Firebase configuration
    const firebaseConfig = {
        apiKey: "AIzaSyDNCzEIcI_pAV5UBmRZ4ZVLKmMrqSKn0jM",
        authDomain: "clinic-afe03.firebaseapp.com",
        projectId: "clinic-afe03",
        storageBucket: "clinic-afe03.firebasestorage.app",
        messagingSenderId: "1057714082265",
        appId: "1:1057714082265:web:d5c647f5d96bb54be9c9c8",
        measurementId: "G-P6KM0RX5WX"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const provider = new GoogleAuthProvider();

    // Configure Google provider
    provider.setCustomParameters({
        prompt: 'select_account'
    });

    // DOM Elements
    const googleSignInBtn = document.getElementById('googleSignIn');
    const errorContainer = document.getElementById('errorContainer');
    const successContainer = document.getElementById('successContainer');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const loginForm = document.querySelector('form[method="POST"]');

    // Helper Functions
    function showError(message) {
        errorContainer.innerHTML = message;
        errorContainer.classList.remove('hidden');
        successContainer.classList.add('hidden');
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            errorContainer.classList.add('hidden');
        }, 5000);
    }

    function showSuccess(message) {
        successContainer.innerHTML = message;
        successContainer.classList.remove('hidden');
        errorContainer.classList.add('hidden');
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            successContainer.classList.add('hidden');
        }, 3000);
    }

    function setLoading(isLoading) {
        if (isLoading) {
            loadingSpinner.classList.remove('hidden');
            googleSignInBtn.disabled = true;
            googleSignInBtn.classList.add('opacity-50', 'cursor-not-allowed');
            
            // Also disable the Laravel form submit button if it exists
            const laravelSubmitBtn = loginForm?.querySelector('button[type="submit"]');
            if (laravelSubmitBtn) {
                laravelSubmitBtn.disabled = true;
                laravelSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        } else {
            loadingSpinner.classList.add('hidden');
            googleSignInBtn.disabled = false;
            googleSignInBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            
            // Re-enable Laravel form submit button
            const laravelSubmitBtn = loginForm?.querySelector('button[type="submit"]');
            if (laravelSubmitBtn) {
                laravelSubmitBtn.disabled = false;
                laravelSubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    // Google Sign In Handler
    googleSignInBtn.addEventListener('click', async () => {
        setLoading(true);
        
        try {
            // Sign in with Google popup
            const result = await signInWithPopup(auth, provider);
            const user = result.user;
            
            // Get the ID token
            const idToken = await user.getIdToken();
            
            // Send Firebase user data to Laravel - REMOVED photo field
            const response = await fetch('{{ route("firebase.login") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    uid: user.uid,
                    email: user.email,
                    name: user.displayName,
                    role: 'user' 
                })
            });

            const data = await response.json();
            
            if (data.success) {
                showSuccess('Login successful! Redirecting...');
                
                // Redirect to the appropriate dashboard
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                showError(data.message || 'Login failed. Please try again.');
                
                // Sign out from Firebase if Laravel login failed
                await auth.signOut();
            }

        } catch (error) {
            console.error('Google sign-in error:', error);
            
            // Handle specific Firebase errors
            switch (error.code) {
                case 'auth/popup-closed-by-user':
                    showError('Sign-in cancelled. Please try again.');
                    break;
                case 'auth/popup-blocked':
                    showError('Pop-up was blocked by your browser. Please allow pop-ups and try again.');
                    break;
                case 'auth/cancelled-popup-request':
                    // Ignore - this is just a duplicate request
                    break;
                default:
                    showError('Google sign-in failed. Please try again or use email login.');
            }
        } finally {
            setLoading(false);
        }
    });

    // Optional: Add email/password login with Firebase
    // You can uncomment this if you want Firebase email/password login
    /*
    const firebaseEmailForm = document.createElement('form');
    firebaseEmailForm.id = 'firebaseEmailForm';
    firebaseEmailForm.className = 'space-y-5 hidden';
    // Add Firebase email/password form fields here
    */
</script>

<!-- Add this for browsers that don't support ES6 modules -->
<script nomodule>
    alert('Your browser does not support ES6 modules. Please update your browser to use this application.');
</script>
</body>
</html>