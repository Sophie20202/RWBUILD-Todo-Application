<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ISTAfricaAuthController extends Controller
{
    /**
     * Redirect user to IST Africa Auth login page
     */
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('iaa_oauth_state', $state);
    
        // Build the URL with query parameters
        $params = http_build_query([
            'client_id' => config('services.ist_africa.client_id'),
            'redirect_uri' => config('services.ist_africa.redirect'),
            'response_type' => 'code',
            'state' => $state,
        ]);
    
        $url = config('services.ist_africa.auth_url') . '?' . $params;
    
        return redirect()->away($url);
    }

    /**
     * Handle callback from IST Africa Auth
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');
        $state = $request->query('state');
        $sessionState = $request->session()->get('iaa_oauth_state');
    
        Log::info('Callback hit', compact('code', 'state'));
    
        // Validate state to prevent CSRF
        if (!$code || !$state || $state !== $sessionState) {
            return redirect('/login')->withErrors([
                'error' => 'Invalid authentication response'
            ]);
        }

        // Remove used state
        $request->session()->forget('iaa_oauth_state');
    
        // Exchange authorization code for access token
        $tokenResponse = Http::asForm()->post(
            config('services.ist_africa.token_url'),
            [
                'grant_type' => 'authorization_code',
                'client_id' => config('services.ist_africa.client_id'),
                'client_secret' => config('services.ist_africa.client_secret'),
                'code' => $code,
                'redirect_uri' => config('services.ist_africa.redirect'),
            ]
        );
    
        Log::info('Token response', [
            'status' => $tokenResponse->status(),
            'body' => $tokenResponse->json(),
        ]);
    
        if (!$tokenResponse->successful()) {
            return redirect('/login')->withErrors([
                'error' => 'Failed to obtain access token: ' . ($tokenResponse->json()['message'] ?? 'Unknown error')
            ]);
        }

        $tokenData = $tokenResponse->json();
        $accessToken = $tokenData['access_token'] ?? null;

        if (!$accessToken) {
            return redirect('/login')->withErrors([
                'error' => 'No access token received'
            ]);
        }
    
        // Decode JWT to get user info
        $userInfo = $this->decodeJWT($accessToken);
    
        if (!$userInfo || empty($userInfo['email'])) {
            return redirect('/login')->withErrors([
                'error' => 'Invalid user data received'
            ]);
        }
    
        $user = $this->findOrCreateUser($userInfo);
    
        Auth::login($user);
        $request->session()->regenerate();
    
        return redirect()->route('dashboard');
    }
    
    /**
     * API endpoint to authenticate via IAA (for Next.js frontend)
     */
    public function apiAuthenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        $response = Http::post(
            config('services.ist_africa.auth_url') . '/api/auth/authenticate',
            [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'client_id' => config('services.ist_africa.client_id'),
                'client_secret' => config('services.ist_africa.client_secret'),
                // ADD THIS LINE:
                'redirect_uri' => config('services.ist_africa.redirect'), 
            ]
        );
    
        return response()->json($response->json(), $response->status());
    }
    /**
     * Decode JWT token to extract user info
     */
    private function decodeJWT(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
        
        return [
            'id' => $payload['sub'] ?? $payload['userId'] ?? null,
            'email' => $payload['email'] ?? null,
            'name' => $payload['name'] ?? null,
            'role' => $payload['role'] ?? 'user',
        ];
    }
    
    /**
     * Find or create local user from IAA data
     */
    private function findOrCreateUser(array $istAfricaUser): User
    {
        $user = User::where('client_id', $istAfricaUser['id'] ?? null)
            ->orWhere('email', $istAfricaUser['email'] ?? null)
            ->first();
    
        if ($user) {
            $user->update([
                'client_id' => $istAfricaUser['id'] ?? $user->client_id,
                'name' => $istAfricaUser['name'] ?? $user->name,
                'role' => $istAfricaUser['role'] ?? $user->role,
                'email_verified_at' => now(),
            ]);
        } else {
            $user = User::create([
                'client_id' => $istAfricaUser['id'] ?? null,
                'name' => $istAfricaUser['name'] ?? 'IST Africa User',
                'email' => $istAfricaUser['email'],
                'role' => $istAfricaUser['role'] ?? 'user',
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);
        }
    
        return $user;
    }
}