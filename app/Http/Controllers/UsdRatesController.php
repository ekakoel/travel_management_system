<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Tours;
use App\Models\UserLog;
use App\Models\UsdRates;
use App\Models\BankAccount;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\StoreUsdRatesRequest;
use AmrShawky\LaravelCurrency\Facade\Currency;

class UsdRatesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index()
    {
        $codes = ['USD', 'CNY', 'TWD'];
        $rates = UsdRates::whereIn('name', $codes)->get()->keyBy('name');
        $externalRates = $this->externalRates();

        $currencyRates = collect($codes)->map(function ($code) use ($rates, $externalRates) {
            $rate = $rates->get($code);

            return [
                'code' => $code,
                'model' => $rate,
                'id' => optional($rate)->id,
                'rate' => optional($rate)->rate,
                'sell' => optional($rate)->sell,
                'buy' => optional($rate)->buy,
                'difference' => optional($rate)->difference,
                'updated_at' => optional($rate)->updated_at,
                'external_rate' => $externalRates['rates'][$code] ?? null,
            ];
        })->values();

        return view('backend.developer.currency', [
            'currencyRates' => $currencyRates,
            'externalRates' => $externalRates,
            'tax' => Tax::query()->first(),
            'bank_acc' => BankAccount::query()->orderBy('currency')->orderBy('bank')->get(),
        ]);
    }

    public function func_update_usdrates(Request $request,$id)
    {
        return $this->updateRate($request, $id, 'USD', 'Update USD Rate');
    }

    public function func_update_cnyrates(Request $request,$id)
    {
        return $this->updateRate($request, $id, 'CNY', 'Update CNY Rate');
    }

    public function func_update_twdrates(Request $request,$id)
    {
        return $this->updateRate($request, $id, 'TWD', 'Update TWD Rate');
    }

    public function func_update_tax(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $tax=Tax::findOrFail($id);
            $tax->update([
                "tax"=>$request->tax,
            ]);
            // USER LOG
            $action = "Update Tax";
            $service = "Tax";
            $subservice = "Tax";
            $page = "usdrates";
            $note = "Update Tax: ".$id;
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
            
            return redirect("/currency")->with('success','The UsdRates has been successfully updated!');
        }else{
            return redirect("/currency")->with('error','Tidak dapat merubah data!');
        }
    }

    public function showRates()
    {
        $apiKey = config('exchange_rate_api_key');
        $baseUrl = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD";

        $response = Http::get($baseUrl);
        $rates = $response->json();

        if ($response->successful()) {
            $usdRate = $rates['conversion_rates']['USD'];
            $twdRate = $rates['conversion_rates']['TWD'];
            $cnyRate = $rates['conversion_rates']['CNY'];

            return view('currency-rates', [
                'usdRate' => $usdRate,
                'twdRate' => $twdRate,
                'cnyRate' => $cnyRate,
            ]);
        } else {
            return view('currency-rates')->withErrors('Unable to retrieve exchange rates.');
        }
    }

    private function updateRate(Request $request, int $id, string $code, string $action)
    {
        if (! Gate::any(['posDev', 'posAuthor'])) {
            return redirect()->route('currency')->with('error', 'Anda tidak dapat merubah data!');
        }

        $validated = $request->validate([
            'sell' => ['required', 'numeric', 'min:0'],
            'difference' => ['required', 'numeric', 'min:0'],
        ]);

        $sell = (float) $validated['sell'];
        $difference = (float) $validated['difference'];
        $buy = max($sell - $difference, 0);
        $rate = UsdRates::findOrFail($id);

        $rate->update([
            'rate' => $sell,
            'sell' => $sell,
            'buy' => $buy,
            'difference' => $difference,
        ]);

        UserLog::create([
            'action' => $action,
            'service' => 'Currency',
            'subservice' => $code,
            'subservice_id' => $id,
            'page' => 'currency',
            'user_id' => $request->user()->id,
            'user_ip' => $request->getClientIp(),
            'note' => "{$action}: {$id}",
        ]);

        return redirect()->route('currency')->with('success', "{$code} rate has been updated.");
    }

    private function externalRates(): array
    {
        return Cache::remember('backend.currency.external_rates', now()->addMinutes(30), function () {
            $apiKey = config('app.exchange_rate_api_key');

            if (! $apiKey) {
                return [
                    'rates' => [],
                    'retrieved_at' => null,
                    'status' => 'missing_api_key',
                ];
            }

            try {
                $response = Http::timeout(4)->get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD");

                if (! $response->successful()) {
                    return [
                        'rates' => [],
                        'retrieved_at' => null,
                        'status' => 'unavailable',
                    ];
                }

                $conversionRates = $response->json('conversion_rates', []);
                $idrRate = (float) ($conversionRates['IDR'] ?? 0);

                return [
                    'rates' => [
                        'USD' => $idrRate ?: null,
                        'CNY' => isset($conversionRates['CNY']) && (float) $conversionRates['CNY'] > 0
                            ? $idrRate / (float) $conversionRates['CNY']
                            : null,
                        'TWD' => isset($conversionRates['TWD']) && (float) $conversionRates['TWD'] > 0
                            ? $idrRate / (float) $conversionRates['TWD']
                            : null,
                    ],
                    'retrieved_at' => now(),
                    'status' => 'available',
                ];
            } catch (\Throwable $exception) {
                return [
                    'rates' => [],
                    'retrieved_at' => null,
                    'status' => 'unavailable',
                ];
            }
        });
    }
}
