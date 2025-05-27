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

    protected $primaryKey = 'uuid';

    // Usa UUIDs string
    protected $keyType = 'string';

    // Não está utiliza auto-incremento
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'is_auth',
        'otp',
        'scope',
        'phone',
        'is_admin',
        'role'
    ];

    public function points()
    {
        return $this->hasMany(PointMysqlModel::class, 'user_uuid', 'uuid');
    }
}
