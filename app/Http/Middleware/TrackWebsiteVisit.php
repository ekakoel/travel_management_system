<?php

namespace App\Http\Middleware;

use App\Models\WebsiteVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Throwable;

class TrackWebsiteVisit
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            $this->recordVisit($request);
        }

        return $response;
    }

    protected function shouldTrack(Request $request, $response): bool
    {
        if (!$request->isMethod('GET') || $request->ajax()) {
            return false;
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 400) {
            return false;
        }

        $contentType = (string) $response->headers->get('content-type');

        return Str::contains($contentType, 'text/html');
    }

    protected function recordVisit(Request $request): void
    {
        try {
            $agent = new Agent();
            $agent->setUserAgent((string) $request->userAgent());
            $ip = (string) $request->ip();
            $userAgent = (string) $request->userAgent();
            $route = $request->route();
            $countryCode = $this->countryCode($request);

            WebsiteVisit::create([
                'visitor_hash' => $this->hash($ip . '|' . $userAgent),
                'user_id' => Auth::id(),
                'method' => $request->method(),
                'path' => '/' . ltrim($request->path(), '/'),
                'url' => Str::limit($request->fullUrl(), 255, ''),
                'route_name' => $route ? $route->getName() : null,
                'page_title' => $this->pageTitle($request),
                'area' => $this->area($request),
                'country_code' => $countryCode,
                'country_name' => $this->countryName($countryCode),
                'referrer_host' => parse_url((string) $request->headers->get('referer'), PHP_URL_HOST),
                'device_type' => $this->deviceType($agent),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'ip_hash' => $this->hash($ip),
                'user_agent_hash' => $this->hash($userAgent),
                'visit_date' => now()->toDateString(),
                'occurred_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    protected function area(Request $request): string
    {
        if ($request->is('admin*') || $request->is('dashboard*') || $request->is('orders-admin*')) {
            return 'backend';
        }

        if (Auth::check()) {
            return 'home';
        }

        return 'landing-page';
    }

    protected function countryCode(Request $request): ?string
    {
        $country = $request->headers->get('CF-IPCountry')
            ?: $request->headers->get('X-Vercel-IP-Country')
            ?: $request->headers->get('X-AppEngine-Country');

        if (!$country || strtoupper($country) === 'XX') {
            return null;
        }

        return strtoupper(substr($country, 0, 8));
    }

    protected function countryName(?string $countryCode): string
    {
        $countries = [
            'AU' => 'Australia',
            'CA' => 'Canada',
            'CN' => 'China',
            'DE' => 'Germany',
            'FR' => 'France',
            'GB' => 'United Kingdom',
            'HK' => 'Hong Kong',
            'ID' => 'Indonesia',
            'IN' => 'India',
            'JP' => 'Japan',
            'KR' => 'South Korea',
            'MY' => 'Malaysia',
            'NL' => 'Netherlands',
            'NZ' => 'New Zealand',
            'SG' => 'Singapore',
            'TH' => 'Thailand',
            'TW' => 'Taiwan',
            'US' => 'United States',
        ];

        return $countryCode ? ($countries[$countryCode] ?? $countryCode) : 'Unknown';
    }

    protected function pageTitle(Request $request): string
    {
        $routeName = $request->route() ? $request->route()->getName() : null;

        return Str::of($routeName ?: $request->path())
            ->replace(['view.', 'admin.', '-', '_', '.'], [' ', ' ', ' ', ' ', ' '])
            ->title()
            ->trim()
            ->toString();
    }

    protected function deviceType(Agent $agent): string
    {
        if ($agent->isTablet()) {
            return 'tablet';
        }

        if ($agent->isMobile()) {
            return 'mobile';
        }

        return 'desktop';
    }

    protected function hash(string $value): string
    {
        return hash_hmac('sha256', $value, (string) config('app.key'));
    }
}
