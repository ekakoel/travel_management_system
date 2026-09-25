<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'document_type',
        'storage_path',
        'original_filename',
        'mime_type',
        'size',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
