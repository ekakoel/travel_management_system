<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Models\Attention;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Models\TermAndCondition;
use App\Services\PublicFaqService;
use App\Http\Requests\StoreTermAndConditionRequest;
use App\Http\Requests\UpdateTermAndConditionRequest;

class TermAndConditionController extends Controller
{
    private const POLICY_TYPES = [
        'User',
        'System',
        'Administrator',
        'Currency',
        'Price',
        'Promotion',
        'FAQ',
    ];

    public function index()
    {
        $attentions = Attention::where('page','term-and-condition')->get();
        $policySections = $this->policySections(self::POLICY_TYPES);
        $business = BusinessProfile::where('id',1)->first();
        return view('privacy-policy.term-and-condition',[
            'attentions'=>$attentions,
            'policySections'=>$policySections,
            'policyTypes'=>self::POLICY_TYPES,
            'business'=>$business,
        ]);
    }
    public function v_privacy_policy()
    {
        $business = BusinessProfile::where('id',1)->first();
        $attentions = Attention::where('page','term-and-condition')->get();
        $tandcs = TermAndCondition::where('type','User')
        ->where('status','Active')
        ->get();
        $system_term = TermAndCondition::where('type','System')
        ->where('status','Active')
        ->get();
        $admin_term = TermAndCondition::where('type','Administrator')
        ->where('status','Active')
        ->get();
        $price_term = TermAndCondition::where('type','Price')
        ->get();
        $promotion_term = TermAndCondition::where('type','Promotion')
        ->get();
        return view('privacy-policy.privacy-policy',compact('tandcs'),[
            'attentions'=>$attentions,
            'system_term'=>$system_term,
            'admin_term'=>$admin_term,
            'price_term'=>$price_term,
            'promotion_term'=>$promotion_term,
            'business'=>$business,
        ]);
    }

    // FUNCTION EDIT POLICY =============================================================================================================>
    public function func_edit_policy(Request $request,$id)
    {
        $policy=TermAndCondition::findOrFail($id);
        $policy->update([
            "type"=>$request->type,
            "name_id"=>$request->name_id,
            "name_en"=>$request->name_en,
            "name_zh"=>$request->name_zh,
            "policy_id"=>$request->policy_id,
            "policy_en"=>$request->policy_en,
            "policy_zh"=>$request->policy_zh,
            "status"=>$request->status,
        ]);

        // USER LOG
        $action = "Edit Policy";
        $service = "Term & Condition";
        $subservice = "Policy";
        $page = "term-and-condition";
        $note = "Update Policy: ".$id;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$id,
            "page"=>$page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/term-and-condition")->with('success','Policy has been Updated!');
    }


    // FUNCTION ADD POLICY =============================================================================================================>
    public function func_add_policy(Request $request)
        {
            $policy =new TermAndCondition([
                "type"=>$request->type,
                "name_id"=>$request->name_id,
                "name_en"=>$request->name_en,
                "name_zh"=>$request->name_zh,
                "policy_en"=>$request->policy_en,
                "policy_id"=>$request->policy_id,
                "policy_zh"=>$request->policy_zh,
                "status"=>$request->status,
            ]);
            $policy->save();
            
            // USER LOG
            $action = "Add new Policy";
            $service = "Term & Condition";
            $subservice = "Policy";
            $page = "term-and-condition";
            $note = "Add New Policy: ".$policy->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$policy->id,
                "page"=>$page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/term-and-condition")->with('success','New Policy added successfully!');
        }
// FUNCTION REMOVE SERVICE =============================================================================================================>
    public function fdestroy_policy(Request $request,$id)
        {
            $policy=TermAndCondition::findOrFail($id);
            $policy->delete();
        
            // USER LOG
            $action = "Destroy Policy";
            $service = "Term & Condition";
            $subservice = "Policy";
            $page = "term-and-condition";
            $note = "Destroy Policy: ".$policy->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$policy->id,
                "page"=>$page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/term-and-condition")->with('success','Policy has been Removed!');
        }

