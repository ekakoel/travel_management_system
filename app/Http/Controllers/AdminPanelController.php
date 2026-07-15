<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tours;
use App\Models\Hotels;
use App\Models\Villas;
use App\Models\UserLog;
use App\Models\Services;
use App\Models\UsdRates;
use App\Models\Weddings;
use App\Models\Attention;
use App\Models\Activities;
use App\Models\AdminPanel;
use App\Models\HotelPrice;
use App\Models\Transports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreAdminPanelRequest;
use App\Http\Requests\UpdateAdminPanelRequest;

class AdminPanelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','type:admin']);
    }
    
    // FUNCTION ADD SERVICE =============================================================================================================>
    public function admin_panel_main(Request $request){
        return view('backend.developer.index', $this->adminPanelData());
    }

    public function hotelPriceChart(Request $request)
    {
        $hotelId = $request->hotel_id;

        $prices = HotelPrice::where('hotels_id', $hotelId)
            ->select(
                DB::raw('MONTH(start_date) as month'),
                DB::raw('AVG(contract_rate) as avg_price')
            )
            ->groupBy(DB::raw('MONTH(start_date)'))
            ->orderBy('month')
            ->get();

        $months = [];
        $values = [];

        foreach ($prices as $price) {
            $months[] = Carbon::create()->month((int) $price->month)->format('M');
            $values[] = round((float) $price->avg_price, 2);
        }

        return response()->json([
            'months' => $months,
            'values' => $values
        ]);
    }
    
    public function index()
    {
        return view('backend.developer.index', $this->adminPanelData());
    }

    protected function adminPanelData(): array
    {
        $serviceCounts = [
            'Hotels' => $this->activeDraftCounts(Hotels::query()),
            'Tours' => $this->activeDraftCounts(Tours::query()),
            'Activities' => $this->activeDraftCounts(Activities::query()),
            'Transports' => $this->activeDraftCounts(Transports::query()),
            'Villas' => $this->activeDraftCounts(Villas::query()),
            'Weddings' => [
                'active' => Weddings::query()->where('status', 'Active')->count(),
                'draft' => Weddings::query()->where('status', '!=', 'Active')->count(),
            ],
        ];

        $services = Services::query()
            ->orderByRaw("status = 'Active' desc")
            ->orderBy('name')
            ->get()
            ->map(function ($service) use ($serviceCounts) {
                $counts = $serviceCounts[$service->name] ?? ['active' => 0, 'draft' => 0];

                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'nicname' => $service->nicname,
                    'icon' => $service->icon,
                    'status' => $service->status,
                    'active_count' => $counts['active'],
                    'draft_count' => $counts['draft'],
                    'total_count' => $counts['active'] + $counts['draft'],
                ];
            });

        $expectedCurrencies = collect(['USD', 'CNY', 'TWD']);
        $currencyRates = UsdRates::query()
            ->whereIn('name', $expectedCurrencies->all())
            ->get()
            ->keyBy('name');

        $attentions = Attention::query()
            ->where('page', 'admin-panel')
            ->get();

        $inactiveServices = $services->where('status', '!=', 'Active')->count();
        $totalDraftContent = collect($serviceCounts)->sum('draft');
        $missingCurrencyRates = $expectedCurrencies
            ->reject(fn ($currency) => $currencyRates->has($currency))
            ->values();
        $servicesMissingMetadata = $services
            ->filter(fn ($service) => empty($service['nicname']) || empty($service['icon']))
            ->values();

        $developerHealthChecks = collect([
            [
                'label' => 'Service Registry',
                'status' => $servicesMissingMetadata->isEmpty() ? 'Healthy' : 'Needs Review',
                'meta' => $servicesMissingMetadata->isEmpty()
                    ? 'All registered services have slug and icon metadata.'
                    : $servicesMissingMetadata->count() . ' services are missing slug or icon metadata.',
                'tone' => $servicesMissingMetadata->isEmpty() ? 'healthy' : 'warning',
            ],
            [
                'label' => 'Access Baseline',
                'status' => 'Role Based',
                'meta' => 'Developer pages rely on route middleware and policies for access control.',
                'tone' => 'info',
            ],
            [
                'label' => 'Currency Integration',
                'status' => $missingCurrencyRates->isEmpty() ? 'Ready' : 'Incomplete',
                'meta' => $missingCurrencyRates->isEmpty()
                    ? 'USD, CNY, and TWD exchange rates are configured.'
                    : 'Missing rate: ' . $missingCurrencyRates->implode(', '),
                'tone' => $missingCurrencyRates->isEmpty() ? 'healthy' : 'danger',
            ],
            [
                'label' => 'Developer Notes',
                'status' => $attentions->count() . ' Notes',
                'meta' => $attentions->isEmpty()
                    ? 'No admin-panel notes are currently configured.'
                    : 'Review active notes before changing this dashboard.',
                'tone' => $attentions->isEmpty() ? 'neutral' : 'info',
            ],
        ]);

        return [
            'adminpanel' => AdminPanel::query()->get(),
            'attentions' => $attentions,
            'currencyRates' => $currencyRates,
            'expectedCurrencies' => $expectedCurrencies,
            'missingCurrencyRates' => $missingCurrencyRates,
            'services' => $services,
            'serviceCounts' => $serviceCounts,
            'developerHealthChecks' => $developerHealthChecks,
            'dashboardStats' => [
                [
                    'label' => 'Registered Services',
                    'value' => $services->where('status', 'Active')->count(),
                    'meta' => $inactiveServices . ' inactive from ' . $services->count() . ' total services',
                    'icon' => 'fa fa-cubes',
                    'tone' => 'teal',
                ],
                [
                    'label' => 'Draft Content',
                    'value' => $totalDraftContent,
                    'meta' => 'Inactive or draft records across service domains',
                    'icon' => 'fa fa-code-fork',
                    'tone' => 'blue',
                ],
                [
                    'label' => 'Currency Setup',
                    'value' => $currencyRates->count() . '/' . $expectedCurrencies->count(),
                    'meta' => $missingCurrencyRates->isEmpty() ? 'Required rates configured' : 'Missing ' . $missingCurrencyRates->implode(', '),
                    'icon' => 'fa fa-exchange',
                    'tone' => 'green',
                ],
                [
                    'label' => 'Developer Notes',
                    'value' => $attentions->count(),
                    'meta' => 'Admin-panel notes configured for developer review',
                    'icon' => 'fa fa-sticky-note-o',
                    'tone' => 'amber',
                ],
            ],
        ];
    }

    protected function activeDraftCounts($query): array
    {
        return [
            'active' => (clone $query)->where('status', 'Active')->count(),
            'draft' => (clone $query)->where('status', '!=', 'Active')->count(),
        ];
    }

