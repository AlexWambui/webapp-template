<?php

namespace Modules\ContactMessage\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ContactMessageEditPageResource extends JsonResource
{
    public function toArray($request)
    {
        // Set timezone to Africa/Nairobi
        $createdAt = Carbon::parse($this->created_at)->setTimezone('Africa/Nairobi');
        $updatedAt = Carbon::parse($this->updated_at)->setTimezone('Africa/Nairobi');

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'message' => $this->message,
            'is_read' => (bool) $this->is_read,
            'is_resolved' => (bool) $this->is_resolved,
            
            'created_at_time_formatted' => $createdAt->format('H:i'),
            'created_at_date_formatted' => $createdAt->format('M d, Y')
        ];
    }
}