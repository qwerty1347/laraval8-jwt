<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NgmApiKeyErrorLog extends Model
{
    use HasFactory;

    protected $connection = "log_mysql";

    protected $table = "ngm_apiKeyErrorLog";

    public $timestamps = false;
}