// FUNCTION ADD SERVICE =============================================================================================================>
    public function func_add_service(Request $request)
        {
            $status = "Draft";
            $service =new Services([
                "name"=>$request->name,
                "nicname"=>$request->nicname,
                "icon"=>$request->icon,
                "status"=>$status,
            ]);
            $service->save();
            
            // USER LOG
            $service_id = 1;
            $action = "Add";
            $service = "Service";
            $subservice = $request->name;
            $page = "admin-panel";
            $note = "Add Service";
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$service_id,
                "page"=>$page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/admin-panel")->with('success','Service has been added!');
        }

// FUNCTION EDIT SERVICE =============================================================================================================>
    public function func_edit_service(Request $request,$id)
    {
        $service=Services::findOrFail($id);
        $service->update([
            "name"=>$request->name,
            "nicname"=>$request->nicname,
            "icon"=>$request->icon,
            "status"=>$request->status,
        ]);

        // USER LOG
        $action = "Edit Service";
        $service = "Service";
        $subservice = "Disable Service";
        $page = "admin-panel";
        $note = "Update Service: ".$id;
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
        return redirect("/admin-panel")->with('success','Service has been Updated!');
    }

// FUNCTION DISABLE SERVICE =============================================================================================================>
    public function func_disable_service(Request $request,$id)
    {
        $service=Services::findOrFail($id);
        $service->update([
            "status"=>$request->status,
        ]);

        // USER LOG
        $action = "Update Service";
        $service = "Service";
        $subservice = "Disable Service";
        $page = "admin-panel";
        $note = "Update Service: ".$id;
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
        return redirect("/admin-panel")->with('success','Service has been disable!');
    }

// FUNCTION ENNABLE SERVICE =============================================================================================================>
    public function func_enable_service(Request $request,$id)
    {
        $service=Services::findOrFail($id);
        $service->update([
            "status"=>$request->status,
        ]);

        // USER LOG
        $action = "Update Service";
        $service = "Service";
        $subservice = "Enable Service";
        $page = "admin-panel";
        $note = "Update Service: ".$id;
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
        return redirect("/admin-panel")->with('success','Service has been activated!');
    }

// FUNCTION REMOVE SERVICE =============================================================================================================>
    public function func_remove_service(Request $request,$id)
    {
        $service=Services::findOrFail($id);
        $service->delete();
       
        // USER LOG
        $action = "Remove Service";
        $service = "Service";
        $subservice = $request->service;
        $page = "admin-panel";
        $note = "Remove service: ".$id;
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
        return redirect("/admin-panel")->with('success','Service has been Removed!');
    }
    
}
