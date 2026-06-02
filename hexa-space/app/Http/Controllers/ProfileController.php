<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\CounselingSession;
use App\Models\DailyJournal;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $data = ['user' => $user];

        if ($user->role === 'user') {
            $data['totalSessions'] = CounselingSession::where('user_id', $user->id)->count();
            $data['activeSessions'] = CounselingSession::where('user_id', $user->id)->where('status', 'active')->count();
            $data['totalJournals'] = DailyJournal::where('user_id', $user->id)->count();

            $streak = 0;
            $checkDate = Carbon::today();
            while (DailyJournal::where('user_id', $user->id)->whereDate('created_at', $checkDate)->exists()) {
                $streak++;
                $checkDate->subDay();
            }
            $data['streak'] = $streak;
        }

        return view('profile.edit', $data);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        if ($request->hasFile('avatar')) {
            $request->validate(['avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048']);

            if ($request->user()->avatar) {
                Storage::disk('public')->delete($request->user()->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $request->user()->update(['avatar' => $path]);

            return Redirect::route('profile.edit')->with('success', 'Foto profil berhasil diperbarui.');
        }

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('success', 'Profile berhasil diperbarui.');
    }

    public function removeAvatar(Request $request): RedirectResponse
    {
        if ($request->user()->avatar) {
            Storage::disk('public')->delete($request->user()->avatar);
            $request->user()->update(['avatar' => null]);
        }

        return Redirect::route('profile.edit')->with('success', 'Foto profil berhasil dihapus.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
