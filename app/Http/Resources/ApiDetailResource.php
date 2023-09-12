<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Organization;
use Carbon\Carbon;
class ApiDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'organization' => Organization::select('name')->find($this->org_id),
            'client' => $this->client_id,
            'title'=>str_replace('api/', '', $this->api_url),
            'latitude' => $this->latitute?$this->latitute:'',
            'longitude' => $this->longitute?$this->longitute:'',
            'deviceName' => $this->deviceName?$this->deviceName:'',
            'deviceBrand' => $this->deviceBrand?$this->deviceBrand:'',
            'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('Y-m-d h:i:s'),
            'updated_at' => Carbon::createFromFormat('Y-m-d H:i:s', $this->updated_at)->format('Y-m-d h:i:s'),
        ];
    }
}
