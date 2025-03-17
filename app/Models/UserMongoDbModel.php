<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class UserMongoDbModel extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    /**
     * Nome da tabela no banco de dados.
     *
     * @var string
     */
    protected $collection = 'usuarios'; 

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'phone',
        'is_admin',
        'role'
    ];
}
