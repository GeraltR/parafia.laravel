<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'permissionLevel' => $this->permission_level->value,
            'permissionLevelLabel' => $this->permission_level->label(),
            'canWrite' => [
                'site' => $this->permission_level->canWriteSite(),
                'content' => $this->permission_level->canWriteContent(),
                'management' => $this->permission_level->canWriteManagement(),
            ],
        ];
    }
}
