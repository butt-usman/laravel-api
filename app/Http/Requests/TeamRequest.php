<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "*.position" => "required|string",
            "*.mainSkill" => "required|string",
            "*.numberOfPlayers" => "required|integer",
        ];
    }
    public function messages()
    {
        return [
            "*.position.required"=>"position field is required.",
            "*.mainSkill.required"=>"mainSkill field is required.",
            "*.numberOfPlayers.required"=>"numberOfPlayers field is required.",
        ];
    }
}
