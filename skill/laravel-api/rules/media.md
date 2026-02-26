# Media & File Management

The package provides a `MediaHelper` class to standardize file uploads, deletions, and base64 image handling. **ALWAYS** use this instead of raw Laravel `Storage` calls for simple file management.

## Rules

- **MUST** use `Essa\APIToolKit\MediaHelper` for all file uploads and deletions.
- **MUST** define storage paths in a central location (e.g., `StoragePath` Enum or Constants).
- **MUST** use `deleteFile()` when updating or deleting a model with an associated file to prevent "orphan" files.
- **NEVER** use `request()->file('...')->store(...)` directly—always wrap it in `MediaHelper`.
- **NEVER** hardcode the disk name—use the default or call `MediaHelper::disk('s3')` if explicitly required.

## Common Operations

### Single File Upload
```php
use Essa\APIToolKit\MediaHelper;

$path = MediaHelper::uploadFile($request->file('avatar'), 'avatars');
```

### Multiple File Upload
```php
use Essa\APIToolKit\MediaHelper;

$paths = MediaHelper::uploadMultiple($request->file('photos'), 'products');
```

### Base64 Image Upload
Useful for mobile apps or frontend crops:
```php
use Essa\APIToolKit\MediaHelper;

$path = MediaHelper::uploadBase64Image($request->image_base64, 'profiles');
```

### Deleting a File
Always delete the old file when replacing it:
```php
use Essa\APIToolKit\MediaHelper;

MediaHelper::deleteFile($user->avatar);
```

### Using a Specific Disk
```php
use Essa\APIToolKit\MediaHelper;

$path = MediaHelper::disk('s3')->uploadFile($file, 'backups');
```

## Integration Example (Action)

```php
public function execute(array $data): User
{
    if (isset($data['avatar'])) {
        // Delete old avatar if it exists
        if ($this->user->avatar) {
            MediaHelper::deleteFile($this->user->avatar);
        }

        $data['avatar'] = MediaHelper::uploadFile($data['avatar'], 'avatars');
    }

    $this->user->update($data);

    return $this->user;
}
```
