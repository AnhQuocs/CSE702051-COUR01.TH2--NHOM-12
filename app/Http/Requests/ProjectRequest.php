<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'nullable|uuid|exists:categories,id',
            'priority' => 'required|in:Thấp,Trung bình,Cao',
            'status' => 'required|in:Lên kế hoạch,Đang thực hiện,Đã hoàn thành,Hoàn thành muộn',
            'deadline' => 'required|date|after_or_equal:today',
            'reminder_time' => 'nullable|date|before:deadline',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'deadline.after_or_equal' => 'Deadline phải từ hôm nay trở đi.',
            'reminder_time.before' => 'Thời gian nhắc phải trước deadline.',
        ];
    }
}
