<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(UsersTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(ToursSeeder::class);
        $this->call(HotelsSeeder::class);
        $this->call(TransportsSeeder::class);
        $this->call(ActivitiesSeeder::class);
        $this->call(PartnersSeeder::class);
        $this->call(HotelPriceSeeder::class);
        $this->call(HotelRoomSeeder::class);
        $this->call(HotelTypeSeeder::class);
        $this->call(RoomFacilitiesSeeder::class);
        $this->call(HotelPromoSeeder::class);
        $this->call(HotelPackageSeeder::class);
        $this->call(OrdersSeeder::class);
        $this->call(BusinessProfileSeeder::class);
        $this->call(ActivityTypeSeeder::class);
        $this->call(TransportTypeSeeder::class);
        $this->call(TourTypeSeeder::class);
        $this->call(TransportBrandSeeder::class);
        $this->call(InvoiceAdminSeeder::class);
        $this->call(ReservationSeeder::class);
        $this->call(GuidesSeeder::class);
        $this->call(GuestsSeeder::class);
        $this->call(DriversSeeder::class);
        $this->call(AgentSeeder::class);
        $this->call(AdditionalServiceSeeder::class);
        $this->call(AttentionSeeder::class);
        $this->call(MarkupSeeder::class);
        $this->call(UsdRatesSeeder::class);
        $this->call(OptionalRateSeeder::class);
        $this->call(TransportPriceSeeder::class);
        $this->call(TaxSeeder::class);
        $this->call(ServicesSeeder::class);
        $this->call(WeddingsSeeder::class);
        $this->call(ExtraBedSeeder::class);
        $this->call(ContractSeeder::class);
        $this->call(BookingCodeSeeder::class);
        //duplicate product for data 
    }
}
