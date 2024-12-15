<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;

class GetDatatableRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => '',
            'query.search'=>'',
            'pagination.page' => 'gt:0',
            'pagination.perpage' => 'gt:0',
            'sort.sort' => 'in:asc,desc',
            'sort.field' => '',
        ];
    }
}
