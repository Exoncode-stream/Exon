# Backend Architecture & Refactoring Documentation

This document describes the architectural refactoring, design patterns, and code quality improvements implemented in Exon's Laravel 12 backend.

---

## 🛠️ Summary of Backend Modifications

### 1. 📝 Custom FormRequest Validation Layer (`app/Http/Requests/`)
Extracted inline validation rules from controllers into dedicated Laravel `FormRequest` classes for cleaner separation of concerns:
- **`ArticleRequest.php`**: Validates `title` (required, string, max 255) and `content` (required, string).
- **`CommentRequest.php`**: Validates `content` (required, string, max 1000).
- **`LinkRequest.php`**: Validates `name` (required, string, max 255) and `url` (required, valid URL).
- **`VideoRequest.php`**: Validates `title` (required, string, max 255), `youtube_id` (required, string), and `category` (required, string, max 100).

### 2. 🧩 Polymorphic Eloquent Trait (`app/Models/Traits/HasCommentsAndLikes.php`)
Extracted repetitive morphMany relations (`comments()` and `likes()`) shared across polymorphic content models into a single reusable trait:
```php
namespace App\Models\Traits;

use App\Models\Comment;
use App\Models\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCommentsAndLikes
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
```
- Applied `HasCommentsAndLikes` to `Article.php` and `Video.php`.

### 3. ⚙️ Polymorphic Resolver Service (`app/Services/PolymorphicResolver.php`)
Replaced duplicated `$type === 'articles' ? Article::class : Video::class` switches with a centralized resolution service:
- Resolves morph types (`articles` -> `Article::class`, `videos` -> `Video::class`).
- Safely handles model resolution or throws `404 Not Found` HTTP exceptions.

### 4. 👤 Domain Methods & Constants on `User` Model (`app/Models/User.php`)
Encapsulated authentication and role logic directly on the `User` model:
- **`TOKEN_COOKIE_NAME`**: Constant for consistent HttpOnly cookie naming (`exon_token`).
- **`findByToken(string $rawToken)`**: Resolves a user via SHA-256 hashed token lookup (with raw fallback).
- **`generateToken()`**: Generates a 64-character random token, stores its SHA-256 hash, and sets expiration (7 days).
- **`invalidateToken()`**: Clears user token and expiration timestamp upon logout or token expiry.
- **`isStaff()`**: Helper returning `true` if role is `admin` or `moderator`.

### 5. 🛣️ Implicit Model Binding & Route Cleanups (`routes/api.php` & Controllers)
- Updated API routes to leverage Laravel Implicit Model Binding (e.g. `/articles/{article}`, `/videos/{video}`, `/links/{link}`, `/comments/{comment}`).
- Simplified controller methods (`show`, `update`, `destroy`) by receiving typed Eloquent instances directly, removing manual `findOrFail($id)` queries.
- Refactored `TokenAuth` middleware to consume `User::findByToken()` and `User::invalidateToken()`.

---

## 🧪 Verification & Testing

- [x] **PHPUnit Suite**: Run `./vendor/bin/phpunit` or `docker compose run --rm backend-test`.
- [x] **Code Formatting**: Validated code styling via Laravel Pint (`./vendor/bin/pint`).
- [x] **API Route Contracts**: Verified standard JSON responses (`user`, `articles`, `videos`, `links`, `comments`, etc.).
