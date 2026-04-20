<?php

namespace App\Domain\Setting\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    // Sem softDelete — settings são permanentes
    public $timestamps = true;

    /**
     * Atributo virtual que retorna o valor já convertido para o tipo correto.
     * Chaves do tipo 'secret' são descriptografadas automaticamente pelo
     * cast 'encrypted' declarado abaixo.
     */
    public function getCastValueAttribute(): mixed
    {
        return match($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'secret'  => $this->value, // já descriptografado pelo cast 'encrypted'
            default   => $this->value,
        };
    }

    /**
     * Colunas com cast nativo do Laravel.
     * O cast 'encrypted' aplica AES-256 automaticamente ao salvar/ler.
     */
    protected function casts(): array
    {
        // Apenas 'value' de linhas com type='secret' precisa de encrypt.
        // Como o cast é por coluna (não por linha), usamos o accessor acima
        // para a tipagem e guardamos o valor já criptografado manualmente
        // via mutator.
        return [];
    }

    /**
     * Ao salvar, criptografa valores do tipo secret.
     */
    protected static function booted(): void
    {
        static::saving(function (self $setting) {
            if ($setting->type === 'secret' && $setting->isDirty('value')) {
                $setting->value = encrypt($setting->value);
            }
        });

        static::retrieved(function (self $setting) {
            if ($setting->type === 'secret') {
                try {
                    $setting->value = decrypt($setting->value);
                } catch (\Exception) {
                    // Valor ainda não criptografado (primeira inserção via seed)
                }
            }
        });
    }
}
