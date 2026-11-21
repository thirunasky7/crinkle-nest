<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    /** Returns [type, src]; type is file|embed|link|null. */
    public function playable(): array
    {
        if ($this->video_file) {
            return ['file', media($this->video_file)];
        }
        $url = (string) $this->video_url;
        if ($url === '') {
            return [null, null];
        }
        if (preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([\w-]{6,})~', $url, $m)) {
            return ['embed', 'https://www.youtube.com/embed/'.$m[1].'?autoplay=1&rel=0'];
        }
        if (preg_match('~vimeo\.com/(\d+)~', $url, $m)) {
            return ['embed', 'https://player.vimeo.com/video/'.$m[1].'?autoplay=1'];
        }
        if (preg_match('~\.(mp4|webm|ogg)(\?.*)?$~i', $url)) {
            return ['file', $url];
        }

        return ['link', $url];
    }
}
