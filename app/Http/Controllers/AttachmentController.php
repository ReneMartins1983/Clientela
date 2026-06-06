<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function download(Attachment $attachment): StreamedResponse
    {
        abort_unless($attachment->client->user_id === auth()->id(), 403);

        return Storage::disk('local')->download($attachment->path, $attachment->name);
    }
}