    public function terms_and_conditions()
    {
        $business = BusinessProfile::where('id',1)->first();
        $policyGroups = $this->publicPolicyGroups(['User', 'System', 'Administrator', 'Currency', 'Price', 'Promotion']);

        return view('frontend.landing-page.policies.terms-and-conditions',[
            'business'=>$business,
            'pageKey'=>'terms',
            'pageTitle'=>__('messages.Terms and Conditions'),
            'pageEyebrow'=>__('messages.Legal Center'),
            'pageDescription'=>__('messages.Read the active terms, operating rules, pricing policies, and promotion conditions before using the Bali Kami Tour partner platform.'),
            'policyGroups'=>$policyGroups,
            'summaryItems'=>[
                __('messages.User access rules'),
                __('messages.System and administrator policy'),
                __('messages.Price, currency, and promotion terms'),
            ],
            'emptyMessage'=>__('messages.No active policy content is available yet.'),
        ]);
    }
    public function privacy_policy()
    {
        $business = BusinessProfile::where('id',1)->first();
        $policyGroups = $this->publicPolicyGroups(['System']);

        return view('frontend.landing-page.policies.privacy-policy',[
            'pageKey'=>'privacy',
            'pageTitle'=>__('messages.Privacy Policy'),
            'pageEyebrow'=>__('messages.Privacy and Data Use'),
            'pageDescription'=>__('messages.Understand how Bali Kami Tour protects user information and handles data inside the partner platform.'),
            'opening'=>__('messages.Welcome to https://online.balikamitour.com, a platform dedicated to providing information on tourism services online. Bali Kami Tour & Travel understands the importance of your privacy and is committed to protecting your personal information. This Privacy Policy explains how we collect, use, and protect the information you provide to us, based on applicable legal provisions. By using our services, you can be assured that the privacy data you provide to us will not be used for any purposes that may harm any party. By registering, you agree to the terms of this Privacy Policy.'),
            'policyGroups'=>$policyGroups,
            'summaryItems'=>[
                __('messages.Personal data protection'),
                __('messages.Platform usage transparency'),
                __('messages.Registered partner privacy'),
            ],
            'emptyMessage'=>__('messages.No active privacy policy content is available yet.'),
            'business'=>$business,
        ]);
    }

    public function faq(PublicFaqService $publicFaqService)
    {
        $business = BusinessProfile::where('id',1)->first();
        $policyGroups = $publicFaqService->groups();

        return view('frontend.landing-page.policies.faq', [
            'pageKey'=>'faq',
            'pageTitle'=>__('messages.FAQs'),
            'pageEyebrow'=>__('messages.Help Center'),
            'pageDescription'=>__('messages.Find practical answers about registration, partner access, platform use, and operational support before signing in.'),
            'policyGroups'=>$policyGroups,
            'summaryItems'=>[
                __('messages.Partner onboarding'),
                __('messages.Account access'),
                __('messages.Operational support'),
            ],
            'emptyMessage'=>__('messages.No active FAQ content is available yet.'),
            'business'=>$business,
        ]);
    }

    private function policySections(array $types)
    {
        return collect($types)->map(function ($type) {
            return [
                'type'=>$type,
                'title'=>$this->policyTypeLabel($type),
                'items'=>TermAndCondition::where('type', $type)->latest()->get(),
            ];
        });
    }

    private function publicPolicyGroups(array $types)
    {
        return collect($types)->map(function ($type) {
            return [
                'type'=>$type,
                'title'=>$this->policyTypeLabel($type),
                'items'=>TermAndCondition::where('type', $type)
                    ->where('status', 'Active')
                    ->get()
                    ->map(function ($policy) {
                        return [
                            'title'=>$this->localizedPolicyValue($policy, 'name'),
                            'content'=>$this->localizedPolicyValue($policy, 'policy'),
                        ];
                    })
                    ->filter(fn ($policy) => filled($policy['title']) || filled($policy['content']))
                    ->values(),
            ];
        })->filter(fn ($group) => $group['items']->isNotEmpty())->values();
    }

    private function localizedPolicyValue(TermAndCondition $policy, string $field): ?string
    {
        $locale = app()->getLocale();
        $suffix = str_starts_with($locale, 'zh') ? 'zh' : ($locale === 'en' ? 'en' : 'id');
        $localizedField = "{$field}_{$suffix}";
        $fallbackField = "{$field}_en";

        return $policy->{$localizedField} ?: $policy->{$fallbackField} ?: $policy->{"{$field}_id"};
    }

    private function policyTypeLabel(string $type): string
    {
        return match ($type) {
            'User' => __('messages.User Policy'),
            'System' => __('messages.System Policy'),
            'Administrator' => __('messages.Administrator Policy'),
            'Currency' => __('messages.Currency Policy'),
            'Price' => __('messages.Price Policy'),
            'Promotion' => __('messages.Promotion Policy'),
            'FAQ' => __('messages.FAQs'),
            default => $type,
        };
    }

}
