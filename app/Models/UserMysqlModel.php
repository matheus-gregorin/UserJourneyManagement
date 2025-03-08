<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMysqlModel extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    /**
     * Nome da tabela no banco de dados.
     *
     * @var string
     */
    protected $table = 'usuarios';

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'is_admin',
        'role'
    ];
}
