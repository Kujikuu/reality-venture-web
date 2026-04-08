<?php

namespace App\Http\Requests;

use App\Enums\BusinessStage;
use App\Enums\DiscoverySource;
use App\Enums\FundingRound;
use App\Enums\Industry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStartupApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(?:\+?966|0)?5\d{8}$/'],
            'city' => ['nullable', 'string', 'max:255'],
            'linkedin_profile' => ['nullable', 'url', 'max:500'],

            'business_stage' => ['required', Rule::enum(BusinessStage::class)],
            'company_name' => ['required', 'string', 'max:255'],
            'number_of_founders' => ['nullable', 'integer', 'min:1', 'max:20'],
            'hq_country' => ['nullable', 'string', 'size:2'],
            'website_link' => ['nullable', 'url', 'max:500'],
            'founded_date' => ['nullable', 'date', 'before_or_equal:today'],
            'industry' => ['nullable', Rule::enum(Industry::class)],
            'industry_other' => ['nullable', 'required_if:industry,other', 'string', 'max:255'],
            'company_description' => ['required', 'string', 'max:600'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:20480'],

            'current_funding_round' => ['nullable', Rule::enum(FundingRound::class)],
            'investment_ask_sar' => ['nullable', 'integer', 'min:1'],
            'valuation_sar' => ['nullable', 'integer', 'min:1'],
            'previous_funding' => ['nullable', 'string', 'max:2000'],
            'demo_link' => ['nullable', 'url', 'max:500'],

            'discovery_source' => ['required', Rule::enum(DiscoverySource::class)],
            'referral_name' => ['nullable', 'required_if:discovery_source,referral', 'string', 'max:255'],
            'referral_param' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'validation.firstName.required',
            'last_name.required' => 'validation.lastName.required',
            'email.required' => 'validation.email.required',
            'email.email' => 'validation.email.format',
            'phone.required' => 'validation.phone.required',
            'phone.regex' => 'validation.phone.format',
            'linkedin_profile.url' => 'validation.linkedin.url',

            'company_name.required' => 'validation.companyName.required',
            'number_of_founders.required' => 'validation.numberOfFounders.required',
            'number_of_founders.min' => 'validation.numberOfFounders.min',
            'number_of_founders.max' => 'validation.numberOfFounders.max',
            'hq_country.required' => 'validation.hqCountry.required',
            'website_link.required' => 'validation.websiteLink.required',
            'website_link.url' => 'validation.websiteLink.url',
            'founded_date.required' => 'validation.foundedDate.required',
            'founded_date.before_or_equal' => 'validation.foundedDate.future',
            'industry.required' => 'validation.industry.required',
            'industry_other.required_if' => 'validation.industryOther.required',
            'company_description.required' => 'validation.companyDescription.required',
            'company_description.max' => 'validation.companyDescription.max',

            'current_funding_round.required' => 'validation.currentFundingRound.required',
            'investment_ask_sar.required' => 'validation.investmentAsk.required',
            'investment_ask_sar.min' => 'validation.investmentAsk.min',
            'valuation_sar.required' => 'validation.valuation.required',
            'valuation_sar.min' => 'validation.valuation.min',
            'demo_link.url' => 'validation.demoLink.url',

            'city.max' => 'validation.city.max',
            'business_stage.required' => 'validation.businessStage.required',
            'attachment.mimes' => 'validation.attachment.mimes',
            'attachment.max' => 'validation.attachment.max',

            'discovery_source.required' => 'validation.discoverySource.required',
            'referral_name.required_if' => 'validation.referralName.required',
        ];
    }
}
