<?php

namespace WalkerChiu\Friendship\Models\Forms;

use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;

class FriendshipFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (string) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'user_id_a' => trans('php-friendship::friendship.user_id_a'),
            'user_id_b' => trans('php-friendship::friendship.user_id_b'),
            'state'     => trans('php-friendship::friendship.state'),
            'flag_a'    => trans('php-friendship::friendship.flag_a'),
            'flag_b'    => trans('php-friendship::friendship.flag_b')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'user_id_a' => ['required','string','different:user_id_b','exists:'.config('wk-core.table.user').',id'],
            'user_id_b' => ['required','string','different:user_id_a','exists:'.config('wk-core.table.user').',id'],
            'state'     => ['required', Rule::in(config('wk-core.class.friendship.friendshipState')::getCodes())],
            'flag_a'    => 'boolean',
            'flag_b'    => 'boolean'
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','string','exists:'.config('wk-core.table.friendship.friendships').',id']]);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.required'         => trans('php-core::validation.required'),
            'id.string'           => trans('php-core::validation.string'),
            'id.exists'           => trans('php-core::validation.exists'),
            'user_id_a.required'  => trans('php-core::validation.required'),
            'user_id_a.string'    => trans('php-core::validation.string'),
            'user_id_a.different' => trans('php-core::validation.different'),
            'user_id_a.exists'    => trans('php-core::validation.exists'),
            'user_id_b.required'  => trans('php-core::validation.required'),
            'user_id_b.string'    => trans('php-core::validation.string'),
            'user_id_b.different' => trans('php-core::validation.different'),
            'user_id_b.exists'    => trans('php-core::validation.exists'),
            'state.required'      => trans('php-core::validation.required'),
            'state.in'            => trans('php-core::validation.in'),
            'flag_a.boolean'      => trans('php-core::validation.boolean'),
            'flag_b.boolean'      => trans('php-core::validation.boolean')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
    }
}
