<?php

namespace App\Http\Controllers\Spiritual;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SpiritualQuestion;
use App\Models\SpiritualAnswer;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SpiritualController extends Controller
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

            $exists = SpiritualAnswer::where('user_id', $userId)
                                    ->exists();

            if ($exists) {
                return $this->forceLogout();
            }

            $questions = SpiritualQuestion::all();
            return view('spiritual.spiritual', compact('questions'));
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

            $bank_account_name = $request->input('bank_account_name');

            $bank_account_number = $request->input('bank_account_number');

            $answeredIds = SpiritualAnswer::where('user_id', $userId)->pluck('spiritual_question_id')->toArray();

            $scores = json_decode($request->input('scores'), true);


            if (!is_array($scores)) {
                return back()->withErrors(['scores' => 'Format scores tidak valid.']);
            }

            $invalid = collect($scores)->filter(function ($score) {
                return !is_numeric($score) || $score < 1 || $score > 4;
            });

            if ($invalid->isNotEmpty()) {
                return back()->withErrors(['scores' => 'Terdapat nilai di luar skala 1 sampai 4.']);
            }

            Log::debug('Jawaban user', [
                'user_id' => $userId,
                'scores' => $scores,
                'bank_account_number' => $bank_account_number,
                'bank_account_name' => $bank_account_name,
                'timestamp' => now()->toDateTimeString(),
            ]);

            foreach ($scores as $questionId => $score) {
                if (in_array($questionId, $answeredIds)) {
                    continue;
                }

                $exists = SpiritualAnswer::where('spiritual_question_id', $questionId)
                                    ->where('user_id', $userId)
                                    ->exists();

                if ($exists) {
                    return $this->forceLogout();
                }

                SpiritualAnswer::create([
                    'spiritual_question_id' => $questionId,
                    'user_id' => $userId,
                    'score' => $score,
                ]);
            }

            $user = User::find($userId); 
            
            if (!$user) { 
                throw new \Exception("Something went wrong!"); 
            } 
            
            $user->bank_account_number = $bank_account_number;

            $user->bank_account_name = $bank_account_name;
            
            $user->save();

            return $this->forceLogout();

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['auth' => $e->getMessage()]);
        }
    }
}
