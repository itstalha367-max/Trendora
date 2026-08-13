<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemTestRun extends Model
{
    protected $fillable = ['type', 'status', 'message', 'context', 'tested_by'];
    protected $casts = ['context' => 'array'];

    public function tester()
    {
        return $this->belongsTo(User::class, 'tested_by');
    }
}
