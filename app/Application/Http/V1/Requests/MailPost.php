<?php

namespace App\Application\Http\V1\Requests;

use App\Domain\Values\Mail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class MailPost extends FormRequest
{
    protected static function getRules()
    {
        return [
            'from' => 'required|email',
            'to' => 'required|email',
            'cc.*' => 'nullable|email',
            'format' => 'nullable|in:'. implode(',', Mail::TYPES),
            'subject' => 'required',
            'body' => 'required'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return MailPost::getRules();
    }

    public static function validateData(array $data): \Illuminate\Validation\Validator
    {

        /** @var \Illuminate\Validation\Validator $validator */
        return Validator::make($data, MailPost::getRules());
    }
}
