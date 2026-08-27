<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke-tests every page (web) route except service-related ones and the
 * JSON api/* routes. It only checks that GET-ing the route as a guest does
 * not blow up (no 500), so protected routes are expected to come back as a
 * redirect/403/404 rather than 200 — the point is to catch fatal errors like
 * the RegisterController Faker crash, not to exercise business logic.
 */
class RouteSmokeTest extends TestCase
{
    use RefreshDatabase;

    public static function routeProvider(): array
    {
        $dummyId = 999999;

        return [
            // Public pages
            'home' => ['home', []],
            'about' => ['about', []],
            'home.ar.no-slash' => ['home.ar.no-slash', []],
            'contact' => ['contact', []],
            'faq' => ['faq', []],
            'how-it-works' => ['how-it-works', []],
            'login' => ['login', []],
            'register' => ['register', []],
            'privacy-policy' => ['privacy-policy', []],
            'support.take' => ['support.take', []],
            'terms' => ['terms', []],
            'password.request' => ['password.request', []],
            'password.reset.token' => ['password.reset', ['token' => 'dummy-token']],
            'changeLang' => ['changeLang', ['lang' => 'en']],

            // Localized variants ({locale} is regex-constrained)
            'home.localized' => ['home.localized', ['locale' => 'ar']],
            'about.localized' => ['about.localized', ['locale' => 'ar']],
            'contact.localized' => ['contact.localized', ['locale' => 'ar']],
            'faq.localized' => ['faq.localized', ['locale' => 'ar']],
            'how-it-works.localized' => ['how-it-works.localized', ['locale' => 'ar']],
            'privacy-policy.localized' => ['privacy-policy.localized', ['locale' => 'ar']],
            'support.take.localized' => ['support.take.localized', ['locale' => 'ar']],
            'terms.localized' => ['terms.localized', ['locale' => 'ar']],

            // Auth-protected — guest should be redirected/blocked, not crash
            'dashboard' => ['dashboard', []],
            'admin.spa' => ['admin.spa', []],
            'bonus.request' => ['bonus.request', []],
            'checkout' => ['checkout', []],
            'checkout.cancel' => ['checkout.cancel', ['transaction_id' => $dummyId]],
            'checkout.fail' => ['checkout.fail', ['transaction_id' => $dummyId]],
            'checkout.success' => ['checkout.success', ['transaction_id' => $dummyId]],
            'verification.notice' => ['verification.notice', []],
            'notifications.index' => ['notifications.index', []],
            'notifications.latest' => ['notifications.latest', []],
            'notifications.loadMore' => ['notifications.loadMore', []],
            'notifications.unreadCount' => ['notifications.unreadCount', []],
            'notifications.markAsRead' => ['notifications.markAsRead', ['id' => $dummyId]],
            'orders.index' => ['orders.index', []],
            'orders.create' => ['orders.create', []],
            'orders.updateStatuses' => ['orders.updateStatuses', []],
            'orders.show' => ['orders.show', ['order' => $dummyId]],
            'payment-methods.index' => ['payment-methods.index', []],
            'payment-methods.create' => ['payment-methods.create', []],
            'payment-methods.show' => ['payment-methods.show', ['payment_method' => $dummyId]],
            'payment-methods.edit' => ['payment-methods.edit', ['payment_method' => $dummyId]],
            'permissions.index' => ['permissions.index', []],
            'permissions.create' => ['permissions.create', []],
            'permissions.show' => ['permissions.show', ['permission' => $dummyId]],
            'permissions.edit' => ['permissions.edit', ['permission' => $dummyId]],
            'points.index' => ['points.index', []],
            'profile.settings' => ['profile.settings', []],
            'referrals.index' => ['referrals.index', []],
            'roles.index' => ['roles.index', []],
            'roles.create' => ['roles.create', []],
            'roles.show' => ['roles.show', ['role' => $dummyId]],
            'roles.edit' => ['roles.edit', ['role' => $dummyId]],
            'support.index' => ['support.index', []],
            'support.create' => ['support.create', []],
            'support.show' => ['support.show', ['ticket' => $dummyId]],
            'support.edit' => ['support.edit', ['ticket' => $dummyId]],
            'messages.latest' => ['messages.latest', ['ticket' => $dummyId]],
            'transactions.index' => ['transactions.index', []],
            'transactions.create' => ['transactions.create', []],
            'transactions.show' => ['transactions.show', ['transaction' => $dummyId]],
            'transactions.edit' => ['transactions.edit', ['transaction' => $dummyId]],
            'transactions.complete' => ['transactions.complete', ['transaction_id' => $dummyId]],
            'users.index' => ['users.index', []],
            'users.create' => ['users.create', []],
            'users.show' => ['users.show', ['user' => $dummyId]],
            'users.edit' => ['users.edit', ['user' => $dummyId]],
            'users.assignRole' => ['users.assignRole', ['user' => $dummyId]],
            'users.assignPermission' => ['users.assignPermission', ['user' => $dummyId]],
            'users.assignTask' => ['users.assignTask', ['user' => $dummyId]],
            'users.assignProject' => ['users.assignProject', ['user' => $dummyId]],
        ];
    }

    /** @dataProvider routeProvider */
    public function test_route_is_callable_without_a_server_error(string $name, array $params)
    {
        $url = route($name, $params);

        $response = $this->get($url);

        $this->assertLessThan(
            500,
            $response->getStatusCode(),
            "Route [$name] ($url) returned a server error: {$response->getStatusCode()}"
        );
    }

    /** @dataProvider unnamedRouteProvider */
    public function test_unnamed_route_is_callable_without_a_server_error(string $uri)
    {
        $response = $this->get($uri);

        $this->assertLessThan(
            500,
            $response->getStatusCode(),
            "Route [$uri] returned a server error: {$response->getStatusCode()}"
        );
    }

    public static function unnamedRouteProvider(): array
    {
        return [
            'robots.txt' => ['robots.txt'],
            'sitemap-main.xml' => ['sitemap-main.xml'],
            'sitemap-categories.xml' => ['sitemap-categories.xml'],
            'sitemap-platforms.xml' => ['sitemap-platforms.xml'],
        ];
    }

    public function test_sitemap_index_is_callable_without_a_server_error()
    {
        $response = $this->get(route('sitemap'));

        $this->assertLessThan(500, $response->getStatusCode());
    }
}
