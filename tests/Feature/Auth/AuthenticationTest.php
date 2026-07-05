<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_personnel_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.personnel-login');
    }

    public function test_personnel_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => 'nss_personnel',
            'must_change_password' => false,
        ]);

        $component = Volt::test('pages.auth.personnel-login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('personnel.dashboard', absolute: false));

        $this->assertAuthenticated('personnel');
    }

    public function test_company_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/company/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.login');
    }

    public function test_hr_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/hr/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.hr-login');
    }

    public function test_company_admin_can_authenticate_using_the_company_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => 'company_admin',
            'must_change_password' => false,
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('company.dashboard', absolute: false));

        $this->assertAuthenticated('company');
    }

    public function test_hr_staff_can_authenticate_using_the_hr_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => 'hr_staff',
            'must_change_password' => false,
        ]);

        $component = Volt::test('pages.auth.hr-login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('company.dashboard', absolute: false));

        $this->assertAuthenticated('company');
    }

    public function test_hr_staff_cannot_authenticate_on_company_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => 'hr_staff',
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasErrors('form.email')
            ->assertNoRedirect();

        $this->assertGuest('company');
    }

    public function test_company_admin_cannot_authenticate_on_hr_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => 'company_admin',
        ]);

        $component = Volt::test('pages.auth.hr-login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasErrors('form.email')
            ->assertNoRedirect();

        $this->assertGuest('company');
    }

    public function test_company_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'role' => 'company_admin',
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors()
            ->assertNoRedirect();

        $this->assertGuest('company');
    }

    public function test_navigation_menu_can_be_rendered(): void
    {
        $user = User::factory()->create([
            'role' => 'company_admin',
            'must_change_password' => false,
        ]);

        $this->actingAs($user, 'company');

        $response = $this->get('/company/dashboard');

        $response
            ->assertOk()
            ->assertSeeVolt('layout.navigation');
    }

    public function test_company_admin_can_logout_to_company_login(): void
    {
        $user = User::factory()->create([
            'role' => 'company_admin',
            'must_change_password' => false,
        ]);

        $this->actingAs($user, 'company');

        $component = Volt::test('layout.navigation');

        $component->call('logout');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('company.login', absolute: false));

        $this->assertGuest('company');
    }

    public function test_hr_staff_can_logout_to_hr_login(): void
    {
        $user = User::factory()->create([
            'role' => 'hr_staff',
            'must_change_password' => false,
        ]);

        $this->actingAs($user, 'company');

        $component = Volt::test('layout.navigation');

        $component->call('logout');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('hr.login', absolute: false));

        $this->assertGuest('company');
    }
}
