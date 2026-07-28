$schedule->command('accommodation:complete-eligible-orders')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

$schedule->command('transport:complete-eligible-orders')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

$schedule->command('tour:complete-eligible-orders')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

$schedule->command('activity:complete-eligible-orders')
    ->everyFifteenMinutes()
    ->withoutOverlapping();