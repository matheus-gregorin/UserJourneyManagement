<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyMysqlModel extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    /**
     * Nome da tabela no banco de dados.
     *
     * @var string
     */
    protected $table = 'company';

    protected $primaryKey = 'uuid';

    // Usa UUIDs string
    protected $keyType = 'string';

    // Não está utiliza auto-incremento
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'corporateReason',
        'fantasyName',
        'cnpj',
        'plan',
        'active',
        'createdAt',
        'updatedAt'
    ];
}
