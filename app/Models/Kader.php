<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Kader extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $table = 'kader';
   
    protected $fillable = [
        'nama_lengkap',
        'telepon',
        'password',
        'profil_pict',
        'rt_id',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected function casts(): array
    {
        return [
            'dibuat_pada' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // Relationships
    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }
    
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(ListEvent::class, 'event_kader', 'id_kader', 'id_event')
            ->withTimestamps();
    }
    
    public function savedVideos()
    {
        return $this->hasMany(SavedVideo::class, 'kader_id');
    }
    
    // Helper methods
    public function isRegisteredToEvent(int $eventId): bool
    {
        return $this->events()->where('list_event.id', $eventId)->exists();
    }
    
    public function saveVideo(int $videoId): void
    {
        $this->savedVideos()->syncWithoutDetaching([
            $videoId => ['saved_at' => now()]
        ]);
    }
    
    public function unsaveVideo(int $videoId): bool
    {
        return $this->savedVideos()->detach($videoId) > 0;
    }
    
    public function hasVideoSaved(int $videoId): bool
    {
        return $this->savedVideos()->where('video_id', $videoId)->exists();
    }
    
    /**
     * Get the profile picture URL with proper fallback handling
     */
    public function getProfilePictureUrlAttribute()
    {
        if (!$this->profil_pict) {
            return null;
        }
        
        // Try different possible paths
        $possiblePaths = [
            'profile_pictures/' . $this->profil_pict,
            'profile_pictures/' . basename($this->profil_pict),
            $this->profil_pict
        ];
        
        foreach ($possiblePaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->url($path);
            }
        }
        
        return null;
    }
    
    /**
     * Check if profile picture exists
     */
    public function hasProfilePicture(): bool
    {
        return !is_null($this->getProfilePictureUrlAttribute());
    }
}