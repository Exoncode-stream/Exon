<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class User extends Model
{
    use HasFactory;

    public const TOKEN_COOKIE_NAME = 'exon_token';

    protected $fillable = [
        'username',
        'password',
        'token',
        'token_expires_at',
        'role',
    ];

    protected $attributes = [
        'role' => 'viewer',
    ];

    protected $hidden = [
        'password',
        'token',
        'token_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'token_expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Recherche un utilisateur à partir d'un jeton brut (haché SHA-256 avec fallback brut).
     */
    public static function findByToken(string $rawToken): ?self
    {
        $hashedToken = hash('sha256', $rawToken);
        return self::where('token', $hashedToken)->first() 
            ?? self::where('token', $rawToken)->first();
    }

    /**
     * Génère un nouveau jeton aléatoire, sauvegarde son empreinte SHA-256 et retourne le jeton brut.
     */
    public function generateToken(): string
    {
        $plainToken = Str::random(64);
        $this->update([
            'token' => hash('sha256', $plainToken),
            'token_expires_at' => now()->addDays(7),
        ]);
        return $plainToken;
    }

    /**
     * Invalide le jeton actuel.
     */
    public function invalidateToken(): void
    {
        $this->update([
            'token' => null,
            'token_expires_at' => null,
        ]);
    }

    /**
     * Vérifie si le rôle de l'utilisateur fait partie des rôles staff.
     */
    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'moderator']);
    }
}
