<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\AgentDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AgentDocumentController extends Controller
{
    private const ADMIN_POSITIONS = ['developer', 'administrator', 'author'];

    public function download(Agent $agent, AgentDocument $agentDocument): BinaryFileResponse
    {
        abort_unless(in_array(Auth::user()?->position, self::ADMIN_POSITIONS, true), 403);
        abort_unless($agentDocument->agent_id === $agent->id, 404);
        abort_unless(Str::startsWith($agentDocument->storage_path, 'agent-applications/'), 404);

        $disk = Storage::disk('private');
        abort_unless($disk->exists($agentDocument->storage_path), 404);

        return response()->download(
            $disk->path($agentDocument->storage_path),
            $this->downloadName($agentDocument),
            [
                'Content-Type' => $agentDocument->mime_type,
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }

    private function downloadName(AgentDocument $document): string
    {
        $extension = strtolower((string) pathinfo($document->original_filename, PATHINFO_EXTENSION));
        $baseName = Str::slug((string) pathinfo($document->original_filename, PATHINFO_FILENAME));

        return ($baseName ?: 'agent-document').($extension ? '.'.$extension : '');
    }
}
