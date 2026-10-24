<?php

namespace Tests\Feature\Admin;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_coupon_creation_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('admin.coupons.create'));

        $response->assertOk()
            ->assertSee('Coupon name')
            ->assertSee('Percentage off')
            ->assertSee('Fixed amount off')
            ->assertSee('Custom (admin text)')
            ->assertSee('Starting date')
            ->assertSee('Ending date');
    }

    public function test_admin_can_create_a_percentage_coupon(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.coupons.store'), [
            'name' => 'Summer Safari',
            'type' => 'percentage',
            'discount_value' => '15',
            'starts_at' => '2030-05-01',
            'ends_at' => '2030-05-31',
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', [
            'name' => 'Summer Safari',
            'type' => 'percentage',
            'discount_value' => 15,
            'custom_text' => null,
        ]);
        $coupon = Coupon::query()->where('name', 'Summer Safari')->firstOrFail();
        $this->assertSame('2030-05-01', $coupon->starts_at->toDateString());
        $this->assertSame('2030-05-31', $coupon->ends_at->toDateString());
    }

    public function test_admin_can_create_a_fixed_amount_coupon(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.coupons.store'), [
            'name' => 'Fixed Safari Discount',
            'type' => 'fixed_amount',
            'discount_value' => '125.50',
            'starts_at' => '2030-05-01',
            'ends_at' => '2030-05-31',
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', [
            'name' => 'Fixed Safari Discount',
            'type' => 'fixed_amount',
            'discount_value' => 125.5,
        ]);
    }

    public function test_admin_can_create_a_custom_coupon_and_the_offer_text_is_escaped(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customText = '<script>alert("xss")</script> Free pickup';

        $response = $this->actingAs($admin)->post(route('admin.coupons.store'), [
            'name' => 'Custom Safari Offer',
            'type' => 'custom',
            'custom_text' => $customText,
            'starts_at' => '2030-05-01',
            'ends_at' => '2030-05-31',
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', [
            'name' => 'Custom Safari Offer',
            'type' => 'custom',
            'discount_value' => null,
            'custom_text' => $customText,
        ]);

        $this->actingAs($admin)->get(route('admin.coupons.index'))
            ->assertOk()
            ->assertSee($customText)
            ->assertDontSee($customText, false);
    }

    public function test_admin_cannot_create_a_percentage_coupon_over_one_hundred_percent(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->from(route('admin.coupons.create'))
            ->post(route('admin.coupons.store'), [
                'name' => 'Invalid discount',
                'type' => 'percentage',
                'discount_value' => '100.01',
                'starts_at' => '2030-05-01',
                'ends_at' => '2030-05-31',
            ]);

        $response->assertRedirect(route('admin.coupons.create'))
            ->assertSessionHasErrors('discount_value');
        $this->assertDatabaseCount('coupons', 0);
    }

    public function test_admin_cannot_create_a_coupon_ending_before_it_starts(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->from(route('admin.coupons.create'))
            ->post(route('admin.coupons.store'), [
                'name' => 'Invalid dates',
                'type' => 'fixed_amount',
                'discount_value' => '50',
                'starts_at' => '2030-05-31',
                'ends_at' => '2030-05-01',
            ]);

        $response->assertRedirect(route('admin.coupons.create'))
            ->assertSessionHasErrors('ends_at');
        $this->assertDatabaseCount('coupons', 0);
    }

    public function test_admin_must_enter_custom_offer_text_for_a_custom_coupon(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->from(route('admin.coupons.create'))
            ->post(route('admin.coupons.store'), [
                'name' => 'Missing custom offer',
                'type' => 'custom',
                'starts_at' => '2030-05-01',
                'ends_at' => '2030-05-31',
            ]);

        $response->assertRedirect(route('admin.coupons.create'))
            ->assertSessionHasErrors('custom_text');
        $this->assertDatabaseCount('coupons', 0);
    }

    public function test_non_admin_users_cannot_create_coupons(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('admin.coupons.create'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_guests_are_redirected_from_coupon_creation(): void
    {
        $response = $this->get(route('admin.coupons.create'));

        $response->assertRedirect(route('admin.login'));
    }
}
