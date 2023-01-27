<?php

namespace IDT\LaravelCommon\Tests\Fixtures\Models;

use IDT\LaravelCommon\Lib\HashIds\HasHashId;
use Illuminate\Database\Eloquent\Model;

class TestingModel extends Model
{
    use HasHashId;

    protected $guarded = ['id'];
}
