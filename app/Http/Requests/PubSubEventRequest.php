<?php

namespace App\Http\Requests;

use App\Services\Pubsub\Message;
use Illuminate\Foundation\Http\FormRequest;

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

        $requestData['message']['data'] = base64_decode($requestData['message']['data'], true);

        return new Message($requestData['message']);
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
