<?php
use App\Http\Controllers\ActivitiesAdminController;
use App\Http\Controllers\ActivitiesController;
use App\Http\Controllers\ActivityGuestListTemplateController;
use App\Http\Controllers\ActivityQuoteController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AccommodationFinancialFileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AgentRegistrationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BedTypeController;
use App\Http\Controllers\BookingCodeController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadDataHotelController;
use App\Http\Controllers\DriversController;
use App\Http\Controllers\EmailBlastsController;
use App\Http\Controllers\ExtraBedController;
use App\Http\Controllers\FlightsController;
use App\Http\Controllers\FlyerGeneratorController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\FooterManagerController;
use App\Http\Controllers\GuestsController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Backend\Operations\Activities\ActivityAdminController;
use App\Http\Controllers\Backend\Operations\Activities\ActivityGalleryAdminController;
use App\Http\Controllers\Backend\Operations\Hotels\HotelAdditionalChargeAdminController;
use App\Http\Controllers\Backend\Operations\Hotels\HotelAdminController;
use App\Http\Controllers\Backend\Operations\Hotels\HotelContractAdminController;
use App\Http\Controllers\Backend\Operations\Hotels\HotelGalleryAdminController;
use App\Http\Controllers\Backend\Operations\Hotels\HotelPromoAdminController;
use App\Http\Controllers\Backend\Operations\Hotels\HotelPackageAdminController;
use App\Http\Controllers\Backend\Operations\Hotels\HotelNormalPriceAdminController;
use App\Http\Controllers\Backend\Operations\Hotels\HotelRoomAdminController;
use App\Http\Controllers\Backend\Operations\Tours\TourAdminController;
use App\Http\Controllers\Backend\Operations\Tours\TourPriceAdminController;
use App\Http\Controllers\Backend\Operations\Transports\TransportAdminController;
use App\Http\Controllers\Backend\Operations\Transports\TransportGalleryAdminController;
use App\Http\Controllers\Backend\Operations\Transports\TransportPriceAdminController;
use App\Http\Controllers\TransportBrandController;
use App\Http\Controllers\TransportTypeController;
use App\Http\Controllers\HotelPromoController;
use App\Http\Controllers\HotelsAdminController;
use App\Http\Controllers\HotelsController;
use App\Http\Controllers\InvoiceAdminController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ManualBookController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrdersAdminController;
use App\Http\Controllers\OrderWeddingController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\PaymentConfirmationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoomViewController;
use App\Http\Controllers\SpksController;
use App\Http\Controllers\SpksDestinationsController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TermAndConditionController;
use App\Http\Controllers\TourPricesController;
use App\Http\Controllers\ToursController;
use App\Http\Controllers\TourPackageQuoteController;
use App\Http\Controllers\TransportManagementController;
use App\Http\Controllers\TransportsController;
use App\Http\Controllers\UsdRatesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VillasController;
use App\Http\Controllers\WeddingDinnerVenuesController;
use App\Http\Controllers\WeddingInvitationsController;
use App\Http\Controllers\WeddingLunchVenuesController;
use App\Http\Controllers\WeddingMenuController;
use App\Http\Controllers\WeddingPlannerController;
use App\Http\Controllers\WeddingReceptionVenuesController;
use App\Http\Controllers\WeddingsController;
use App\Http\Controllers\WeddingVenuesController;
use App\Http\Controllers\WhatsAppController;

