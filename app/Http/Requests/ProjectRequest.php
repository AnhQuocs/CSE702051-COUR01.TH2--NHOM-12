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
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'nullable|uuid|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reminder_time' => 'nullable|date|before:end_date',
            'tags' => 'nullable|array',
            'tags.*' => 'uuid|exists:tags,id',
            'subtasks' => 'nullable|array',
            'subtasks.*.title' => 'required|string|max:255',
            'subtasks.*.description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề dự án là bắt buộc.',
            'priority.required' => 'Mức độ ưu tiên là bắt buộc.',
            'priority.in' => 'Mức độ ưu tiên không hợp lệ.',
            'start_date.date' => 'Ngày bắt đầu phải là ngày hợp lệ.',
            'end_date.date' => 'Ngày kết thúc phải là ngày hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'reminder_time.date' => 'Thời gian nhắc nhở phải là ngày/giờ hợp lệ.',
            'reminder_time.before' => 'Thời gian nhắc nhở phải trước ngày kết thúc.',
            'category_id.exists' => 'Danh mục được chọn không hợp lệ.',
            'tags.*.exists' => 'Tag được chọn không hợp lệ.',
            'subtasks.*.title.required' => 'Tiêu đề công việc là bắt buộc.',
            'subtasks.*.title.max' => 'Tiêu đề công việc không được quá 255 ký tự.',
            'subtasks.*.description.max' => 'Mô tả công việc không được quá 500 ký tự.',
        ];
    }
}
