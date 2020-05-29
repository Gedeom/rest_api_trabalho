<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Pessoa
 *
 * @property int $id
 * @property string $nome
 * @property string $cpf
 * @property string $dt_nascimento
 * @property string $celular
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pessoa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pessoa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pessoa query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pessoa whereCelular($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pessoa whereCpf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pessoa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pessoa whereDtNascimento($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pessoa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pessoa whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pessoa whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $email
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pessoa whereEmail($value)
 */
class Pessoa extends Model
{
    protected $table = 'pessoa';
}