use App\Http\Controllers\SpkReportController;
use App\Http\Controllers\SpkWhatsAppController;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

    // ---------------------------------------------------
    //                    FRONTEND
    // ---------------------------------------------------
    Route::get('/',[FrontEndController::class,'index'])->name('home');
    Route::get('/hotels',[FrontEndController::class,'hotels_service'])->name('view.hotels-service');
    Route::get('/activities',[FrontEndController::class,'activity_services'])->name('view.activities-service');
    Route::get('/activity/{code}', [FrontEndController::class, 'activity_detail'])->name('view.activity-public-detail');
    Route::get('/hotel/{code}', [FrontEndController::class, 'hotel_detail'])->where('code', '^(?!price-).+$')->name('view.hotel-detail');
    Route::post('/booking-code/remove', [FrontEndController::class, 'remove_booking_code'])->name('bookingcode.remove');
    Route::get('/hotel-{code}', function (Request $request, $code) {
        $parameters = ['code' => $code];

        if ($request->boolean('check_price')) {
            $parameters['check_price'] = 1;
        }
        return redirect()->route('view.hotel-detail', $parameters);

    })->where('code', '^(?!price-).+$')->name('view.hotel-detail-flyer');
    Route::get('/hotel/{code}/check-price', [HotelsController::class, 'checkPriceEntry'])->name('view.hotel-check-price');
    Route::get('/transportations',[FrontEndController::class,'transport_service'])->name('view.transports-service');
    Route::get('/tour-packages',[FrontEndController::class,'tour_package_services'])->name('view.tour-packages-service');
    Route::redirect('/tours', '/tour-packages')->name('view.tours');

    Route::get('/about-us',[HomeController::class,'about_us'])->name('about-us');
    Route::get('/contact-us',[HomeController::class,'contact_us'])->name('contact-us');
    Route::get('/services',[HomeController::class,'services'])->name('services');
    Route::get('/transportation/{id}', [HomeController::class, 'show_transport'])->name('transport.show');
    Route::get('/tour-package-service',[FrontEndController::class,'tour_package_services'])->name('tour-package-service');
    Route::get('/tour-package/{id}',[HomeController::class,'show_tour_package'])->name('tour-package.show');
    Route::post('/subscribe', [SubscriberController::class, 'store'])->name('subscribe.store');
    Route::get('/logout', function () {
        return Auth::check()
            ? redirect('/profile')
            : redirect()->route('login');
    });
    // ================================================================================================================================= DONE


    // ---------------------------------------------------
    //                   REVIEWS
    // ---------------------------------------------------
    Route::get('/create-review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/submit-review', [ReviewController::class, 'store'])->name('review.submit');


    // ---------------------------------------------------
    //                   AGENT PRE REGISTER
    // ---------------------------------------------------
    Route::get('/agent/register', [AgentRegistrationController::class, 'showForm'])->name('agent.register');
    Route::post('/agent-register', [AgentRegistrationController::class, 'submitForm'])->name('agent.register.submit');
    Route::get('/registration-mail', [AgentRegistrationController::class, 'test_view_email']);

    // ---------------------------------------------------
    //                    TRANSPORT MANAJEMEN
    // ---------------------------------------------------
    // Halaman checkin driver
    Route::get('/spks/checkin/{spkDestination}', [TransportManagementController::class, 'checkinPage'])->name('spks.checkin.page');
    // Proses checkin
    Route::post('/spks/checkin/{spkDestination}', [TransportManagementController::class, 'checkin'])->name('spks.checkin');
    Route::get('/checkin/{spkDestination}', [TransportManagementController::class, 'checkinPage'])->name('spk.checkin.page');
    Route::post('/checkin/{id}', [TransportManagementController::class, 'checkin'])->name('spk.checkin');
    Route::get('/spk/{id}/{spkNumber}', [TransportManagementController::class, 'show_spk'])->name('view.spk');
    Route::post('/driver/checkin/{id}', [SpksDestinationsController::class, 'driver_create_destination'])->name('driver.checkin');

    // ---------------------------------------------------
    //                    WA SERVER
    // ---------------------------------------------------
    Route::post('/spk/{id}/send-whatsapp', [WhatsAppController::class, 'send'])->name('spk.send.whatsapp');

    Route::post('/send-whatsapp-both', [WhatsAppController::class, 'send_wa_both'])->name('send.whatsapp-both');
    Route::post('/send-whatsapp-driver', [WhatsAppController::class, 'send_wa_driver'])->name('send.whatsapp-driver');
    Route::post('/send-whatsapp-operator', [WhatsAppController::class, 'send_wa_operator'])->name('send.whatsapp-operator');

    // Route::get('/spk-report/{id}', [WhatsAppController::class, 'spk_report'])->name('view.spk-report');

    Route::get('/test-wa', function () {
        return Http::timeout(5)->get("http://127.0.0.1:3000/status")->json();
    });

    // |--------------------------------------------------------------------------
    // | Public SPK Report
    // |--------------------------------------------------------------------------
    // Route::get('/spk-report/{spk}', [
    //     SpkReportController::class,
    //     'show',
    // ])->middleware('signed')->name('spks.public-report');
    Route::get('/spk-report/{token}', [
        SpkReportController::class,
        'show',
    ])->name('spks.public-report');

    // ---------------------------------------------------
    //                    TEST SYSTEM
    // ---------------------------------------------------
    Route::get('/email-confirmation/{id}',[OrdersAdminController::class,'test_email_confirmation']);
    Route::get('/test-order-contract/{id}',[OrdersAdminController::class,'test_contrat']);
    Route::get('calendar-event', [CalendarController::class, 'index'])->middleware(['auth','adminType']);
    Route::post('calendar-crud-ajax', [CalendarController::class, 'calendarEvents'])->middleware(['auth','adminType']);
    Route::get('/keycode', function () {return view('keycode');});
    // FORM WIZARD
    Route::get('form-wizard', function () {
        return view('wizard');
    });
    Route::get('/contract-inv', [OrdersAdminController::class, 'confirmation_order']);
    // ---------------------------------------------------

    Route::get('lang/{locale}',[LocalizationController::class,'changeLanguage'])->name('language.switch');
    // Route::get('/', function () {return view('/home');});
    // Route::get('/', function () {return redirect('/home');});
    Route::get('change-password', [ForgotPasswordController::class, 'forgetPassword'])->name('change.password.get');
    Route::get('forget-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('forget.password.get');
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('f-forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->middleware('throttle:5,1')->name('f-forget.password.post');
    Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->middleware('throttle:5,1')->name('reset.password.post');
    Route::get('/terms-and-conditions', [TermAndConditionController::class, 'terms_and_conditions'])->name('terms-and-conditions');

    Route::get('/privacy-policy', [TermAndConditionController::class, 'privacy_policy'])->name('privacy-policy');
    Route::get('/faq', [TermAndConditionController::class, 'faq'])->name('faq');
    Route::get('/help', [TermAndConditionController::class, 'faq'])->name('help');
    Route::get('/tour/{slug}',[ToursController::class,'view_tour_detail'])->name('view.tour-detail');
    Route::post('/tour/{tour}/quote', TourPackageQuoteController::class)->name('tour-package.quote');
    Auth::routes(['verify' => true]);
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');
    Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
    Route::middleware(['auth'])->group(function () {
        // Route::get('home', function () {return redirect('/profile');});
        Route::get('/profile', [ProfileController::class,'profile'])->name('profile');
        Route::get('/profile-{email}',[ProfileController::class,'users']);
        Route::put('/fupdate-profile/{id}',[UsersController::class,'func_update_profile']);
        Route::put('/fupdate-profileimg/{id}',[UsersController::class,'func_update_profileimg']);
        Route::put('/fupdate-password',[UsersController::class,'updatePassword'])->name('update-password');
    });
    Route::middleware(['auth','profile.complete','approve'])->group(function () {
        Route::post('/activity/{code}/quote', ActivityQuoteController::class)->name('activity.quote');
        Route::get('/activity/guest-list-template/{format}', ActivityGuestListTemplateController::class)
            ->whereIn('format', ['csv', 'xlsx'])
            ->name('activity.guest-list-template');
        Route::post('/activity/{code}/order', [OrderController::class, 'storeFrontendActivityOrder'])->name('view.activity-order.store');
        // Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard.index');

        // DEVELOPER | ADMINISTRATOR =========================================================================================================
        Route::middleware(['checkPosition:developer,administrator'])->prefix('admin')->name('admin.')->group(function () {
            // ---------------------------------------------------
            //                  COMPANY PROFILE
            // ---------------------------------------------------
            Route::get('/company-profile', [BusinessProfileController::class, 'edit'])->name('company-profile.edit');
            Route::put('/company-profile', [BusinessProfileController::class, 'update'])->name('company-profile.update');
            // ---------------------------------------------------
            //                   FOOTER MANAGER
            // ---------------------------------------------------
            Route::get('/footer-manager', [FooterManagerController::class, 'index'])->name('footer-manager.index');
            Route::put('/footer-manager/settings', [FooterManagerController::class, 'updateSettings'])->name('footer-manager.settings.update');
            Route::post('/footer-manager/links', [FooterManagerController::class, 'storeLink'])->name('footer-manager.links.store');
            Route::put('/footer-manager/links/{footerLink}', [FooterManagerController::class, 'updateLink'])->name('footer-manager.links.update');
            Route::delete('/footer-manager/links/{footerLink}', [FooterManagerController::class, 'destroyLink'])->name('footer-manager.links.destroy');
        });

        // DEVELOPER | ADMINISTRATOR | AUTHOR ================================================================================================
        Route::middleware(['checkPosition:developer,administrator,reservation,author'])->prefix('admin')->name('admin.')->group(function () {
            // ---------------------------------------------------
            //                     DEVELOPER
            // ---------------------------------------------------
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            // ---------------------------------------------------
            //                      AUTHOR
            // ---------------------------------------------------
            Route::get('/panel-main', [AdminPanelController::class, 'admin_panel_main'])->name('panel-main.view');
            Route::get('/panel',[AdminPanelController::class,'index'])->name('panel.index');
            // ---------------------------------------------------
            //                    RESERVATION
            // ---------------------------------------------------

            // ---------------------------------------------------
            //                      PARTNERS
            // ---------------------------------------------------
            Route::get('/partners',[PartnersController::class,'index'])->name('partners.index');
            Route::post('/fcreate-partner',[PartnersController::class,'store'])->name('partner.create');
            Route::put('/fupdate-partner/{id}',[PartnersController::class,'update'])->name('partner.edit');
            Route::put('/fremove-partner/{id}',[PartnersController::class,'destroy'])->name('partner.destroy');
            // ---------------------------------------------------
            //                       GUIDE
            // ---------------------------------------------------
            Route::get('/guides',[GuideController::class,'index'])->name('guides.index');
            Route::post('/fcreate-guide',[GuideController::class,'create'])->name('guide.create');
            Route::post('/fedit-guide/{id}',[GuideController::class,'edit'])->name('guide.edit');
            Route::delete('/fdestroy-guide/{id}',[GuideController::class,'destroy'])->name('guide.destroy');
            // ---------------------------------------------------
            //                       DRIVER
            // ---------------------------------------------------
            Route::get('/drivers',[DriversController::class,'index'])->name('drivers.index');
            Route::post('/fcreate-driver',[DriversController::class,'create'])->name('driver.create');
            Route::post('/fedit-driver/{id}',[DriversController::class,'edit'])->name('driver.edit');
            Route::delete('/fdestroy-driver/{id}',[DriversController::class,'destroy'])->name('driver.destroy');
            // ---------------------------------------------------
            //                     ACTIVITIES
            // ---------------------------------------------------
            Route::get('/activities',[ActivityAdminController::class,'index'])->name('activities.index');
            Route::get('/detail-activity/{id}',[ActivityAdminController::class,'show'])->name('activities.show');
            Route::get('/add-activity',[ActivityAdminController::class,'create'])->name('activities.create');
            Route::get('/edit-activity/{id}',[ActivityAdminController::class,'edit'])->name('activities.edit');
            Route::get('/edit-galery-activity/{id}',[ActivityGalleryAdminController::class,'edit'])->name('activities.gallery.edit');
            Route::put('/activities/{activity}/gallery',[ActivityGalleryAdminController::class, 'update_gallery'])->name('gallery-activities.update');
            Route::post('/fadd-activity',[ActivityAdminController::class,'store'])->name('activities.store');
            Route::put('/fupdate-activity/{id}',[ActivityAdminController::class,'update'])->name('activities.update');
            Route::delete('/remove-activity/{id}',[ActivityGalleryAdminController::class,'destroy'])->name('activities.destroy');
            Route::delete('/fdelete-activity-cover/{id}',[ActivityGalleryAdminController::class,'destroyCover'])->name('activities.cover.destroy');
            Route::delete('/fdelete-activity-img/{id}',[ActivityGalleryAdminController::class,'destroyImage'])->name('activities.images.destroy');

            Route::get('/activity-types', [ActivityAdminController::class, 'type_index'])->name('activity-types.index');
            Route::post('/activity-types', [ActivityAdminController::class, 'type_store'])->name('activity-types.store');
            Route::put('/activity-types/{activityType}', [ActivityAdminController::class, 'type_update'])->name('activity-types.update');
            Route::delete('/activity-types/{activityType}', [ActivityAdminController::class, 'type_destroy'])->name('activity-types.destroy');
            // ---------------------------------------------------
            //                       HOTELS
            // ---------------------------------------------------
            Route::get('/hotels',[HotelAdminController::class,'index'])->name('hotels.index');
            Route::post('/hotels/status-audit/refresh',[HotelAdminController::class,'refreshStatuses'])->name('hotels.status-audit.refresh');
            Route::get('/detail-hotel/{id}',[HotelAdminController::class,'show'])->name('hotels.show');
            Route::get('/edit-hotel/{id}',[HotelAdminController::class,'edit'])->name('hotels.edit');
            Route::get('/add-hotel',[HotelAdminController::class,'create'])->name('hotels.create');
            Route::get('/add-hotel-price/{id}',[HotelNormalPriceAdminController::class,'create'])->name('hotels.prices.create');
            Route::get('/add-hotel-promo/{id}',[HotelPromoAdminController::class,'create'])->name('hotels.promos.create');
            Route::get('/edit-galery-hotel/{id}',[HotelGalleryAdminController::class,'edit'])->name('hotels.gallery.edit');
            Route::post('/edit-galery-hotel/{id}/images',[HotelGalleryAdminController::class,'store'])->name('hotels.gallery.store');
            Route::post('/fadd-hotel',[HotelAdminController::class,'store'])->name('hotel.store');
            Route::post('/fadd-hotel-contract',[HotelContractAdminController::class,'store'])->name('hotels.contracts.store');
            Route::put('/fupdate-hotel-contract/{id}',[HotelContractAdminController::class,'update'])->name('hotels.contracts.update');
            Route::delete('/fdelete-hotel-cover/{id}',[HotelGalleryAdminController::class,'destroyCover'])->name('hotels.cover.destroy');
            Route::delete('/hotels/{hotel}/gallery-images/{image}',[HotelGalleryAdminController::class,'destroyImage'])->name('hotels.images.destroy');
            Route::delete('/fdelete-contract/{id}',[HotelContractAdminController::class,'destroy'])->name('hotels.contracts.destroy');
            Route::delete('/remove-hotel/{id}',[HotelAdminController::class,'destroy'])->name('hotels.destroy');
            // ---------------------------------------------------
            //                HOTELS OPTIONAL RATE
            // ---------------------------------------------------
            Route::post('/fadd-optionalrate',[HotelsAdminController::class,'func_add_optionalrate'])->name('hotels.optional_rate.add');
            Route::put('/fupdate-optionalrate/{id}',[HotelsAdminController::class,'func_edit_optionalrate'])->name('hotels.optional_rate.update');
            Route::delete('/fdelete-optionalrate/{id}',[HotelsAdminController::class,'delete_optionalrate'])->name('hotels.optional_rate.delete');
            // ---------------------------------------------------
            //                    HOTELS ROOM
            // ---------------------------------------------------
            Route::get('/add-room/{id}',[HotelRoomAdminController::class,'create'])->name('hotels.room.create');
            Route::get('/edit-room/{id}',[HotelRoomAdminController::class,'edit'])->name('hotels.room.edit');
            Route::post('/fadd-room',[HotelRoomAdminController::class,'store'])->name('hotels.room.store');
            Route::put('/fedit-room/{id}',[HotelRoomAdminController::class,'update'])->name('hotels.room.update');
            Route::patch('/hotel-rooms/{room}/status',[HotelRoomAdminController::class,'updateStatus'])->name('hotels.room.status.update');
            Route::delete('/fdelete-room/{id}',[HotelRoomAdminController::class,'destroy'])->name('hotels.room.delete');
            Route::get('/autocomplete/room-view', [RoomViewController::class, 'autocomplete'])->name('autocomplate.hotels.room_view');
            Route::get('/autocomplete/bed-type', [BedTypeController::class, 'autocomplete'])->name('autocomplate.hotels.room.bed_type');

            // ---------------------------------------------------
            //                 TRANSPORT MANAGEMENT
            // ---------------------------------------------------
            Route::get('/distance/{lat1}/{lng1}/{lat2}/{lng2}', [MapController::class, 'getDistance']);
            Route::get('/transport-management', [SpksController::class, 'index'])->name('transport-management.index');
            Route::get('/spks/{id}', [SpksController::class, 'show'])->name('detail-spk');
            Route::post('/spks/store', [SpksController::class, 'store'])->name('spks.store');
            Route::post('/fadd-transport-reservation', [ReservationController::class, 'addReservation'])->name('transport-reservation.add');

            Route::post('/spks/generate', [SpksController::class, 'generate'])->name('spks.generate');
            Route::delete('/spks/destroy/{id}', [SpksController::class, 'destroy'])->name('spks.destroy');
            Route::post('/spks/{spk}/destinations', [SpksController::class, 'store'])->name('spks.destinations.store');
            Route::post('/spks/fupdate-spk/{id}', [SpksController::class, 'func_update_spk'])->name('spk.update');
            Route::post('/reservation/fupdate-reservation/{id}', [ReservationController::class, 'func_update_transport_management_reservation'])->name('transport-management-reservation.update');

            Route::post('/spks/fadd-spk-destination/{id}', [SpksController::class, 'func_add_spk_destination'])->name('spk-destinations.add');
            Route::post('/spks/fupdate-spk-destination/{id}', [SpksController::class, 'func_update_spk_destination'])->name('spk-destinations.update');
            Route::delete('/spks/fdelete-spk-destination/{id}', [SpksController::class, 'func_delete_spk_destination'])->name('spk-destination.delete');
        });

        // DEVELOPER | ADMINISTRATOR | RESERVATION | AUTHOR ==================================================================================
        Route::middleware(['checkPosition:developer,administrator,reservation,author'])->prefix('admin')->name('admin.')->group(function () {
            // ---------------------------------------------------
            //                   HOTELS PACKAGE
            // ---------------------------------------------------
            Route::get('/add-hotel-package/{id}',[HotelPackageAdminController::class,'create'])->name('hotels.packages.create');
            Route::get('/edit-hotel-package/{id}',[HotelPackageAdminController::class,'edit'])->name('hotels.packages.edit');
            Route::post('/fadd-package',[HotelPackageAdminController::class,'store'])->name('hotels.packages.store');
            Route::put('/fedit-package/{id}',[HotelPackageAdminController::class,'update'])->name('hotels.packages.update');
            Route::patch('/hotel-packages/{package}/status',[HotelPackageAdminController::class,'updateStatus'])->name('hotels.packages.status.update');
            Route::delete('/delete-package/{id}',[HotelPackageAdminController::class, 'destroy'])->name('hotels.packages.destroy');
            // ---------------------------------------------------
            //                HOTEL ADDITIONAL CHARGE
            // ---------------------------------------------------
            Route::delete('/fdelete-additional-charge/{id}',[HotelAdditionalChargeAdminController::class,'destroy'])->name('hotels.additional-charges.destroy');
            Route::post('/fadd-additional-charge',[HotelAdditionalChargeAdminController::class,'store'])->name('hotels.additional-charges.store');
            Route::put('/fupdate-additional-charge/{id}',[HotelAdditionalChargeAdminController::class,'update'])->name('hotels.additional-charges.update');
        });

        Route::middleware(['checkPosition:developer,administrator,author'])->group(function () {
            Route::get('/users',[UsersController::class,'index']);
            Route::get('/user-detail/{id}',[UsersController::class,'userdetail']);
            Route::post('/fadd-service',[AdminPanelController::class,'func_add_service'])->name('f-add-service');
            Route::put('/fdisable-service/{id}',[AdminPanelController::class,'func_disable_service'])->name('f-disable-service');
            Route::put('/fedit-service/{id}',[AdminPanelController::class,'func_edit_service'])->name('f-edit-service');
            Route::put('/fenable-service/{id}',[AdminPanelController::class,'func_enable_service'])->name('f-enable-service');
            Route::delete('/fremove-service/{id}',[AdminPanelController::class,'func_remove_service'])->name('f-remove-service');
            Route::put('/admin-panel/registration-access',[AdminPanelController::class,'updateRegistrationAccess'])->name('admin-panel.registration-access.update');

            Route::get('/dashboard/hotel-price-chart', [DashboardController::class, 'hotelPriceChart']);


            // ---------------------------------------------------
            //                    REGISTER NOTIFICATION
            // ---------------------------------------------------
            Route::get('/admin/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications.index');
            Route::get('/admin/agents', [AgentController::class, 'index'])->name('admin.agents.index');
            Route::get('/admin/agents/{id}', [AgentController::class, 'show'])->name('admin.agents.show');
            Route::patch('/admin/agents/{id}/verify', [AgentController::class, 'verify'])->name('admin.agents.verify');

            // ---------------------------------------------------
            //                   BANK ACCOUNT
            // ---------------------------------------------------
            Route::put('/fadd-bank-account',[BankAccountController::class,'func_add_bank_account']);
            Route::put('/fupdate-bank-account/{id}',[BankAccountController::class,'func_update_bank_account']);
            Route::delete('/delete-bank-account/{id}',[BankAccountController::class,'destroy_bank_account']);

            /// ---------------------------------------------------
            //                     CURRENCY
            // ---------------------------------------------------
            Route::get('/currency',[UsdRatesController::class,'index'])->name('currency');
            Route::post('/currency/refresh-rates',[UsdRatesController::class,'refreshRates'])->name('admin.currency.refresh-rates');
            Route::put('/update-usdrates/{id}',[UsdRatesController::class,'func_update_usdrates'])->name('f-update-usd-rates');
            Route::put('/update-cnyrates/{id}',[UsdRatesController::class,'func_update_cnyrates'])->name('f-update-cny-rates');
            Route::put('/update-twdrates/{id}',[UsdRatesController::class,'func_update_twdrates'])->name('f-update-twd-rates');

            // ---------------------------------------------------
            //                       EMAIL
            // ---------------------------------------------------
            Route::get('/send-email-approval', [MailController::class, 'sendEmailApproval'])->name('send.email-approval');
            // ---------------------------------------------------
            //                    USER MANAGER
            // ---------------------------------------------------
            Route::get('/user-manager', [UsersController::class, 'user_manager'])->name('user-manager');
            Route::post('/create-user',[UsersController::class,'func_create_user'])->name('create-user');
            Route::put('/fedit-user/{id}',[UsersController::class,'func_edit_user'])->name('edit-user');
            Route::put('/fapprove-user/{id}',[UsersController::class,'func_approve_user'])->name('approve-user');
            Route::put('/fverified-user/{id}',[UsersController::class,'func_verified_user'])->name('verified-user');
            Route::delete('/fremove-user/{id}', [UsersController::class, 'destroy'])->name('remove-user');
            // ---------------------------------------------------
            //                      WEDDING
            // ---------------------------------------------------
            Route::get('/getCeremonyDecorations', [WeddingsController::class, 'getCeremonyDecorations']);
            Route::get('/getReceptionDecorations', [WeddingsController::class, 'getReceptionDecorations']);

            // ---------------------------------------------------
            //                TERMS AND CONDITIONS
            // ---------------------------------------------------
            Route::get('/term-and-condition',[TermAndConditionController::class,'index'])->name('view.term-and-condition');
            Route::put('/fupdate-policy/{id}',[TermAndConditionController::class,'func_edit_policy'])->name('term-and-condition.policy.update');
            Route::put('/fadd-policy',[TermAndConditionController::class,'func_add_policy'])->name('term-and-condition.policy.store');
            Route::delete('/fdestroy-policy/{id}',[TermAndConditionController::class,'fdestroy_policy'])->name('term-and-condition.policy.destroy');

            // ---------------------------------------------------
            //                     CURRENCY
            // ---------------------------------------------------
            Route::put('/update-tax/{id}',[UsdRatesController::class,'func_update_tax'])->name('f-update-tax');
            Route::put('/add-tax',[UsdRatesController::class,'func_add_tax'])->name('f-add-tax');
            // ---------------------------------------------------
            //                      HOTELS
            // ---------------------------------------------------
            Route::put('/fupdate-hotel/{id}',[HotelAdminController::class,'update'])->name('func.hotel.edit');
            // ---------------------------------------------------
            //                     EXTRA BED
            // ---------------------------------------------------
            Route::post('/fadd-e-b',[ExtraBedController::class,'func_add_extra_bed'])->name('func.extrabed.add');
            Route::put('/fedit-e-b/{id}',[ExtraBedController::class,'fedit_extra_bed'])->name('func.extrabed.edit');
            Route::delete('/fdelete-e-b/{id}',[ExtraBedController::class,'fdelete_extra_bed'])->name('func.extrabed.delete');

            // ---------------------------------------------------
            //                   HOTELS PRICES
            // ---------------------------------------------------
            Route::post('/fadd-price',[HotelNormalPriceAdminController::class,'store'])->name('admin.hotels.normal-prices.store');
            Route::put('/fedit-price/{id}',[HotelNormalPriceAdminController::class,'update'])->name('admin.hotels.normal-prices.update');
            Route::delete('/delete-price/{id}',[HotelNormalPriceAdminController::class,'destroy'])->name('admin.hotels.normal-prices.destroy');
            // ---------------------------------------------------
            //                    HOTELS PROMO
            // ---------------------------------------------------
            Route::get('/edit-hotel-promo/{id}',[HotelPromoAdminController::class,'edit'])->name('admin.hotels.promos.edit');
            Route::post('/fadd-promo',[HotelPromoAdminController::class,'store'])->name('admin.hotels.promos.store');
            Route::put('/fedit-promo/{id}',[HotelPromoAdminController::class,'update'])->name('admin.hotels.promos.update');
            Route::patch('/hotel-promos/{promo}/status',[HotelPromoAdminController::class,'updateStatus'])->name('admin.hotels.promos.status.update');
            Route::delete('/delete-promo/{id}',[HotelPromoAdminController::class, 'destroy'])->name('admin.hotels.promos.destroy');


            // ---------------------------------------------------
            //                 ADDITIONAL SERVICE
            // ---------------------------------------------------
            Route::post('/add-additional-service',[ReservationController::class,'func_add_additional_service']);
            Route::put('/update-additional-service/{id}',[ReservationController::class,'func_update_additional_service']);
            Route::delete('/delete-additional-service/{id}',[ReservationController::class,'destroy_additional_service']);


            // ---------------------------------------------------
            //                     TRANSPORTS
            // ---------------------------------------------------
            Route::get('/add-transport',[TransportAdminController::class,'create'])->name('admin.transports.create');
            Route::get('/edit-transport/{id}',[TransportAdminController::class,'edit'])->name('admin.transports.edit');
            Route::get('/edit-galery-transport/{id}',[TransportGalleryAdminController::class,'edit'])->name('admin.transports.gallery.edit');
            Route::post('/fadd-transport',[TransportAdminController::class,'store'])->name('admin.transports.store');
            Route::post('/fadd-transport-price',[TransportPriceAdminController::class,'store'])->name('admin.transports.prices.store');
            Route::put('/fupdate-transport/{id}',[TransportAdminController::class,'update'])->name('admin.transports.update');
            Route::put('/fupdate-transport-price/{id}',[TransportPriceAdminController::class,'update'])->name('admin.transports.prices.update');
            Route::put('/delete-transport/{id}',[TransportAdminController::class,'destroy'])->name('admin.transports.destroy');
            Route::delete('/fdelete-transport-price/{id}',[TransportPriceAdminController::class,'destroy'])->name('admin.transports.prices.destroy');
            Route::delete('/fdelete-transport-cover/{id}',[TransportGalleryAdminController::class,'destroyCover'])->name('admin.transports.cover.destroy');
            Route::delete('/fdelete-transport-img/{id}',[TransportGalleryAdminController::class,'destroyImage'])->name('admin.transports.images.destroy');

            

            // ---------------------------------------------------
            //                       EMAIL
            // ---------------------------------------------------
            Route::get('/promo-email-blast', [EmailBlastsController::class, 'index']);
            Route::get('/send-promo-to-agent/{id}', [EmailBlastsController::class, 'send_email_promo']);
            Route::get('/send-promo-to-specific-agent/{id}', [EmailBlastsController::class, 'send_specific_email_promo']);
            Route::post('/fsend-promo-email-to-agent/{id}', [EmailBlastsController::class, 'func_send_email_promo'])->name('send.promo.email');
            Route::post('/fsend-promo-specific-email-to-agent/{id}', [EmailBlastsController::class, 'func_send_specific_email_promo'])->name('send.promo.specific.email');
            Route::get('/send-promo-to-agent/{id}', [EmailBlastsController::class, 'send_email_promo']);
            // ---------------------------------------------------
            //                    BOOKING CODE
            // ---------------------------------------------------
            Route::put('/fadd-booking-code',[BookingCodeController::class,'create'])->name('fadd-booking-code');
            Route::put('/fupdate-bookingcode/{id}',[BookingCodeController::class,'func_update_bookingcode'])->name('f-update-booking-code');
            Route::put('/fremove-bookingcode/{id}',[BookingCodeController::class,'func_remove_bookingcode'])->name('f-remove-booking-code');
            // ---------------------------------------------------
            //                      PROMOTION
            // ---------------------------------------------------
            Route::post('/fadd-promotion',[PromotionController::class,'create'])->name('fadd-promotion');
            Route::post('/fupdate-promotion/{id}',[PromotionController::class,'update'])->name('fupdate-promotion');
            Route::post('/fremove-promotion/{id}',[PromotionController::class,'destroy'])->name('fremove-promotion');
        });
        // ========================================================================================================================================> (WEDDING AUTHOR)
        Route::middleware(['checkPosition:developer,administrator,weddingAuthor'])->group(function () {
            // ---------------------------------------------------
            //                       VENDORS
            // ---------------------------------------------------
            Route::post('/fadd-vendor',[VendorController::class,'func_add_vendor']);
            Route::post('/fadd-vendor-package',[VendorController::class,'func_add_package']);
            Route::put('/fupdate-vendor/{id}',[VendorController::class,'func_update_vendor']);
            Route::put('/fupdate-vendor-package/{id}',[VendorController::class,'func_update_vendor_package']);
            Route::put('/fremove-vendor-package/{id}',[VendorController::class,'func_remove_package']);
            Route::put('/fremove-vendor/{id}',[VendorController::class,'func_remove_vendor']);
            // ---------------------------------------------------
            //                      WEDDING
            // ---------------------------------------------------
            Route::get('/weddings-edit/{id}',[WeddingsController::class,'view_edit_wedding']);
            Route::get('/weddings-add',[WeddingsController::class,'view_add_wedding']);
            Route::put('/fupdate-weddings/{id}',[WeddingsController::class,'func_update_wedding']);
            Route::put('/fadd-wedding-fixed-service/{id}',[WeddingsController::class,'func_add_wedding_fixed_service']);
            Route::put('/fadd-wedding-other/{id}',[WeddingsController::class,'func_add_wedding_other']);
            Route::put('/fadd-wedding-price/{id}',[WeddingsController::class,'func_add_wedding_price']);
            Route::delete('/fweddings-remove/{id}',[WeddingsController::class,'destroy_wedding']);
            Route::put('/fupdate-wedding-info/{id}',[WeddingsController::class,'func_edit_wedding_info']);
            Route::put('/fupdate-entrance-fee/{id}',[WeddingsController::class,'func_edit_entrance_fee']);
            // ---------------------------------------------------
            //                  WEDDING PACKAGE
            // ---------------------------------------------------
            Route::get('/add-wedding-package/{id}',[WeddingsController::class,'view_add_wedding_package']);
            Route::get('/edit-wedding-package/{id}',[WeddingsController::class,'view_edit_wedding_package']);
            Route::post('/fadd-wedding-package/{id}',[WeddingsController::class,'func_add_wedding_package']);
            Route::put('/factivate-wedding-package/{id}',[WeddingsController::class,'func_activate_wedding_package']);
            Route::put('/fdraft-wedding-package/{id}',[WeddingsController::class,'func_draft_wedding_package']);
            Route::put('/fdrafted-wedding-package/{id}',[WeddingsController::class,'func_drafted_wedding_package']);
            Route::put('/fremove-wedding-package/{id}',[WeddingsController::class,'func_removed_wedding_package']);
            Route::put('/fedit-wedding-package/{id}',[WeddingsController::class,'func_edit_wedding_package']);
            Route::delete('/fdelete-wedding-package/{id}',[WeddingsController::class,'destroy_wedding']);
            // ---------------------------------------------------
            //                   WEDDING VENUE
            // ---------------------------------------------------
            Route::put('/fadd-wedding-venue/{id}',[WeddingsController::class,'func_add_wedding_venue']);
            Route::put('/fadd-wedding-dinner-venue/{id}',[WeddingsController::class,'func_add_wedding_dinner_venue']);
            // ---------------------------------------------------
            //                  RECEPTION VENUE
            // ---------------------------------------------------
            Route::post('/fcreate-new-reception-venue/{id}',[WeddingReceptionVenuesController::class,'func_add_reception_venue']);
            Route::get('/update-reception-venue/{id}',[WeddingReceptionVenuesController::class,'view_edit_reception_venue']);
            Route::put('/fupdate-reception-venue/{id}',[WeddingReceptionVenuesController::class,'func_edit_reception_venue']);
            Route::put('/factivate-reception-venue/{id}',[WeddingReceptionVenuesController::class,'func_activate_reception_venue']);
            Route::put('/fdeactivate-reception-venue/{id}',[WeddingReceptionVenuesController::class,'func_deactivate_reception_venue']);
            Route::delete('/fdelete-wedding-reception-venue/{id}',[WeddingReceptionVenuesController::class,'destroy_wedding_reception_venue']);
            // ---------------------------------------------------
            //                  CEREMONY VENUE
            // ---------------------------------------------------
            Route::get('/add-ceremony-venue/{id}',[WeddingsController::class,'view_add_wedding_venue']);
            Route::post('/fadd-wedding-venue',[WeddingsController::class,'func_add_wedding_venue']);
            Route::get('/edit-wedding-venue/{id}',[WeddingsController::class,'view_edit_wedding_venue']);
            Route::put('/fedit-wedding-venue/{id}',[WeddingsController::class,'func_edit_wedding_venue']);
            Route::delete('/fdelete-wedding-venue/{id}',[WeddingsController::class,'destroy_wedding_venue']);
            Route::put('/factivate-ceremony-venue/{id}',[WeddingVenuesController::class,'func_activate_ceremony_venue']);
            Route::put('/fdeactivate-ceremony-venue/{id}',[WeddingVenuesController::class,'func_deactivate_ceremony_venue']);
            // ---------------------------------------------------
            //                 WEDDING DECORATION
            // ---------------------------------------------------
            Route::put('/fadd-wedding-decoration/{id}',[WeddingsController::class,'func_add_wedding_decoration']);
            Route::get('/add-decoration-ceremony-venue/{id}',[WeddingsController::class,'view_add_decoration_ceremony_venue']);
            Route::get('/edit-decoration-ceremony-venue/{id}',[WeddingsController::class,'view_edit_decoration_ceremony_venue']);
            Route::post('/fadd-decoration-ceremony-venue/{id}',[WeddingsController::class,'func_add_decoration_ceremony_venue']);
            Route::put('/fedit-decoration-ceremony-venue/{id}',[WeddingsController::class,'func_edit_decoration_ceremony_venue']);
            Route::put('/fsave-to-draft-decoration-ceremony-venue/{id}',[WeddingsController::class,'func_save_to_draft_decoration_ceremony_venue']);
            Route::put('/fsave-to-active-decoration-ceremony-venue/{id}',[WeddingsController::class,'func_save_to_active_decoration_ceremony_venue']);
            Route::delete('/fdelete-decoration-ceremony-venue/{id}',[WeddingsController::class,'destroy_decoration_ceremony_venue']);
            // ---------------------------------------------------
            //                  WEDDING MAKEUP
            // ---------------------------------------------------
            Route::put('/fadd-wedding-makeup/{id}',[WeddingsController::class,'func_add_wedding_makeup']);
            // ---------------------------------------------------
            //               WEDDING ENTERTAINMENT
            // ---------------------------------------------------
            Route::put('/fadd-wedding-entertainment/{id}',[WeddingsController::class,'func_add_wedding_entertainment']);
            // ---------------------------------------------------
            //               WEDDING DOCUMENTATION
            // ---------------------------------------------------
            Route::put('/fadd-wedding-documentation/{id}',[WeddingsController::class,'func_add_wedding_documentation']);
            // ---------------------------------------------------
            //              WEDDING SITES AND VILLAS
            // ---------------------------------------------------
            Route::put('/fadd-wedding-rooms/{id}',[WeddingsController::class,'func_add_wedding_room']);
            // ---------------------------------------------------
            //                 WEDDING TRANSPORT
            // ---------------------------------------------------
            Route::put('/fadd-wedding-transports/{id}',[WeddingsController::class,'func_add_wedding_transport']);
            // ---------------------------------------------------
            //                   WEDDING FLIGHT
            // ---------------------------------------------------
            Route::put('/func-update-order-wedding-flight-admin/{id}',[OrderWeddingController::class,'func_update_order_wedding_flight_admin']);
            // ---------------------------------------------------
            //                 WEDDING CONTRACT
            // ---------------------------------------------------
            Route::post('/fadd-wedding-contract',[WeddingsController::class,'func_add_wedding_contract']);
            Route::put('/fupdate-wedding-contract/{id}',[WeddingsController::class,'func_edit_wedding_contract']);
            Route::delete('/fdelete-wedding-contract/{id}',[WeddingsController::class,'delete_wedding_contract']);
            // ---------------------------------------------------
            //                 WEDDING LUNCH VENUE
            // ---------------------------------------------------
            Route::post('/fcreate-new-lunch-venue/{id}',[WeddingLunchVenuesController::class,'func_add_lunch_venue']);
            Route::get('/update-lunch-venue/{id}',[WeddingLunchVenuesController::class,'view_edit_lunch_venue']);
            Route::put('/fupdate-lunch-venue/{id}',[WeddingLunchVenuesController::class,'func_edit_lunch_venue']);
            Route::put('/factivate-lunch-venue/{id}',[WeddingLunchVenuesController::class,'func_activate_lunch_venue']);
            Route::put('/fdeactivate-lunch-venue/{id}',[WeddingLunchVenuesController::class,'func_deactivate_lunch_venue']);
            Route::delete('/fdelete-wedding-lunch-venue/{id}',[WeddingLunchVenuesController::class,'destroy_wedding_lunch_venue']);
            // ---------------------------------------------------
            //                 WEDDING DINNER VENUE
            // ---------------------------------------------------
            Route::get('/add-dinner-venue/{id}',[WeddingDinnerVenuesController::class,'view_add_dinner_venue']);
            Route::post('/fcreate-new-dinner-venue/{id}',[WeddingDinnerVenuesController::class,'func_add_dinner_venue']);
            Route::get('/update-dinner-venue/{id}',[WeddingDinnerVenuesController::class,'view_edit_dinner_venue']);
            Route::put('/fupdate-dinner-venue/{id}',[WeddingDinnerVenuesController::class,'func_edit_dinner_venue']);
            Route::put('/factivate-dinner-venue/{id}',[WeddingDinnerVenuesController::class,'func_activate_dinner_venue']);
            Route::put('/fdeactivate-dinner-venue/{id}',[WeddingDinnerVenuesController::class,'func_deactivate_dinner_venue']);
            Route::delete('/fdelete-dinner-venue/{id}',[WeddingDinnerVenuesController::class,'destroy_dinner_venue']);
            // ---------------------------------------------------
            //                WEDDING DINNER PACKAGE
            // ---------------------------------------------------
            // Route::get('/vadd-dinner-package/{id}',[WeddingDinnerController::class,'view_add_dinner_package']);
            // Route::post('/fcreate-dinner-package/{id}',[WeddingDinnerController::class,'func_add_dinner_package']);
            // Route::get('/update-dinner-package/{id}',[WeddingDinnerController::class,'view_update_dinner_package']);
            // Route::put('/fupdate-dinner-package/{id}',[WeddingDinnerController::class,'func_update_dinner_package']);
            // ---------------------------------------------------
            //               WEDDING FOOD AND BEVERAGE
            // ---------------------------------------------------
            // Route::get('/vadd-food-and-beverage/{id}',[WeddingMenuController::class,'view_add_food_and_beverage']);
            // Route::post('/fadd-food-and-beverage/{id}',[WeddingMenuController::class,'func_add_food_and_beverage']);
        });
        // ========================================================================================================================================> (WEDDING SALES)
        Route::middleware(['checkPosition:developer,administrator'])->group(function () {
        });
        // ========================================================================================================================================> (RESERVATION)
        Route::middleware(['checkPosition:developer,administrator,reservation'])->group(function () {

            

            // ---------------------------------------------------
            //                       REVIEWS TOURS
            // ---------------------------------------------------
            Route::get('/admin/reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
            Route::patch('/admin/reviews/{review}/status', [ReviewController::class, 'updateStatus'])->name('admin.reviews.updateStatus');
            Route::delete('/admin/reviews/{review}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');
            Route::get('/generate-review-link', [ReviewController::class, 'showForm'])->name('view.generate-review-link');
            Route::post('/generate-review-link', [ReviewController::class, 'generate'])->name('generate.review-link');

            // ---------------------------------------------------
            //                       REVIEWS WEDDING
            // ---------------------------------------------------
            Route::get('/admin/wedding-reviews', [ReviewController::class, 'wedding_review_index'])->name('admin.wedding-reviews.index');
            Route::patch('/admin/wedding-reviews/{wedding_review}/status', [ReviewController::class, 'updateWeddingStatus'])->name('admin.wedding-reviews.updateStatus');
            Route::get('/generate-wedding-review-link', [ReviewController::class, 'showWeddingForm'])->name('view.generate-wedding-review-link');
            Route::post('/generate-wedding-review-link', [ReviewController::class, 'generate_wedding_review_link'])->name('generate.wedding-review-link');
            Route::get('/wedding-reviews/print/{bookingCode}', [ReviewController::class, 'print_wedding_reviews'])->name('wedding-reviews.print');
            // ---------------------------------------------------
            //                       ORDERS
            // ---------------------------------------------------
            Route::get('/orders-admin',[OrdersAdminController::class,'index'])->name('admin.order.index');
            Route::get('/orders-admin/{id}',[OrdersAdminController::class,'view_order_admin_detail'])->name('admin.order.show');
            Route::get('/orders-admin-{id}', fn ($id) => redirect()->route('admin.order.show', ['id' => $id]))
                ->whereNumber('id')
                ->name('admin.order.show.legacy');
            Route::put('/fupdate-confirmation-number/{id}',[OrdersAdminController::class,'func_update_confirmation_number'])
                ->whereNumber('id')
                ->name('admin.orders.confirmation-number.update');
            Route::put('/fupdate-confirmation-number-{id}',[OrdersAdminController::class,'func_update_confirmation_number'])
                ->whereNumber('id')
                ->name('admin.orders.confirmation-number.update.legacy');
            Route::post('/fadd-order-note/{id}',[OrdersAdminController::class,'func_add_order_note'])
                ->whereNumber('id')
                ->name('admin.orders.notes.store');
            Route::post('/fadd-order-note-{id}',[OrdersAdminController::class,'func_add_order_note'])
                ->whereNumber('id')
                ->name('admin.orders.notes.store.legacy');
            Route::put('/fsend-confirmation/{id}',[OrdersAdminController::class,'func_send_confirmation'])->whereNumber('id')->name('admin.orders.confirmation.send');
            Route::put('/fsend-confirmation-{id}',[OrdersAdminController::class,'func_send_confirmation'])->whereNumber('id')->name('admin.orders.confirmation.send.legacy');
            Route::put('/fresend-confirmation-order/{id}',[OrdersAdminController::class,'resend_confirmation_order'])->whereNumber('id')->name('admin.orders.confirmation.resend');
            Route::put('/fresend-confirmation-order-{id}',[OrdersAdminController::class,'resend_confirmation_order'])->whereNumber('id')->name('admin.orders.confirmation.resend.legacy');
            Route::put('/fgenerate-invoice/{id}',[OrdersAdminController::class,'fgenerate_invoice'])->name('admin.orders.invoice.generate');
            Route::put('/fregenerate-invoice-pdf/{order}',[OrdersAdminController::class,'fregenerate_invoice_pdf'])->whereNumber('order')->name('admin.orders.invoice.regenerate');
            Route::put('/fsend-approval-email/{id}',[OrdersAdminController::class,'fsend_approval_email'])->name('admin.orders.approval-email.send');
            Route::put('/fedit-confirmation-order/{id}',[OrdersAdminController::class,'func_edit_confirmation_order']);
            Route::put('/fadd-confirmation-order/{id}',[OrdersAdminController::class,'func_add_confirmation_order']);
            Route::put('/factivate-order/{id}',[OrdersAdminController::class,'func_activate_order'])->name('admin.orders.workflow.activate');
            Route::get('/add-optional-rate-order/{id}',[OrdersAdminController::class,'add_optional_rate_order'])->name('view.add-optional-rate-order');
            Route::post('/fadmin-add-optional-service-order/{id}',[OrdersAdminController::class,'func_add_optional_service_order'])->name('func.admin-add-optional-service-order');
            Route::put('/fadmin-update-optional-service-order/{id}',[OrdersAdminController::class,'func_update_optional_service_order'])->name('func.admin-update-optional-service-order');
            Route::get('/edit-additional-services/{id}',[OrdersAdminController::class,'edit_additional_services']);
            Route::put('/fedit-additional-services/{id}',[OrdersAdminController::class,'func_edit_additional_services']);
            Route::get('/edit-airport-shuttle/{id}',[OrdersAdminController::class,'edit_airport_shuttle']);
            Route::post('/fadd-airport-shuttle',[OrdersAdminController::class,'func_add_airport_shuttle']);
            Route::put('/fedit-airport-shuttles/{id}',[OrdersAdminController::class,'func_edit_airport_shuttle']);
            Route::put('/fupdate-pickup-dropoff/{id}',[OrdersAdminController::class,'func_update_pickup_dropoff']);
            Route::put('/fupdate-flight/{id}',[OrdersAdminController::class,'func_update_flight']);
            Route::get('/admin-edit-order-room/{id}',[OrdersAdminController::class,'admin_edit_order_room']);
            Route::put('/fadd-guide-order/{id}',[OrdersAdminController::class,'func_add_guide_order']);
            Route::put('/fedit-guide-order/{id}',[OrdersAdminController::class,'func_edit_guide_order']);
            Route::put('/fdelete-guide-order/{id}',[OrdersAdminController::class,'func_delete_guide_order']);
            Route::put('/fadd-driver-order/{id}',[OrdersAdminController::class,'func_add_driver_order']);
            Route::put('/fedit-driver-order/{id}',[OrdersAdminController::class,'func_edit_driver_order']);
            Route::put('/fdelete-driver-order/{id}',[OrdersAdminController::class,'func_delete_driver_order']);
            Route::put('/fadmin-update-order/{id}',[OrdersAdminController::class,'fadmin_update_order'])->name('func.order-admin.update');
            Route::put('/farchive-order/{id}',[OrdersAdminController::class,'func_archive_order'])
                ->whereNumber('id')
                ->name('admin.orders.workflow.archive');
            Route::put('/fupdate-order-invalid/{id}',[OrdersAdminController::class,'func_update_order_invalid']);
            Route::put('/fupdate-order-rejected/{id}',[OrdersAdminController::class,'func_update_order_rejected']);
            Route::put('/fupdate-order-discounts/{id}',[OrdersAdminController::class,'func_update_order_discounts'])->name('func.admin-update-order-dicounts');

            Route::put('/ffinalization-order/{id}',[OrdersAdminController::class,'func_finalization_order'])->name('func.admin-finalization-order');
            Route::delete('/optional-rate-order/{id}', [OrdersAdminController::class, 'remove_optional_rate_order'])->name('optional-rate-order.destroy');

            // ---------------------------------------------------
            //                    ORDER WEDDING
            // ---------------------------------------------------
            Route::post('/fadd-order-wedding-note/{id}',[OrdersAdminController::class,'func_add_order_wedding_note']);
            Route::get('/update-wedding-service/{id}',[OrdersAdminController::class, 'view_update_wedding_service']);
            Route::put('/fupdate-order-wedding-venue/{id}',[OrdersAdminController::class,'func_update_order_wedding_venue']);
            Route::put('/fupdate-order-wedding-room/{id}',[OrdersAdminController::class,'func_update_order_wedding_room']);
            Route::put('/fupdate-order-wedding-makeup/{id}',[OrdersAdminController::class,'func_update_order_wedding_makeup']);
            Route::put('/fupdate-order-wedding-decoration/{id}',[OrdersAdminController::class,'func_update_order_wedding_decoration']);
            Route::put('/fupdate-order-wedding-dinner_venue/{id}',[OrdersAdminController::class,'func_update_order_wedding_dinner_venue']);
            Route::put('/fupdate-order-wedding-entertainment/{id}',[OrdersAdminController::class,'func_update_order_wedding_entertainment']);
            Route::put('/fupdate-order-wedding-documentation/{id}',[OrdersAdminController::class,'func_update_order_wedding_documentation']);
            Route::put('/fupdate-order-wedding-transport/{id}',[OrdersAdminController::class,'func_update_order_wedding_transport']);
            Route::put('/fupdate-order-wedding-other/{id}',[OrdersAdminController::class,'func_update_order_wedding_other']);
            Route::put('/func-final-order/{id}',[OrdersAdminController::class,'func_final_wedding_order']);
            Route::put('/fremove-order-discounts/{id}',[OrdersAdminController::class,'func_remove_order_discounts']);
            Route::put('/fadmin-update-order-room/{id}',[OrdersAdminController::class,'func_admin_update_order_room'])->name('func.admin-update-order-room');
            Route::put('/fadmin-update-bridal/{id}',[OrdersAdminController::class,'func_update_bridal']);
            Route::delete('/fremove-airport-shuttle/{id}',[OrdersAdminController::class,'func_remove_airport_shuttle']);
            Route::delete('/admin-delete-opser/{id}',[OrdersAdminController::class,'destroy_opser_order']);
            Route::get('/contract/{id}',[OrdersAdminController::class,'view_contract_wedding_eng']);
            Route::put('/confirm-order-wedding/{id}',[OrdersAdminController::class,'func_confirm_order_wedding']);
            Route::put('/func-add-order-wedding-flight-admin/{id}',[OrdersAdminController::class,'func_add_order_wedding_flight']);
            Route::delete('/func-delete-order-wedding-flight-admin/{id}',[OrdersAdminController::class,'func_delete_order_wedding_flight_admin']);
            Route::put('/fadd-invitation-order-wedding/{id}',[OrdersAdminController::class,'func_add_order_wedding_invitation']);
            Route::put('/fedit-invitation-order-wedding/{id}',[OrdersAdminController::class,'func_edit_order_wedding_invitation']);
            Route::delete('/func-delete-order-wedding-invitation-admin/{id}',[OrdersAdminController::class,'func_delete_order_wedding_invitation']);

            Route::put('/admin-fadd-additional-charge/{id}',[OrdersAdminController::class,'func_admin_add_request_service']);
            Route::put('/admin-fupdate-additional-charge/{id}',[OrdersAdminController::class,'func_admin_update_request_service']);
            Route::put('/admin-fdelete-additional-charge/{id}',[OrdersAdminController::class,'func_admin_delete_request_service']);

            Route::put('/fadmin-add-order-wedding-accommodation/{id}',[OrdersAdminController::class,'func_admin_add_order_wedding_accommodation']);
            Route::get('/admin-validate-order-wedding-accommodation/{id}',[OrdersAdminController::class,'view_validate_order_wedding_accommodation']);
            Route::put('/admin-fupdate-accommodation-wedding-order/{id}',[OrdersAdminController::class,'func_update_wedding_order_accommodation']);
            Route::put('/admin-fupdate-accommodation-brides/{id}',[OrdersAdminController::class,'admin_func_update_accommodation_brides']);
            Route::put('/admin-fupdate-accommodation-invitation-price/{id}',[OrdersAdminController::class,'admin_func_update_accommodation_invitation']);
            Route::put('/admin-fupdate-price-accommodation/{id}',[OrdersAdminController::class,'admin_func_update_price_accommodation']);
            Route::delete('/admin-func-delete-order-wedding-accommodation/{id}',[OrdersAdminController::class,'func_delete_order_wedding_accommodation_invitation']);
            Route::get('/validate-orders-wedding/{id}',[OrdersAdminController::class,'view_validate_order_wedding']);
            Route::put('/admin-fupdate-wedding-order-bride/{id}',[OrdersAdminController::class,'func_validate_bride_order_wedding']);
            Route::put('/admin-fupdate-wedding-order-wedding/{id}',[OrdersAdminController::class,'func_validate_wedding_and_reservation']);
            Route::put('/admin-fupdate-wedding-order-ceremony-venue/{id}',[OrdersAdminController::class,'func_validate_wedding_order_ceremony_venue']);
            Route::put('/admin-fupdate-order-wedding-remark/{id}',[OrdersAdminController::class,'func_validate_wedding_order_remark']);
            Route::put('/admin-fdelete-wedding-order-ceremony-venue/{id}',[OrdersAdminController::class,'func_admin_delete_ceremony_venue']);
            Route::put('/admin-fupdate-wedding-order-decoration-ceremony-venue/{id}',[OrdersAdminController::class,'func_admin_update_decoration_ceremony_venue']);
            Route::put('/admin-fdelete-wedding-order-decoration-ceremony-venue/{id}',[OrdersAdminController::class,'func_admin_delete_decoration_ceremony_venue']);
            Route::put('/admin-fupdate-wedding-order-reception-venue/{id}',[OrdersAdminController::class,'admin_func_update_reception_venue']);
            Route::put('/admin-fdelete-wedding-order-reception-venue/{id}',[OrdersAdminController::class,'admin_func_delete_reception_venue']);
            Route::put('/admin-fupdate-wedding-order-decoration-reception-venue/{id}',[OrdersAdminController::class,'admin_func_update_decoration_reception_venue']);
            Route::put('/admin-fdelete-wedding-order-decoration-reception-venue/{id}',[OrdersAdminController::class,'admin_func_delete_decoration_reception_venue']);
            Route::put('/admin-fadd-additional-service-to-order-wedding/{id}',[OrdersAdminController::class,'admin_func_add_additional_service_to_wedding_order']);
            Route::put('/admin-fupdate-confirmation-number/{id}',[OrdersAdminController::class,'admin_func_update_confirmation_numbber']);
            Route::put('/fvalidate-order-wedding/{id}',[OrdersAdminController::class,'admin_func_validate_order_wedding']);
            Route::put('/admin-fadd-transport-invitation/{id}',[OrdersAdminController::class,'admin_func_add_transport_invitation_wedding']);
            Route::put('/admin-fupdate-transport-invitation/{id}',[OrdersAdminController::class,'admin_func_update_transport_invitation']);
            Route::post('/fadmin-update-lunch-venue-order-wedding/{id}',[OrdersAdminController::class,'admin_func_update_lunch_venue']);
            Route::post('/fadmin-delete-lunch-venue/{id}',[OrdersAdminController::class,'admin_func_delete_lunch_venue']);
            // ---------------------------------------------------
            //                    RESERVATION (RSV)
            // ---------------------------------------------------
            Route::get('/reservation',[ReservationController::class, 'index'])->name('view.reservation');
            Route::get('/order-rsv/{id}',[ReservationController::class, 'view_order_rsv']);
            Route::get('/reservation/{id}',[ReservationController::class, 'view_detail_reservation'])->whereNumber('id')->name('view.reservation.detail');
            Route::put('/reservation/{reservation}/invoice',[ReservationController::class, 'create_reservation_invoice'])->whereNumber('reservation')->name('view.reservation.invoice.store');
            Route::get('/rsv-hotel/{id}',[ReservationController::class, 'view_reservation_hotel']);
            Route::get('/add-rsv-order/{id}',[ReservationController::class, 'view_add_rsv_order']);
            Route::get('/add-rsv-transport/{id}',[ReservationController::class, 'view_add_rsv_transport']);
            Route::get('/add-rsv-activity-tour/{id}',[ReservationController::class, 'view_add_rsv_activity_tour']);
            Route::put('/fremove-rsv-order/{id}',[ReservationController::class,'func_remove_rsv_order']);
            Route::put('/fadd-reservation',[ReservationController::class,'func_add_rsv_order'])->name('admin.reservations.manual.store');
            Route::put('/fupdate-accommodation/{id}',[ReservationController::class,'func_update_accommodation']);
            Route::put('/fupdate-restaurant/{id}',[ReservationController::class,'func_update_restaurant']);
            Route::put('/fupdate-activity-tour/{id}',[ReservationController::class,'func_update_activity_tour']);
            Route::put('/fupdate-include/{id}',[ReservationController::class,'func_update_include']);
            Route::put('/fupdate-exclude/{id}',[ReservationController::class,'func_update_exclude']);
            Route::put('/fupdate-remark/{id}',[ReservationController::class,'func_update_remark']);
            Route::put('/fupdate-invoice-bank/{id}',[ReservationController::class,'func_update_invoice_bank']);
            Route::put('/activate-reservation/{id}',[ReservationController::class,'func_activate_reservation']);
            Route::put('/deactivate-reservation/{id}',[ReservationController::class,'func_deactivate_reservation'])->whereNumber('id')->name('view.reservation.deactivate');
            Route::put('/fadd-guest/{id}',[ReservationController::class,'func_add_guest'])->name('func.reservation-add-guest');
            Route::put('/fadd-restaurant',[ReservationController::class,'func_add_restaurant']);
            Route::put('/fadd-include',[ReservationController::class,'func_add_include']);
            Route::put('/fadd-exclude',[ReservationController::class,'func_add_exclude']);
            Route::put('/fadd-remark',[ReservationController::class,'func_add_remark']);
            Route::put('/fadd-invoice',[ReservationController::class,'func_add_invoice']);
            Route::put('/fupdate-cin-cut/{id}',[ReservationController::class,'fupdate_cin_cut']);
            Route::put('/fupdate-reservation/{id}',[ReservationController::class,'func_update_reservation']);
            Route::put('/fupdate-reservation-pickup-name/{id}',[ReservationController::class,'func_update_reservation_pickup_name']);
            Route::put('/fupdate-guest/{id}',[ReservationController::class,'func_update_guest']);
            Route::delete('/delete-guest/{id}',[ReservationController::class,'destroy_guest']);
            Route::delete('/fdelete-restaurant/{id}',[ReservationController::class,'destroy_restaurant']);
            Route::delete('/fdelete-include/{id}',[ReservationController::class,'destroy_include']);
            Route::delete('/fdelete-exclude/{id}',[ReservationController::class,'destroy_exclude']);
            Route::delete('/fdelete-remark/{id}',[ReservationController::class,'destroy_remark']);
            Route::delete('/fdelete-rsv/{id}',[ReservationController::class,'destroy_rsv'])->whereNumber('id')->name('admin.reservations.destroy');
            // ---------------------------------------------------
            //                      INVOICE
            // ---------------------------------------------------
            Route::get('/invoice',[InvoiceAdminController::class,'index'])->name('admin.invoices.index');
            Route::get('/invoice/{invoice}',[InvoiceAdminController::class,'view_detail_invoice'])->whereNumber('invoice')->name('admin.invoices.show');
            Route::put('/invoice/{invoice}/additional-items',[InvoiceAdminController::class,'func_add_additional_inv'])->whereNumber('invoice')->name('admin.invoices.adjustments.store');
            Route::put('/fupdate-additional-inv/{additionalInvoice}',[InvoiceAdminController::class,'func_update_additional_inv'])->whereNumber('additionalInvoice')->name('admin.invoices.adjustments.update');
            Route::delete('/delete-additional-inv/{additionalInvoice}',[InvoiceAdminController::class,'destroy_additional_inv'])->whereNumber('additionalInvoice')->name('admin.invoices.adjustments.destroy');
            Route::put('/invoice/{invoice}/bank',[InvoiceAdminController::class,'updateBank'])->whereNumber('invoice')->name('admin.invoices.bank.update');
            // ---------------------------------------------------
            //                PAYMENT CONFIRMATION
            // ---------------------------------------------------
            Route::get('/orders-admin/{order}/payments/{payment}/receipt',[AccommodationFinancialFileController::class,'adminReceipt'])->name('admin.orders.accommodation.payments.receipt');
            Route::get('/orders-admin/{order}/transport/payments/{payment}/receipt',[AccommodationFinancialFileController::class,'adminTransportReceipt'])->name('admin.orders.transport.payments.receipt');
            Route::get('/orders-admin/{order}/tour/payments/{payment}/receipt',[AccommodationFinancialFileController::class,'adminTourReceipt'])->name('admin.orders.tour.payments.receipt');
            Route::get('/orders-admin/{order}/activity/payments/{payment}/receipt',[AccommodationFinancialFileController::class,'adminActivityReceipt'])->name('admin.orders.activity.payments.receipt');
            Route::get('/orders-admin/{order}/invoice/{locale}/preview',[AccommodationFinancialFileController::class,'adminInvoicePreview'])->where('locale', 'en|zh-CN|zh')->name('admin.orders.accommodation.invoice.preview');
            Route::get('/orders-admin/{order}/invoice/{locale}/download',[AccommodationFinancialFileController::class,'adminInvoiceDownload'])->where('locale', 'en|zh-CN|zh')->name('admin.orders.accommodation.invoice.download');
            Route::post('/fconfirmation-payment/{id}',[OrdersAdminController::class,'fconfirmation_payment'])->name('admin.confirm.receipt');
            Route::post('/fadmin-add-payment-confirmation/{id}',[OrdersAdminController::class,'admin_add_payment_confirmation'])->name('func.admin-add-payment-confirmation');
            Route::post('/forder-wedding-confirmation-payment/{id}',[OrdersAdminController::class,'forder_wedding_confirmation_payment']);
            Route::post('/order-wedding-add-payment-confirmation/{id}',[OrdersAdminController::class,'admin_add_payment_confirmation_to_order_wedding']);
        });
        // ========================================================================================================================================> (ADMIN)
        Route::middleware(['adminType'])->group(function () {
            Route::prefix('admin')->group(function () {

            });


            // ---------------------------------------------------
            //                        VILLAS
            // ---------------------------------------------------
            Route::get('/villas-admin',[VillasController::class,'admin_index'])->name('admin.villas.index');
            Route::get('/admin-villa-detail/{id}',[VillasController::class,'admin_villa_detail'])->name('admin.villa.show');
            Route::get('/admin-villa-edit/{id}',[VillasController::class,'admin_villa_edit'])->name('admin.villa.edit');
            Route::delete('/fdelete-villa/{id}',[VillasController::class,'func_delete_villa'])->name('func.villa.delete');
            Route::put('/fupdate-villa/{id}',[VillasController::class,'func_update_villa'])->name('func.update-villa');
            Route::post('/fadd-villa-contract',[VillasController::class,'func_add_villa_contract'])->name('func.add-villa-contract');
            Route::delete('/fdelete-villa-contract/{id}',[VillasController::class,'delete_villa_contract'])->name('func.remove-villa-contract');
            Route::put('/fupdate-villa-contract/{id}',[VillasController::class,'func_edit_villa_contract'])->name('func.update-villa-contract');
            Route::put('/fupdate-villa-room/{id}',[VillasController::class,'func_update_villa_room'])->name('func.update-villa-room');
            Route::delete('/fdelete-villa-room/{id}',[VillasController::class,'func_delete_villa_room'])->name('func.delete-villa-room');
            Route::get('/admin-villa-room-add/{id}',[VillasController::class,'view_add_villa_room'])->name('admin.villa-room.add');
            Route::get('/edit-villa-room/{id}',[VillasController::class,'admin_edit_villa_room'])->name('view.edit-villa-room');
            Route::post('/fadd-villa-room',[VillasController::class,'func_add_villa_room'])->name('func.add-villa-room');
            Route::get('/edit-villa-room/{id}',[VillasController::class,'admin_edit_villa_room'])->name('view.edit-villa-room');

            Route::get('/add-villa-price/{id}',[VillasController::class,'view_admin_add_villa_price'])->name('view.add-villa-price');
            Route::post('/fadd-villa-price/{id}',[VillasController::class,'func_add_villa_price'])->name('func.villa-price.add');
            Route::put('/fupdate-villa-price/{id}',[VillasController::class,'func_edit_villa_price'])->name('func.villa-price.update');
            Route::delete('/fdelete-villa-price/{id}',[VillasController::class,'func_delete_villa_price'])->name('func.villa-price.delete');

            Route::put('/fupdate-villa-additional-service/{id}',[VillasController::class,'func_edit_additional_service'])->name('func.villa-additional-service.update');
            Route::post('/fadd-villa-additional-service',[VillasController::class,'func_add_additional_service'])->name('func.villa-additional-service.add');
            Route::delete('/fdelete-villa-additional-service/{id}',[VillasController::class,'func_delete_additional_service'])->name('func.villa-additional-service.delete');
            Route::post('/check-villa-status', [VillasController::class, 'checkVillaStatus'])->name('villa.checkStatus');
            // ---------------------------------------------------
            //                       TOURS PACKAGES
            // ---------------------------------------------------
            Route::post('/fadd-tour',[TourAdminController::class,'store'])->name('admin.tours.store');
            Route::put('/fupdate-tour/{id}',[TourAdminController::class,'update'])->name('admin.tours.update');
            Route::get('/tour-package-admin',[TourAdminController::class,'index'])->name('admin.tour-packages.index');
            Route::get('/detail-tour/{id}',[TourAdminController::class,'show'])->name('admin.tours.show');
            Route::get('/edit-tour/{id}',[TourAdminController::class,'edit'])->name('admin.tours.edit');
            Route::delete('/remove-tour/{id}',[TourAdminController::class,'destroy'])->name('admin.tours.destroy');
            Route::get('/add-tour',[TourAdminController::class,'create'])->name('admin.tours.create');
            Route::get('/tour-location/references',[TourAdminController::class,'searchTourLocationReferences'])->name('tour-location.references');
            Route::post('/tour-location/resolve-coordinates',[TourAdminController::class,'resolveTourLocationCoordinates'])->name('tour-location.resolve-coordinates');
            Route::post('/detail-tour-{tour}/prices',[TourPriceAdminController::class,'store'])->name('admin.tours.prices.store');
            Route::put('/detail-tour-{tour}/prices/{tourPrice}',[TourPriceAdminController::class,'update'])->name('admin.tours.prices.update');
            Route::delete('/detail-tour-{tour}/prices/{tourPrice}',[TourPriceAdminController::class,'destroy'])->name('admin.tours.prices.destroy');
            Route::post('/detail-tour-{tour}/prices/{tourPrice}/restore',[TourPriceAdminController::class,'restore'])->name('admin.tours.prices.restore');

            // ---------------------------------------------------
            //                     TRANSPORTS
            // ---------------------------------------------------
            Route::get('/transports-admin',[TransportAdminController::class,'index'])->name('admin.transports.index');
            Route::get('/detail-transport/{id}',[TransportAdminController::class,'show'])->name('admin.transports.show');
            Route::get('/transport-types', [TransportTypeController::class, 'index'])->name('admin.transport-types.index');
            Route::post('/transport-types', [TransportTypeController::class, 'store'])->name('admin.transport-types.store');
            Route::put('/transport-types/{transportType}', [TransportTypeController::class, 'update'])->name('admin.transport-types.update');
            Route::delete('/transport-types/{transportType}', [TransportTypeController::class, 'destroy'])->name('admin.transport-types.destroy');
            Route::get('/transport-brands', [TransportBrandController::class, 'index'])->name('admin.transport-brands.index');
            Route::post('/transport-brands', [TransportBrandController::class, 'store'])->name('admin.transport-brands.store');
            Route::put('/transport-brands/{transportBrand}', [TransportBrandController::class, 'update'])->name('admin.transport-brands.update');
            Route::delete('/transport-brands/{transportBrand}', [TransportBrandController::class, 'destroy'])->name('admin.transport-brands.destroy');

            // ---------------------------------------------------
            //                        EMAIL
            // ---------------------------------------------------
            Route::get('/email-reservation', [MailController::class, 'index']);
            // ---------------------------------------------------
            //                     BOOKING CODE
            // ---------------------------------------------------
            Route::get('/booking-code',[BookingCodeController::class,'index'])->name('booking-code');
            // ---------------------------------------------------
            //                      PROMOTION
            // ---------------------------------------------------
            Route::get('/promotion',[PromotionController::class,'index'])->name('promotion');

            

            // Print SPK (PDF atau langsung view untuk print)
            Route::get('/spks/{id}/print', [SpksController::class, 'print'])->name('spks.print');
            Route::post('/spk/{id}/guest/add', [SpksController::class, 'func_add_guest'])->name('func.spk-guest.add');
            Route::post('/spk/guest-update/{id}', [SpksController::class, 'func_update_spk_guest'])->name('func.spk-guest.update');
            Route::delete('/spk/guest/{id}', [SpksController::class, 'func_delete_guest'])->name('func.spk-guest.delete');
            Route::post('/spk/{id}/airport-shuttle/add', [SpksController::class, 'func_add_airport_shuttle'])->name('func.spk-airport-shuttle.add');
            Route::delete('/spk/airport-shuttle-delete/{id}', [SpksController::class, 'func_delete_airport_shuttle'])->name('func.spk-airport-shuttle.delete');
            Route::post('/spk/airport-shuttle-update/{id}', [SpksController::class, 'func_update_airport_shuttle'])->name('func.spk-airport-shuttle.update');
            Route::get('/spks/{id}/detail', [SpksController::class, 'spk_detail'])->name('spks.detail.partials');

            // |--------------------------------------------------------------------------
            // | RESERVATION
            // |--------------------------------------------------------------------------
            Route::get('/reservation/{no}', [ReservationController::class, 'detail_reservation'])->name('view.detail-reservation');
            Route::get('/reservation-archive', [ReservationController::class, 'reservationArchive'])->name('reservation.archive');
            Route::resource('reservations', ReservationController::class);
            Route::post('reservations/{reservation}/generate-spk', [ReservationController::class, 'generateSpks'])->name('reservations.generateSpks');
            Route::resource('spks', SpksController::class)->only(['show']);



            // Send WhatsApp melalui web whatsapp
            // |--------------------------------------------------------------------------
            // | SPK WhatsApp
            // |--------------------------------------------------------------------------
            Route::get('/spks/{spk}/send-whatsapp', [SpkWhatsAppController::class,'send',])->name('spks.send-whatsapp');
            Route::get('/spks/{spk}/send-whatsapp-driver', [SpkWhatsAppController::class,'send_to_driver',])->name('spks.send-whatsapp-driver');


            // ---------------------------------------------------
            //                        CHAT
            // ---------------------------------------------------
            Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
            Route::post('/chat/send-message', [ChatController::class, 'sendMessage'])->name('chat.send');
            // ---------------------------------------------------
            //                      CONTRACT
            // ---------------------------------------------------
            Route::get('/confirmation-order/{id}', [OrdersAdminController::class, 'confirmation_order']);
            Route::get('/print-contract-order/{id}', [OrdersAdminController::class, 'print_contract_order'])->name('admin.orders.document.print');
            Route::get('/print-contract-wedding/{id}', [OrdersAdminController::class, 'print_contract_wedding']);
            // ---------------------------------------------------
            //                       WEDDING
            // ---------------------------------------------------
            Route::get('/weddings-admin',[WeddingsController::class,'index'])->name('weddings-admin.index');
            Route::get('/weddings-hotel-admin/{id}',[WeddingsController::class,'view_wedding_hotel_admin_detail']);
            Route::get('/detail-vendor/{id}',[VendorController::class,'view_vendor_detail']);
            Route::put('/frefresh-wedding-price/{id}',[WeddingsController::class,'func_refresh_wedding_price']);
            Route::get('/weddings-admin/{id}',[WeddingsController::class,'view_wedding_admin_detail']);
        });
        // ========================================================================================================================================> (AGENT)
        Route::group([], function () {
            // ---------------------------------------------------
            //                      HOTELS
            // ---------------------------------------------------
            // Route::get('/hotels',[HotelsController::class,'index'])->name('view.hotels');
            Route::get('/hotels/autocomplete', [HotelsController::class, 'autocomplete'])->name('hotels.autocomplete');
            Route::get('/hotels/autocomplete-region', [HotelsController::class, 'autocompleteRegion'])->name('hotels.autocompleteRegion');
            Route::get('/hotels/load-more', [HotelsController::class, 'loadMore'])->name('hotels.load-more');
            Route::post('/search-hotels',[HotelsController::class,'search_hotel'])->name('view.hotels-search');
            Route::post('/order-room/{id}',[HotelsController::class,'order_room'])->name('view.order-room');
            Route::post('/fcheck-code',[HotelsController::class,'fcheck_code'])->name('func.hotel-check-code');
            Route::get('/accommodation/price-{code}', function (Request $request, $code) {
                $checkin = $request->query('checkin') ?: session('booking_dates.checkin');
                $checkout = $request->query('checkout') ?: session('booking_dates.checkout');

                if ($checkin && $checkout) {
                    return redirect()->route('view.hotel-prices.page', [
                        'code' => $code,
                        'checkin' => $checkin,
                        'checkout' => $checkout,
                    ]);
                }

                return redirect()->route('view.hotel-detail', [
                    'code' => $code,
                    'check_price' => 1,
                ]);
            })->name('view.accommodation-prices.page');
            Route::post('/accommodation/price-{code}', [HotelsController::class, 'hotel_price'])->name('view.accommodation-prices');
            Route::get('/hotel-price-{code}',[HotelsController::class,'hotel_price_page'])->name('view.hotel-prices.page');
            Route::post('/hotel-price-{code}',[HotelsController::class,'hotel_price'])->name('view.hotel-prices');
            Route::get('/hotel-{code}-{bcode}',[HotelsController::class,'hoteldetail_bookingcode'])->name('hotel-bookingcode');
            Route::post('/hotel-price-{code}-{bcode}',[HotelsController::class,'hotel_price_bookingcode'])->name('view.hotel-prices-bcode');
            Route::post('/fadd-optional-rate-order',[HotelsController::class,'func_add_optional_rate_order'])->name('fadd.optional-rate-order');
            // ---------------------------------------------------
            //                           VILLAS
            // ---------------------------------------------------
            Route::get('/villas',[VillasController::class,'index'])->name('view.villas-service');
            Route::get('/villas/autocomplete', [VillasController::class, 'autocomplete'])->name('villas.autocomplete');
            Route::get('/villas/autocomplete-region', [VillasController::class, 'autocompleteRegion'])->name('villas.autocompleteRegion');
            Route::get('/villas/load-more', [VillasController::class, 'loadMore'])->name('villas.load-more');
            Route::get('/villas/{code}',[VillasController::class,'show'])->name('view.villas.show');
            Route::post('/villa-price-{code}',[VillasController::class,'villa_price'])->name('view.villa-prices');
            Route::get('/villas/search', [VillasController::class, 'search_villas'])->name('villas.search-villas');
            // ---------------------------------------------------
            //                    HOTELS PROMO
            // ---------------------------------------------------
            Route::get('/hotel-promo/{id}/{checkin}/{checkout}',[HotelPromoController::class,'index'])->name('view.hotel-promo-detail');
            Route::post('/hotel-promo',[HotelPromoController::class,'hotelpromo'])->name('view.hotel-promo');
            // ---------------------------------------------------
            //                    TOURS PACKAGES AGENT
            // ---------------------------------------------------
            // Route::get('/tour-packages',[ToursController::class,'index'])->name('view.tours');
            Route::get('/tour-{code}-{bcode}',[ToursController::class,'view_tour_detail_bookingcode'])->name('view.tour-detail-bookingcode');
            Route::get('/tours/load-more', [ToursController::class, 'loadMore'])->name('tours.load-more');
            Route::post('/search-tours',[ToursController::class,'search_tour'])->name('view.tour-search');
            Route::post('/tour-detail',[ToursController::class,'tour_check_code'])->name('func.tour-check-code');
            Route::post('/tour-add-bookingcode',[ToursController::class,'search_tour'])->name('func.tour-add-bookingcode');

            Route::get('/get-tour-prices/{tour_id}', [TourPricesController::class, 'getPrices'])->name('get-tour-prices');
            Route::post('/fcreate-order-tour-package/{id}',[OrderController::class,'func_create_order_tour_package'])->name('func.order-tour-package.create');
            // ---------------------------------------------------
            //                 HOTEL PROMO FLYER
            // ---------------------------------------------------
            // Route::get('/promotion-flyer/{id}', [FlyerGeneratorController::class, 'flyer_detail'])->name('view.flyers-detail');
            // Route::get('/flyers', [FlyerGeneratorController::class, 'index'])->name('index.flyers');
            // ---------------------------------------------------
            //                      ACTIVITY
            // ---------------------------------------------------
            // Route::get('/activities',[ActivitiesController::class,'index'])->name('view.activities-service');
            Route::get('/activity-{code}/booking-code/{bcode}',[ActivitiesController::class,'activitydetail_bookingcode'])->name('view.activity-detail-booking-code');
            Route::get('/activity-{code}',[ActivitiesController::class,'activitydetail'])->name('view.activity-detail');
            Route::post('/activity-detail',[ActivitiesController::class,'activity_check_code'])->name('view.activity-check-code');
            Route::post('/search-activities',[ActivitiesController::class,'search_activities'])->name('view.search-activity');
            // ---------------------------------------------------
            //                      TRANSPORT
            // ---------------------------------------------------
            // Route::get('/transports',[transportsController::class,'index'])->name('view.transports-service');
            Route::get('/transport-{code}',[transportsController::class,'transport_detail'])->name('view.transport-detail');
            Route::get('/transport/{code}/{bcode}',[transportsController::class,'transport_detail_bookingcode'])->name('view.transport-detail-booking-code');
            Route::post('/transport-detail',[transportsController::class,'transport_check_code'])->name('view.transport-detail-check-code');
            Route::post('/search-transports',[transportsController::class,'search_transports'])->name('view.search-transport');
            // ---------------------------------------------------
            //                        ORDER
            // ---------------------------------------------------
            Route::get('/orders',[OrderController::class,'index'])->name('view.orders');
            Route::get('/orders/history', [OrderController::class, 'order_history'])->name('orders.history');
            Route::get('/orders/{id}/invoice/preview', [OrderController::class, 'preview_order_invoice'])->name('orders.invoice.preview');
            Route::get('/orders/{id}/invoice/download', [OrderController::class, 'download_order_invoice'])->name('orders.invoice.download');

            Route::get('/order/{id}',[OrderController::class,'detail_order'])->name('view.detail-order');
            Route::post('/fadd-order',[OrderController::class,'func_add_order'])->name('func.add-order');
            Route::put('/cancel-order/{id}',[OrderController::class,'func_cancel_order'])->name('func.cancel-order');
            Route::put('/freupload-order/{id}',[OrderController::class,'func_reupload_order'])->name('func.reupload-order');
            Route::put('/fremove-order/{id}',[OrderController::class,'func_remove_order'])->name('func.remove-order');
            Route::put('/fapprove-order/{id}',[OrderController::class,'func_approve_order'])->name('func.approve-order');
            Route::delete('/delete-order/{id}',[OrderController::class,'destroy_order'])->name('func.delete-order');
            // ---------------------------------------------------
            //                      ORDER HOTEL
            // ---------------------------------------------------
            Route::get('/edit-order-hotel/{id}',[OrderController::class,'edit_order_hotel'])->name('view.edit-order-hotel');
            Route::put('/fsubmit-order-hotel/{id}',[OrderController::class,'func_submit_order_hotel'])->name('func.submit-order-hotel');
            Route::get('/detail-order-hotel/{id}',[OrderController::class,'detail_order_hotel'])->name('view.detail-order-hotel');
            // PROMO
            Route::post('/order-hotel-promo/{id}',[orderController::class,'order_hotel_promo'])->name('view.order-hotel-promo');
            Route::post('/fcreate-order-hotel-promo',[orderController::class,'func_create_order_hotel_promo'])->name('func.create.order-hotel-promo');
            // PACKAGEorder
            Route::post('/order-hotel-package/{id}',[orderController::class,'order_hotel_package'])->name('view.order-hotel-package');
            Route::post('/fcreate-order-hotel-package/{id}',[orderController::class,'func_create_order_hotel_package'])->name('func.create.order-hotel-package');
            // NORMAL
            Route::post('/order-hotel-normal/{id}',[orderController::class,'order_hotel_normal'])->name('view.order-hotel-normal');
            Route::post('/fcreate-order-hotel-normal',[orderController::class,'func_create_order_hotel_normal'])->name('func.create.order-hotel-normal');
            // ADDITIONAL CHARGE
            Route::get('/edit-order-additional-charge/{id}',[OrderController::class,'edit_order_additional_charge'])->name('view.edit-order-additional-charge');
            Route::post('/fcreate-order-additional-charge/{id}',[OrderController::class,'func_create_order_additional_charge'])->name('func.create.order-additional-charge');
            Route::put('/fupdate-order-additional-charge/{id}',[OrderController::class,'func_update_order_additional_charge'])->name('func.update.order-additional-charge');
            Route::delete('/fdelete-order-additional-charge/{id}', [OrderController::class, 'func_delete_order_additional_charge'])->name('func.delete.order-additional-charge');
            // OPTIONAL RATE
            Route::post('/fadd-optional-rate',[OrderController::class,'func_add_optional_rate'])->name('func.add-optional-rate-order');
            Route::put('/fupdate-optional-rate-order/{id}',[OrderController::class,'func_update_optional_rate_order'])->name('func.update-optional-rate-order');
            Route::delete('/delete-opser/{id}',[OrderController::class,'destroy_opser_order'])->name('func.destroy-optional-rate-order');
            // SUITE AND VILLAS
            Route::get('/edit-order-room/{id}',[OrderController::class,'edit_order_room'])->name('view.edit-order-room');
            Route::put('/fupdate-order-room/{id}',[OrderController::class,'func_update_order_room'])->name('func.update.order-room');
            // ---------------------------------------------------
            //                   ORDER VILLA
            // ---------------------------------------------------
            Route::get('/villa-order/{code}',[OrderController::class,'order_villa'])->name('view.order-villa');
            Route::post('/villa-order',[OrderController::class,'func_create_order_villa'])->name('func.add-order-villa');
            Route::get('/edit-order-villa/{id}',[OrderController::class,'edit_order_villa'])->name('view.edit-order-villa');
            Route::put('/checkout-order-villa/{id}',[OrderController::class,'func_checkout_order_villa'])->name('func.checkout-order-villa');
            Route::delete('/remove-guests/{id}', [GuestsController::class, 'remove'])->name('guests.remove');
            Route::get('/detail-order-villa/{id}',[OrderController::class,'detail_order_villa'])->name('view.detail-order-villa');

            // ---------------------------------------------------
            //                   ORDER TOUR PACKAGE
            // ---------------------------------------------------
            Route::get('/edit-order-tour/{id}',[OrderController::class,'edit_order_tour'])->name('view.edit-order-tour');
            Route::put('/fupdate-order-tour/{id}',[OrderController::class,'func_update_order_tour'])->name('func.order-tour.update');
            Route::get('/detail-order-tour/{id}',[OrderController::class,'detail_order_tour'])->name('view.detail-order-tour');
            // ---------------------------------------------------
            //                   ORDER TOUR ACTIVITY
            // ---------------------------------------------------
            Route::get('/edit-order-activity/{id}',[OrderController::class,'edit_order_activity'])->name('view.edit-order-activity');
            // Route::put('/fupdate-order-activity/{id}',[OrderController::class,'func_update_order_activity'])->name('func.order-activity.update');
            Route::get('/detail-order-activity/{id}',[OrderController::class,'detail_order_activity'])->name('view.detail-order-activity');
            // ---------------------------------------------------
            //                   ORDER TRANSPORT
            // ---------------------------------------------------
            Route::post('/order-transport/{id}',[OrderController::class,'order_transport'])->name('view.order-transport');
            Route::post('/fcreate-order-transport/{id}',[OrderController::class,'func_create_order_transport'])->name('func.create.order-transport');
            Route::get('/edit-order-transport/{id}',[OrderController::class,'edit_order_transport'])->name('view.edit-order-transport');
            Route::put('/fsubmit-order-transport/{id}',[OrderController::class,'func_submit_order_transport'])->name('func.submit.order-transport');
            Route::get('/detail-order-transport/{id}',[OrderController::class,'detail_order_transport'])->name('view.detail-order-transport');
            // ---------------------------------------------------
            //                    ORDER WEDDING
            // ---------------------------------------------------
            Route::get('/order-wedding',[OrderWeddingController::class,'view_order_wedding'])->name('view.order-wedding');
            Route::put('/fsubmit-order-wedding/{id}',[OrderWeddingController::class,'func_submit_order_wedding'])->name('func.submit-order-wedding');
            Route::get('/detail-order-wedding-{orderno}',[OrderWeddingController::class,'detail_order_wedding'])->name('view.detail-order-wedding');
            Route::get('/edit-order-wedding-{orderno}',[OrderWeddingController::class,'edit_order_wedding'])->name('view.edit-order-wedding');
            Route::post('/forder-wedding-venue/{id}',[OrderWeddingController::class,'func_create_order_wedding_venue'])->name('func.create-order-weding');
            Route::put('/fupdate-wedding-ceremonial-venue/{id}',[OrderWeddingController::class,'func_update_wedding_ceremonial_venue'])->name('func.update-wedding-ceremony-venue');
            Route::post('/fupdate-wedding-order-ceremony-venue/{id}',[OrderWeddingController::class,'func_update_wedding_order_ceremony_venue'])->name('func.update-order-wedding-ceremony-venue');
            Route::put('/fdelete-ceremony-venue/{id}',[OrderWeddingController::class,'func_delete_ceremony_venue'])->name('func.delete-order-wedding-ceremony-venue');
            Route::put('/fupdate-wedding-order-bride/{id}',[OrderWeddingController::class,'func_update_wedding_order_brides'])->name('func.update-order-wedding-brides');
            Route::put('/fupdate-wedding-order-wedding/{id}',[OrderWeddingController::class,'func_update_wedding_order_wedding'])->name('func.update-order-wedding');
            Route::put('/fadd-wedding-order-additional-service/{id}',[OrderWeddingController::class,'func_add_wedding_order_addser_decoration'])->name('func.add-order-wedding-additional-service-decoration');
            Route::post('/fadd-order-wedding-transport/{id}',[OrderWeddingController::class,'func_add_wedding_order_transport'])->name('func.add-order-wedding-transport');
            Route::put('/fuser-update-order-wedding-transport/{id}',[OrderWeddingController::class,'func_user_update_order_wedding_transport'])->name('func.update-order-wedding-transport');
            Route::delete('/fremove-order-wedding-transport/{id}',[OrderWeddingController::class,'func_remove_transport_from_order_wedding'])->name('func-remove-order-wedding-transport');
            Route::delete('/fremove-order-wedding-additional-charge/{id}',[OrderWeddingController::class,'func_remove_request_service_from_order_wedding'])->name('func.remove-request-service-wedding-order');
            Route::post('/upload-pdf/{id}', [OrderWeddingController::class, 'store_invoice_pdf'])->name('upload.pdf.store');
            Route::put('/fadd-decoration-to-ceremony-venue/{id}',[OrderWeddingController::class,'func_add_decoration_to_ceremony_venue'])->name('func.add-decoration-to-ceremony-venue');
            Route::put('/fupdate-decoration-ceremony-venue/{id}',[OrderWeddingController::class,'func_update_decoration_ceremony_venue'])->name('func.update-decoration-ceremony-venue');
            Route::put('/fdelete-decoration-ceremony-venue/{id}',[OrderWeddingController::class,'func_delete_decoration_ceremony_venue'])->name('func.delete-decoration-ceremony-venue');
            Route::put('/fupdate-decoration-reception-venue/{id}',[OrderWeddingController::class,'func_update_decoration_reception_venue'])->name('func.update-decoration-reception-venue');
            Route::put('/fdelete-decoration-reception-venue/{id}',[OrderWeddingController::class,'func_delete_decoration_reception_venue'])->name('func.delete-decoration-reception-venue');
            Route::put('/fadd-order-wedding-remark/{id}',[OrderWeddingController::class,'func_add_order_wedding_remark'])->name('func.add-order-wedding-remark');

            Route::put('/fdelete-order-wedding-remark/{id}',[OrderWeddingController::class,'func_delete_order_wedding_remark'])->name('func.delete-order-wedding-remark');
            Route::put('/fadd-order-wedding-accommodation/{id}',[OrderWeddingController::class,'func_add_order_wedding_accommodation'])->name('func.add-order-wedding-accommodation');
            Route::put('/fupdate-order-wedding-accommodation/{id}',[OrderWeddingController::class,'func_update_order_wedding_accommodation'])->name('func.update-order-wedding-accommodation');
            Route::delete('/fdelete-order-wedding-accommodation/{id}',[OrderWeddingController::class,'func_delete_order_wedding_accommodation'])->name('func.delete-order-wedding-accommodation');

            Route::put('/fupdate-reception-venue/{id}',[OrderWeddingController::class,'func_update_reception_venue'])->name('func.update-reception-venue');
            Route::put('/fdelete-reception-venue/{id}',[OrderWeddingController::class,'func_delete_reception_venue'])->name('func.delete-reception-venue');
            Route::put('/fadd-additional-service-to-order-wedding/{id}',[OrderWeddingController::class,'func_additional_service_to_order_wedding'])->name('func.additional-service-to-order-wedding');
            Route::put('/fadd-order-wedding-brides-flight/{id}',[OrderWeddingController::class,'func_order_wedding_bride_flight'])->name('func.order-wedding-bride-flight');
            Route::put('/fadd-order-wedding-additional-service/{id}',[OrderWeddingController::class,'func_add_order_wedding_additional_service'])->name('func.add-order-wedding-additional-service');
            Route::post('/fadd-order-reception-venue/{id}',[OrderWeddingController::class,'func_add_order_reception_venue'])->name('func.add-order-reception-venue');
            Route::post('/fadd-order-wedding-package/{id}',[OrderWeddingController::class,'func_add_order_wedding_package'])->name('func.add-order-wedding-package');
            Route::post('/fadd-order-wedding-flight/{id}',[OrderWeddingController::class,'func_add_order_wedding_flight'])->name('func.add-order-wedding-flight');
            Route::post('/fupdate-order-wedding-flight/{id}',[OrderWeddingController::class,'func_update_order_wedding_flight'])->name('func.update-order-wedding-flight');
            Route::delete('/fdelete-order-wedding-flight/{id}',[OrderWeddingController::class,'func_delete_order_wedding_flight'])->name('func.delete-order-wedding-flight');
            Route::put('/fadd-invitation-to-order-wedding/{id}',[OrderWeddingController::class,'func_add_invitation_to_order_wedding'])->name('func.add-invitation-to-order-wedding');
            Route::post('/fupdate-invitation-order-wedding/{id}',[OrderWeddingController::class,'func_update_invitation_to_order_wedding'])->name('func.update-invitation-to-orders-wedding');
            Route::delete('/func-delete-invitation-order-wedding/{id}',[OrderWeddingController::class,'func_delete_invitation_to_order_wedding'])->name('func.delete-invitation-to-order-wedding');
            Route::delete('/delete-wedding-order/{id}',[OrderWeddingController::class,'func_delete_order_wedding'])->name('func.delete-order-wedding');
            // ---------------------------------------------------
            //               PAYMENT CONFIRMATION
            // ---------------------------------------------------
            Route::get('/orders/accommodation/{order}/payments/{payment}/receipt',[AccommodationFinancialFileController::class,'customerReceipt'])->name('orders.accommodation.payments.receipt');
            Route::get('/orders/transport/{order}/payments/{payment}/receipt',[AccommodationFinancialFileController::class,'customerTransportReceipt'])->name('orders.transport.payments.receipt');
            Route::get('/orders/tour/{order}/payments/{payment}/receipt',[AccommodationFinancialFileController::class,'customerTourReceipt'])->name('orders.tour.payments.receipt');
            Route::get('/orders/activity/{order}/payments/{payment}/receipt',[AccommodationFinancialFileController::class,'customerActivityReceipt'])->name('orders.activity.payments.receipt');
            Route::get('/orders/accommodation/{order}/invoice/{locale}/preview',[AccommodationFinancialFileController::class,'customerInvoicePreview'])->where('locale', 'en|zh-CN|zh')->name('orders.accommodation.invoice.preview');
            Route::get('/orders/accommodation/{order}/invoice/{locale}/download',[AccommodationFinancialFileController::class,'customerInvoiceDownload'])->where('locale', 'en|zh-CN|zh')->name('orders.accommodation.invoice.download');
            Route::get('/orders/transport/{order}/invoice/{locale}/preview',[AccommodationFinancialFileController::class,'customerInvoicePreview'])->where('locale', 'en|zh-CN|zh')->name('orders.transport.invoice.preview');
            Route::get('/orders/transport/{order}/invoice/{locale}/download',[AccommodationFinancialFileController::class,'customerInvoiceDownload'])->where('locale', 'en|zh-CN|zh')->name('orders.transport.invoice.download');
            Route::get('/orders/tour/{order}/invoice/{locale}/preview',[AccommodationFinancialFileController::class,'customerInvoicePreview'])->where('locale', 'en|zh-CN|zh')->name('orders.tour.invoice.preview');
            Route::get('/orders/tour/{order}/invoice/{locale}/download',[AccommodationFinancialFileController::class,'customerInvoiceDownload'])->where('locale', 'en|zh-CN|zh')->name('orders.tour.invoice.download');
            Route::get('/orders/activity/{order}/invoice/{locale}/preview',[AccommodationFinancialFileController::class,'customerInvoicePreview'])->where('locale', 'en|zh-CN|zh')->name('orders.activity.invoice.preview');
            Route::get('/orders/activity/{order}/invoice/{locale}/download',[AccommodationFinancialFileController::class,'customerInvoiceDownload'])->where('locale', 'en|zh-CN|zh')->name('orders.activity.invoice.download');
            Route::post('/fpayment-confirmation/{id}',[PaymentConfirmationController::class,'payment_confirmation'])->name('upload.payment-confirmation');
            Route::post('/fpayment-confirmation-{id}',[PaymentConfirmationController::class,'payment_confirmation'])->whereNumber('id')->name('upload.payment-confirmation.legacy');
            Route::post('/fwedding-payment-confirmation/{id}',[PaymentConfirmationController::class,'wedding_payment_confirmation'])->name('wedding-payment-confirmation');
            Route::put('/fupdate-payment-confirmation/{id}',[PaymentConfirmationController::class,'update_payment_confirmation'])->name('update-payment-confirmation');
            // ---------------------------------------------------
            //                     CONTRACT
            // ---------------------------------------------------
            Route::get('/zh-print-contract-wedding/{id}', [OrdersAdminController::class, 'zh_print_contract_wedding'])->name('func.print-contract-wedding-zh');
            Route::get('/en-print-contract-wedding/{id}', [OrdersAdminController::class, 'en_print_contract_wedding'])->name('func.print-contract-wedding-en');
            // ---------------------------------------------------
            //                   DOWNLOAD DATA
            // ---------------------------------------------------
            Route::get('/download', [DownloadDataHotelController::class, 'index'])->name('view.download-data-index');
            Route::get('/download-data-hotel', [DownloadDataHotelController::class, 'down_data_hotel'])->name('view.download-data-hotel');
            Route::get('/download-data-hotel-package', [DownloadDataHotelController::class, 'down_data_hotel_package'])->name('view.download-data-hotel-package');
            Route::get('/download-data-hotel-promo', [DownloadDataHotelController::class, 'down_data_hotel_promo'])->name('view.download-data-hotel-promo');
            Route::get('/download-data-tour', [DownloadDataHotelController::class, 'down_data_tour'])->name('view.download-data-tour');
            Route::get('/data-hotel', [DownloadDataHotelController::class, 'view_download_hotel'])->name('view.download-hotel');
            Route::get('/generate-pdf', [DownloadDataHotelController::class, 'generatePDF'])->name('func.generate-pdf');
            Route::get('/download-data-hotel-test', [DownloadDataHotelController::class, 'down_data_hotel_test'])->name('view.download-data-hotel-test');
            Route::post('/func-action-log-download-hotel', [DownloadDataHotelController::class, 'action_log_download_hotel'])->name('func.log-download-data-hotel');
            // ---------------------------------------------------
            //                        EMAIL
            // ---------------------------------------------------
            Route::get('/new-order-wedding-mail', [MailController::class, 'view_email_order_wedding'])->name('view.email-order-wedding');
            Route::get('/email-approval', [MailController::class, 'view_email_approval'])->name('view.email-approval');
            Route::get('/email-booking', [MailController::class, 'view_email_booking'])->name('view.email-booking');
            Route::get('/confirmation-mail', [MailController::class, 'view_confirm_email'])->name('view.confirm-email');
            Route::get('/confirmation-payment', [MailController::class, 'view_email_payment_confirmation'])->name('view.email-payment-confirmation');
            Route::get('/manual-book', [ManualBookController::class, 'index'])->name('view.manual-book');
            // ---------------------------------------------------
            //                       WEDDING
            // ---------------------------------------------------
            Route::get('/wedding-hotel-{code}',[WeddingsController::class,'view_wedding_hotel_detail'])->name('view.wedding-detail');
            Route::get('/donwload-file', [WeddingsController::class,'download_pdf'])->name('download.wedding-pdf');
            Route::get('/weddings',[WeddingsController::class,'user_index'])->name('view.weddings');
            Route::post('/wedding-search',[WeddingsController::class,'wedding_search'])->name('view.wedding-search');
            Route::put('/fadd-package-to-wedding-planner/{id}',[WeddingsController::class,'func_add_package_to_wedding_planner'])->name('func.add-package-to-wedding-planner');
            Route::put('/fupdate-cancellation-policy/{id}',[WeddingsController::class,'func_update_cancellation_policy'])->name('func.wedding-update-cancelation-policy');
            Route::get('/edit-wedding-planner/{id}',[WeddingPlannerController::class,'view_edit_wedding_planner'])->name('view.edit-wedding-planner');
            // ---------------------------------------------------
            //                   WEDDING PLANNER
            // ---------------------------------------------------
            Route::get('/wedding-planner',[WeddingPlannerController::class,'index'])->name('view.wedding-planner');
            Route::post('/fadd-wedding-planner',[WeddingPlannerController::class,'func_add_wedding_planner'])->name('func.add-wedding-planner');
            Route::put('/fadd-wedding-planner-brides-flight/{id}',[WeddingPlannerController::class,'func_add_wedding_planner_brides_flight'])->name('func.add-wedding-planner-brides-flight');
            Route::put('/fupdate-wedding-planner-invitations/{id}',[WeddingPlannerController::class,'func_update_wedding_planner_invitations'])->name('func.update-wedding-planner-invitations');
            Route::put('/fadd-wedding-planner-invitations-flight/{id}',[FlightsController::class,'func_add_wedding_planner_invitations_flight'])->name('func.add-wedding-planner-invitation-flight');
            Route::put('/fupdate-wedding-planner-invitations-flight/{id}',[FlightsController::class,'func_update_wedding_planner_invitations_flight'])->name('func.update-wedding-planner-invitations-flight');
            Route::delete('/fdelete-wedding-planner-invitations-flight/{id}',[FlightsController::class,'func_delete_wedding_planner_invitations_flight'])->name('func.delete-wedding-planner-invitation-flight');
            Route::put('/fadd-ceremonial-venue-to-wedding-planner/{id}',[WeddingPlannerController::class,'func_add_ceremony_venue_to_wedding_planner'])->name('func.add-ceremony-venue-to-wedding-planner');
            Route::put('/fadd-reception-venue-to-wedding-planner/{id}',[WeddingPlannerController::class,'func_add_reception_venue_to_wedding_planner'])->name('func.add-reception-venue-to-wedding-planner');
            Route::post('/fadd-wedding-planner-transport/{id}',[WeddingPlannerController::class,'func_add_transport_to_wedding_planner'])->name('func.add-transport-to-wedding-planner');
            Route::delete('/fremove-wedding-planner-transport/{id}',[WeddingPlannerController::class,'func_remove_transport_from_wedding_planner'])->name('func.remove-transport-from-wedding-planner');
            Route::put('/fupdate-wedding-planner-transport/{id}',[WeddingPlannerController::class,'func_update_transport_from_wedding_planner'])->name('func.update-transport-from-wedding-planner');
            Route::put('/fadd-wedding-planner-invitations/{id}',[WeddingPlannerController::class,'func_add_wedding_planner_invitations'])->name('func.add-wedding-planner-invitations');
            Route::put('/fupdate-wedding-planner-bride/{id}',[WeddingPlannerController::class,'func_update_wedding_planner_bride'])->name('func.update-wedding-planner-bride');
            Route::put('/fupdate-wedding-planner-brides-flight/{id}',[WeddingPlannerController::class,'func_update_wedding_planner_bride_flight'])->name('func.update-wedding-planner-bride-flight');
            Route::put('/fupdate-wedding-planner-wedding/{id}',[WeddingPlannerController::class,'func_update_wedding_planner_wedding'])->name('func.update-wedding-planner-wedding');
            Route::put('/fupdate-wedding-planner-ceremonial-venue/{id}',[WeddingPlannerController::class,'func_update_wedding_planner_ceremonial_venue'])->name('func.update-wedding-planner-ceremonial-venue');
            Route::put('/fupdate-wedding-planner-reception-venue/{id}',[WeddingPlannerController::class,'func_update_wedding_planner_reception_venue'])->name('func.update-wedding-planner-reception-venue');
            Route::put('/fdelete-wedding-planner-ceremonial-venue/{id}',[WeddingPlannerController::class,'func_delete_wedding_planner_ceremonial_venue'])->name('func.delete-wedding-planner-ceremonial-venue');
            Route::put('/fdelete-wedding-planner-reception-venue/{id}',[WeddingPlannerController::class,'func_delete_wedding_planner_reception_venue'])->name('func.delete-wedding-planner-reception-venue');
            Route::delete('/fdelete-wedding-planner/{id}',[WeddingPlannerController::class,'func_destroy_wedding_planner'])->name('func.destroy-wedding-planner');
            Route::delete('/fdelete-wedding-planner-invitation/{id}',[WeddingPlannerController::class,'func_destroy_wedding_planner_invitation'])->name('func.destroy-wedding-planner-invitations');
            Route::put('/fsubmit-wedding-planner/{id}',[WeddingPlannerController::class,'func_submit_wedding_planner'])->name('func.submit-wedding-planner');
            // ACCOMMODATION
            Route::get('wedding-accommodation-update/{id}',[WeddingPlannerController::class,'view_update_wedding_accommodation'])->name('view.update-wedding-accommodation');
            Route::post('/fadd-wedding-accommodation/{id}',[WeddingPlannerController::class,'func_add_wedding_accommodations'])->name('func.add-wedding-accommodation');
            Route::post('/fupdate-wedding-accommodation/{id}',[WeddingPlannerController::class,'func_update_wedding_accommodation'])->name('func.update-wedding-accommodation');
            Route::post('/fadd-wedding-planner-accommodation/{id}',[WeddingPlannerController::class,'func_add_wedding_planner_accommodation'])->name('func.add-wedding.planner-accommodation');
            Route::put('/fupdate-wedding-planner-bride-accommodation/{id}',[WeddingPlannerController::class,'func_update_wedding_planner_bride_accommodation'])->name('func.update-wedding-planner-bride-accommodation');
            Route::put('/fupdate-wedding-planner-invitations-accommodation/{id}',[WeddingPlannerController::class,'func_update_wedding_planner_invitations_accommodation'])->name('func.update-wedding-planner-invitations-accommodation');
            Route::delete('/fdelete-wedding-accommodation/{id}',[WeddingPlannerController::class,'func_destroy_wedding_accommodation'])->name('func.destroy-wedding-accommodation');
            Route::delete('/fdelete-wedding-planner-bride-accommodation/{id}',[WeddingPlannerController::class,'func_destroy_wedding_planner_bride_accommodation'])->name('func.destroy-wedding-planner-bride-accommodation');
            // INVITATIONS
            Route::get('wedding-invitations-update/{id}',[WeddingInvitationsController::class,'view_update_wedding_invitations'])->name('view.update-wedding-invitations');
            Route::post('/fadd-wedding-invitations/{id}',[WeddingInvitationsController::class,'func_add_wedding_invitations'])->name('func.add-wedding-invitations');
            Route::post('/fupdate-wedding-invitations/{id}',[WeddingInvitationsController::class,'func_update_wedding_invitations'])->name('func.update-wedding-invitations');
            Route::delete('/fdelete-wedding-invitations/{id}',[WeddingInvitationsController::class,'func_destroy_wedding_invitations'])->name('func.destroy-wedding-invitations');
            // ---------------------------------------------------
            //                            SUBSCRIBE
            // ---------------------------------------------------
            Route::get('/unsubscribe', function (Request $request) {
                $user = User::where('email', $request->email)->first();
                if ($user) {
                    return view('unsubscribe', ['user' => $user]);
                }
                return redirect('/')->with('error', 'User not found.');
            })->name('unsubscribe');
            Route::post('/process-unsubscribe', function (Request $request) {
                $user = User::where('email', $request->email)->first();
                if ($user) {
                    $user->is_subscribed = false;
                    $user->unsubscribe_reason = $request->reason;
                    $user->save();
                    return redirect('/')->with('success', 'You have been unsubscribed successfully. Thank you for your feedback!');
                }
                return redirect('/')->with('error', 'User not found.');
            })->name('process_unsubscribe');
            Route::get('/subscribe', function (Request $request) {
                $user = User::where('email', $request->email)->first();
                if ($user) {
                    return view('subscribe', ['user' => $user]);
                }
                return redirect('/')->with('error', 'User not found.');
            })->name('subscribe');
            Route::post('/process-subscribe', function (Request $request) {
                $user = User::where('email', $request->email)->first();
                if ($user) {
                    $user->is_subscribed = true;
                    $user->save();
                    return redirect('/')->with('success', 'You have successfully subscribed again!');
                }
                return redirect('/')->with('error', 'User not found.');
            })->name('process_subscribe');
        });
    });
    Route::get('/approval/pending', function () {
        return redirect('/profile');
    })->name('approval.pending');
    // ALIHKAN JIKA HALAMAN TIDAK DITEMUKAN
    Route::fallback(function () {
        return redirect('/');
    });
