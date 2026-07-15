<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tours;
use App\Models\Hotels;
use App\Models\Orders;
use App\Models\Villas;
use App\Models\UserLog;
use App\Models\Services;
use App\Models\UiConfig;
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
        $now = Carbon::now();
        $futureOrders = Orders::query()->where('checkin', '>=', $now);

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

        $orderPipeline = collect([
            $this->orderPipelineItem('Confirmed', 'Confirmed', clone $futureOrders),
            $this->orderPipelineItem('Pending', 'Pending', clone $futureOrders),
            $this->orderPipelineItem('Invalid', 'Invalid', clone $futureOrders),
            $this->orderPipelineItem('Rejected', 'Rejected', clone $futureOrders),
        ]);

        $validOrderRange = Orders::query()
            ->where('status', 'Active')
            ->selectRaw('MIN(checkin) as min_date, MAX(checkin) as max_date, COUNT(*) as total_orders')
            ->first();

        $recentOrders = Orders::query()
            ->select(['id', 'orderno', 'service', 'servicename', 'status', 'checkin', 'final_price', 'created_at'])
            ->latest()
            ->limit(6)
            ->get();

        $uiConfigSummary = [
            'total' => UiConfig::query()->count(),
            'active' => UiConfig::query()->where('status', true)->count(),
            'inactive' => UiConfig::query()->where('status', false)->count(),
        ];

        $currencyRates = UsdRates::query()
            ->whereIn('name', ['USD', 'CNY', 'TWD'])
            ->get()
            ->keyBy('name');

        $hotels = Hotels::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        $approvedRevenue = Orders::query()->where('status', 'Approved')->sum('final_price');

        return [
            'adminpanel' => AdminPanel::query()->get(),
            'attentions' => Attention::query()->where('page', 'admin-panel')->get(),
            'currencyRates' => $currencyRates,
            'services' => $services,
            'serviceCounts' => $serviceCounts,
            'orderPipeline' => $orderPipeline,
            'validOrderRange' => $validOrderRange,
            'validOrderRevenue' => $approvedRevenue,
            'recentOrders' => $recentOrders,
            'configs' => UiConfig::query()->orderBy('page', 'asc')->limit(8)->get(),
            'uiConfigSummary' => $uiConfigSummary,
            'hotels' => $hotels,
            'dashboardStats' => [
                [
                    'label' => 'Active Services',
                    'value' => $services->where('status', 'Active')->count(),
                    'meta' => $services->count() . ' total services',
                    'icon' => 'fa fa-cubes',
                    'tone' => 'teal',
                ],
                [
                    'label' => 'Future Orders',
                    'value' => $orderPipeline->sum('count'),
                    'meta' => currencyFormatUsd($orderPipeline->sum('total')),
                    'icon' => 'fa fa-calendar-check-o',
                    'tone' => 'blue',
                ],
                [
                    'label' => 'Approved Revenue',
                    'value' => currencyFormatUsd($approvedRevenue),
                    'meta' => 'Approved order value',
                    'icon' => 'fa fa-line-chart',
                    'tone' => 'green',
                ],
                [
                    'label' => 'UI Config',
                    'value' => $uiConfigSummary['active'] . '/' . $uiConfigSummary['total'],
                    'meta' => $uiConfigSummary['inactive'] . ' inactive controls',
                    'icon' => 'fa fa-sliders',
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

    protected function orderPipelineItem(string $label, string $status, $query): array
    {
        $query->where('status', $status);

        return [
            'label' => $label,
            'status' => $status,
            'count' => (clone $query)->count(),
            'total' => (float) (clone $query)->sum('final_price'),
            'tone' => strtolower($status),
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
