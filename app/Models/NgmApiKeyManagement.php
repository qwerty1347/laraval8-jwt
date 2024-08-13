<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NgmApiKeyManagement extends Model
{
    use HasFactory;

    protected $connection = "ngm_mirror1_mysql";

    protected $table = "ngm_apiKeyManagement";

    public $timestamps = false;
}
