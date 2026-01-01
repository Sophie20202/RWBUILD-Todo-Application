<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ISTAfricaAuthController extends Controller
{
    /**
     * Redirect user to IST Africa Auth login page
     */
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('iaa_oauth_state', $state);
    
        $clientId = env('NEXT_PUBLIC_IAA_CLIENT_ID');
        $redirectUri = env('IST_AFRICA_REDIRECT_URI');
    
        // Use port 3000 for frontend login
        $url = "http://localhost:3000/auth/login?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
        ]);
    
        return redirect()->away($url);
    }

    /**
     * Handle callback from IST Africa Auth
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');
        $state = $request->query('state');
    
        \Log::info('Callback hit', compact('code', 'state'));
    
        if (!$code || !$state) {
            return redirect('/login')->withErrors([
                'error' => 'Invalid authentication response'
            ]);
        }
    
        // Send code as query parameter, not in body
        $tokenResponse = Http::post(
            rtrim(env('IST_AFRICA_AUTH_URL'), '/') . '/api/auth/tokens?code=' . $code,
            [
                'client_id' => env('NEXT_PUBLIC_IAA_CLIENT_ID'),
                'client_secret' => env('IAA_CLIENT_SECRET'),
            ]
        );
    
        \Log::info('Token response', [
            'status' => $tokenResponse->status(),
            'body' => $tokenResponse->json(),
        ]);
    
        if (!$tokenResponse->successful()) {
            return redirect('/login')->withErrors([
                'error' => 'Failed to obtain access token'
            ]);
        }
    
        $accessToken = $tokenResponse['access_token'];
    
        $userInfo = $this->decodeJWT($accessToken);
    
        if (!$userInfo || empty($userInfo['email'])) {
            return redirect('/login')->withErrors([
                'error' => 'Invalid user data received'
            ]);
        }
    
        $user = $this->findOrCreateUser($userInfo);
    
        Auth::login($user);
        $request->session()->regenerate();
    
        return redirect('/dashboard');
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

        // Forward to IAA
        $response = Http::post(rtrim(env('IST_AFRICA_AUTH_URL'), '/') . '/api/auth/authenticate', [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'client_id' => env('NEXT_PUBLIC_IAA_CLIENT_ID'),
            'client_secret' => env('IAA_CLIENT_SECRET'),
        ]);

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
            'id' => $payload['userId'] ?? null,
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