<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\GameQuestion;
use App\Models\GameAnswer;
use App\Models\Company;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{

    public function forceLogout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/'); // arahkan ke halaman login/home
    }

    public function index($session) 
    {
        try {
            $user = auth()->user();

            if (!$user) {
                throw new \Exception('User belum login.');
            }

            $userId = $user->id;

            $exists = GameAnswer::where('user_id', $userId)
                                    ->exists();
            if ($exists) {
                return $this->forceLogout();
            }

            $questions = GameQuestion::where('session', $session)->get();
            $companyIds = $questions->flatMap(fn($q) => json_decode($q->options))->unique();
            $companies = Company::whereIn('id', $companyIds)->get()->keyBy('id');
            $questionIds = $questions->pluck('id')->unique();
            $backgroundImages = GameQuestion::whereIn('id', $questionIds)->pluck('image', 'id');

            Log::info("Background Images: $backgroundImages");

            return view('game.game', compact('questions', 'companies', 'session', 'backgroundImages'));
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['auth' => $e->getMessage()]);
        }

    }

    public function submit(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                throw new \Exception('User belum login.');
            }

            $userId = $user->id;
            $answers = json_decode($request->input('answers'), true);
            $sessionId = $request->input('session_id');

            if (!is_array($answers)) {
                return back()->withErrors(['answers' => 'Format answers tidak valid.']);
            }

            Log::debug('Jawaban user', [
                'user_id' => $userId,
                'answers' => $answers,
                'timestamp' => now()->toDateTimeString(),
            ]);

            foreach ($answers as $answer) {
                $questionId = $answer['question_id'] ?? null;
                $selected   = $answer['selected'] ?? [];

                if (!$questionId || !is_array($selected)) {
                    continue;
                }

                $exists = GameAnswer::where('game_question_id', $questionId)
                                    ->where('user_id', $userId)
                                    ->exists();

                if ($exists) {
                    return $this->forceLogout();
                }

                $ids     = array_column($selected, 'id');
                $reasons = array_column($selected, 'reason');

                GameAnswer::create([
                    'game_question_id' => $questionId,
                    'user_id'          => $userId,
                    'choices'          => json_encode($ids),
                    'reasons'          => json_encode($reasons),
                ]);
            }

            $nextSession = $sessionId + 1;
            $maxSession  = 3;

            Log::debug('next session is ' . $nextSession);

            if ($nextSession > $maxSession) {
                return redirect()->route('financial-literacy')
                                ->with('status', 'Sesi selesai. Terima kasih!');
            }

            return redirect()->route('game-session', ['session' => $nextSession])
                            ->with('status', 'Jawaban berhasil disimpan.');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['auth' => $e->getMessage()]);
        }
    }

}
