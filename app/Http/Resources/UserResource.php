<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema(
 * @OA\Xml(name="UserResource"),
 * @OA\Property(property="name", type="string", readOnly="true", example="John Doe"),
 * @OA\Property(property="username", type="string", example="john_doe"),
 * @OA\Property(property="email", type="string", readOnly="true", format="email", example="john_doe@gmail.com"),
 * @OA\Property(property="role", type="string", readOnly="true", enum={"seller", "buyer"}, example="seller"),
 * @OA\Property(property="balance", type="integer", readOnly="true", example="150"),
 * @OA\Property(property="keys_on_sale", type="integer", readOnly="true", example="5"),
 * @OA\Property(property="keys_sold", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="keys_purchased", type="integer", readOnly="true", example="1"),
 * )
 *
 */
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'balance' => $this->when($this->resource->isSeller(), $this->balance),
            'keys_on_sale' => $this->when($this->resource->isSeller(), $this->keys->count()),
            'keys_sold' => $this->when($this->resource->isSeller(), $this->sales->count()),
            'keys_purchased' => $this->when($this->resource->isBuyer(), $this->payments->count()),
        ];
    }
}
