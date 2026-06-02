<?php

namespace App\Http\Controllers;

use App\Models\MessageFeedback;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class MessageFeedbackController extends Controller
{
    public function toggle(Request $request, ChatMessage $message)
    {
        if ($message->sender !== 'ai') {
            return back()->with('error', 'Hanya bisa memberi feedback pada pesan AI.');
        }

        $existing = MessageFeedback::where('chat_message_id', $message->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            if ($existing->feedback === $request->feedback) {
                $existing->delete();
                return back()->with('success', 'Feedback dihapus.');
            }
            $existing->update(['feedback' => $request->feedback]);
        } else {
            MessageFeedback::create([
                'chat_message_id' => $message->id,
                'user_id' => auth()->id(),
                'feedback' => $request->feedback,
            ]);
        }

        return back()->with('success', 'Terima kasih atas feedbacknya!');
    }
}
