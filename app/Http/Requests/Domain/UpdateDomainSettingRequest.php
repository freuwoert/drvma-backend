<?php

namespace App\Http\Requests\Domain;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDomainSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Domain basics
            'company_name' => 'nullable|string|max:255',
            'company_legalname' => 'nullable|string|max:255',
            'company_slogan' => 'nullable|string|max:255',

            // Defaults
            'default_currency' => 'nullable|string|in:EUR,USD,GBP',
            'default_unit_length' => 'nullable|string|in:m,ft',
            'default_unit_weight' => 'nullable|string|in:kg,lb',
            'default_unit_volume' => 'nullable|string|in:l,gal',
            'default_unit_temperature' => 'nullable|string|in:c,f',
            'default_unit_speed' => 'nullable|string|in:kmh,mph',

            // Policies
            'policy_allow_registration' => 'nullable|boolean',
            'policy_allow_password_reset' => 'nullable|boolean',
            'policy_allow_email_change' => 'nullable|boolean',
            'policy_allow_username_change' => 'nullable|boolean',
            'policy_allow_avatar_upload' => 'nullable|boolean',
            'policy_allow_banner_upload' => 'nullable|boolean',

            // Legal
            'legal_notice' => 'nullable|string|max:32000',
            'legal_privacy' => 'nullable|string|max:32000',

            // Setup steps
            'setup_dismissed' => 'nullable|boolean',
            'setup_completed' => 'nullable|boolean',
            'setup_completed_domain_basics' => 'nullable|boolean',
            'setup_completed_domain_logo' => 'nullable|boolean',
            'setup_completed_role_import' => 'nullable|boolean',
            'setup_completed_user_import' => 'nullable|boolean',
            'setup_completed_admin_selection' => 'nullable|boolean',
        ];
    }
}
