<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;

class PasswordResetApiController extends Controller
{
    // ✅ Envoi du lien de réinitialisation
    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(64);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // ⚠️ À ce stade tu peux envoyer un email réel avec le token, ou juste le retourner
        // Pour l'instant on retourne le token (utile en mode test)
        return response()->json([
            'message' => 'Lien de réinitialisation généré.',
            'token' => $token, // ⚠️ à envoyer par mail en prod
            'email' => $request->email
        ]);
    }

    // ✅ Réinitialisation avec le token
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $reset = DB::table('password_resets')->where('email', $request->email)->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return response()->json(['message' => 'Token invalide'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Supprime le token utilisé
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Mot de passe réinitialisé avec succès']);
    }
}
