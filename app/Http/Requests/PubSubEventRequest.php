<?php

namespace App\Http\Requests;

use Google\Cloud\PubSub\Message;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class PubSubEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function getPubSubMessage(): Message
    {
        $requestData = $this->all();

        Arr::set($requestData, 'message.data', base64_decode(Arr::get($requestData, 'message.data'), true));

        return new Message(Arr::get($requestData, 'message'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message.data' => 'required|string|min:32',
            'message.messageId' => 'required|integer',
            'message.message_id' => 'required|integer',
            'message.publishTime' => 'required|date',
            'message.publish_time' => 'required|date',
            'subscription' => 'required|string|min:8',
        ];
    }
}
