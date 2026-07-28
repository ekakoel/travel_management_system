<?php

namespace App\Console;

use App\Models\OrderLog;
use App\Models\Orders;
use App\Models\Reservation;
use App\Jobs\UpdateCurrencyRates;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->job(new UpdateCurrencyRates)->hourly();
        $schedule->call(function () {
            $now = Carbon::now();
            Orders::query()
                ->where('service', 'Tour Package')
                ->where('status', 'Approved')
                ->whereNotNull('rsv_id')
                ->with(['reservations.invoice.payment'])
                ->chunkById(100, function ($orders) use ($now) {
                    foreach ($orders as $order) {
                        $invoice = optional(optional($order->reservations)->invoice);

                        if (!$invoice || !$invoice->due_date) {
                            continue;
                        }

                        $deadline = Carbon::parse($invoice->due_date);
                        $hasPaymentSubmission = $invoice->payment
                            && $invoice->payment->contains(function ($payment) {
                                return in_array($payment->status, ['Pending', 'Valid', 'Paid'], true);
                            });

                        if (!$deadline->isPast() || $hasPaymentSubmission) {
                            continue;
                        }

                        $order->update([
                            'status' => 'Canceled',
                            'msg' => 'Automatically canceled because no payment confirmation was submitted within 48 hours after approval.',
                        ]);

                        Reservation::where('id', $order->rsv_id)->update([
                            'status' => 'Canceled',
                        ]);

                        OrderLog::create([
                            'order_id' => $order->id,
                            'action' => 'Auto Cancel Payment Deadline',
                            'url' => 'scheduler',
                            'method' => 'Update',
                            'agent' => $order->name,
                            'admin' => null,
                        ]);
                    }
                });
        })->everyFifteenMinutes()->name('orders:auto-cancel-expired-tour-payment');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
