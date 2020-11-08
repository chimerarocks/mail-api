<?php

namespace App\Http\V1\Requests;

use App\Models\Values\MailFormat;
use Illuminate\Foundation\Http\FormRequest;

class MailPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from' => 'required|email',
            'to' => 'required|email',
            'cc' => 'nullable|email',
            'format' => 'nullable|in:'. implode(',', MailFormat::TYPES),
            'subject' => 'required',
            'body' => 'required'
        ];
    }
}
