<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LiteracyQuestion;
use App\Models\LiteracyAnswer; 

class LiteracyController extends Controller
{

    public function forceLogout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/'); // arahkan ke halaman login/home
    }

    public function index() 
    {
        try {
            $user = auth()->user();

            if (!$user) {
                throw new \Exception('User belum login.');
            }

            $userId = $user->id;

            $exists = LiteracyAnswer::where('user_id', $userId)
                                    ->exists();

            if ($exists) {
                return $this->forceLogout();
            }

            $questions = LiteracyQuestion::all();
            return view('financial.financial-literacy', compact('questions'));
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['auth' => $e->getMessage()]);
        }

    }

    public function submit(Request $request) {
        try {
            $user = auth()->user();

            if (!$user) {
                throw new \Exception('User belum login.');
            }

            $userId = $user->id;

            $answeredIds = LiteracyAnswer::where('user_id', $userId)->pluck('literacy_question_id')->toArray();

            $answers = json_decode($request->input('answers'), true);

            Log::debug('Jawaban user', [
                'user_id' => auth()->id(),
                'answers' => $answers,
                'timestamp' => now()->toDateTimeString(),
            ]);

            foreach ($answers as $questionId => $content) {

                if (in_array($questionId, $answeredIds)) {
                    continue;
                }

                $exists = LiteracyAnswer::where('literacy_question_id', $questionId)
                                    ->where('user_id', $userId)
                                    ->exists();

                if ($exists) {
                    return $this->forceLogout();
                }

                LiteracyAnswer::create([
                    'literacy_question_id' => $questionId,
                    'user_id' => $userId,
                    'content' => $content,
                ]);
            }

            return redirect()->route('spiritual');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['auth' => $e->getMessage()]);
        }
    }
}
