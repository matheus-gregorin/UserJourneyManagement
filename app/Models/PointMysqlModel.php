<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointMysqlModel extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    /**
     * Nome da tabela no banco de dados.
     *
     * @var string
     */
    protected $table = 'pontos';

    protected $primaryKey = 'uuid';

    // Usa UUIDs string
    protected $keyType = 'string';

    // Não está utiliza auto-incremento
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'user_uuid',
        'observation',
        'checked'
    ];

    public function user()
    {
        return $this->belongsTo(UserMysqlModel::class, 'user_uuid', 'uuid');
    }
}
