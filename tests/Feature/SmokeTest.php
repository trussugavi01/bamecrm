<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test application health check endpoint
     */
    public function test_health_check_endpoint_is_accessible(): void
    {
        $response = $this->get('/up');
        $response->assertStatus(200);
    }

    /**
     * Test login page loads
     */
    public function test_login_page_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Welcome Back');
    }

    /**
     * Test forgot password page loads
     */
    public function test_forgot_password_page_loads(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertSee('Reset Password');
    }

    /**
     * Test user can login
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
    }

    /**
     * Test dashboard is accessible when authenticated
     */
    public function test_dashboard_is_accessible_when_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    /**
     * Test dashboard redirects to login when not authenticated
     */
    public function test_dashboard_redirects_when_not_authenticated(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/');
    }

    /**
     * Test sponsorships page loads
     */
    public function test_sponsorships_page_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/sponsorships');
        $response->assertStatus(200);
    }

    /**
     * Test contacts page loads
     */
    public function test_contacts_page_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/contacts');
        $response->assertStatus(200);
    }

    /**
     * Test reports page loads
     */
    public function test_reports_page_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/reports');
        $response->assertStatus(200);
    }

    /**
     * Test form builder page loads
     */
    public function test_form_builder_page_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/form-builder');
        $response->assertStatus(200);
    }

    /**
     * Test users page loads for admin
     */
    public function test_users_page_loads_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);
    }

    /**
     * Test settings page loads
     */
    public function test_settings_page_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/settings');
        $response->assertStatus(200);
    }

    /**
     * Test workflows page loads
     */
    public function test_workflows_page_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/workflows');
        $response->assertStatus(200);
    }

    /**
     * Test database connection works
     */
    public function test_database_connection_works(): void
    {
        $this->assertDatabaseCount('users', 0);
        
        User::factory()->create();
        
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Test API endpoint requires authentication
     */
    public function test_api_endpoint_requires_api_key(): void
    {
        $response = $this->postJson('/api/leads/ingest', [
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test rate limiting is active
     */
    public function test_rate_limiting_is_active(): void
    {
        // Make 61 requests (rate limit is 60/minute)
        for ($i = 0; $i < 61; $i++) {
            $response = $this->get('/');
        }

        // Last request should be rate limited
        $response->assertStatus(429);
    }

    /**
     * Test HTTPS redirect in production
     */
    public function test_https_redirect_in_production(): void
    {
        // Set environment to production
        config(['app.env' => 'production']);

        $response = $this->get('http://example.com/dashboard', [
            'HTTP_X_FORWARDED_PROTO' => 'http'
        ]);

        // Should redirect to HTTPS
        $response->assertRedirect();
    }

    /**
     * Test password reset can be requested
     */
    public function test_password_reset_can_be_requested(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test user logout works
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->post('/logout');

        $this->assertGuest();
    }
}
