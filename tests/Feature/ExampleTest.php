<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/transaksi')->assertRedirect('/login');
    }

    public function test_user_can_access_dashboard_and_transaction_pages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));

        $this->get('/')->assertOk();
        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Transaksi')
            ->assertDontSee('href="/admin/user"', false)
            ->assertDontSee('href="/admin/kategori"', false)
            ->assertDontSee('href="/admin/produk"', false);
        $this->get('/admin/transaksi')->assertOk()->assertSee('Tambah Transaksi');
        $this->get('/admin/transaksi/create')->assertOk()->assertSee('Pilih Produk');
    }

    public function test_user_is_forbidden_from_admin_only_pages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));

        foreach (['/admin/user', '/admin/user/create', '/admin/kategori', '/admin/kategori/create', '/admin/produk'] as $url) {
            $this->get($url)->assertForbidden();
        }

        $this->post('/admin/user', [])->assertForbidden();
        $this->post('/admin/kategori', [])->assertForbidden();
        $this->post('/admin/produk', [])->assertForbidden();
    }

    public function test_admin_can_access_all_pages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        foreach (['/', '/admin/dashboard', '/admin/user', '/admin/user/create', '/admin/kategori', '/admin/kategori/create', '/admin/produk', '/admin/transaksi'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_admin_can_create_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $email = fake()->unique()->safeEmail();

        $this->actingAs($admin)->post('/admin/user', [
            'name' => 'User Baru',
            'email' => $email,
            'role' => 'user',
            'password' => 'password123',
            're_password' => 'password123',
        ])->assertRedirect('/admin/user');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'user',
        ]);
    }

    public function test_user_role_is_required_and_restricted(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post('/admin/user', [
            'name' => 'Tanpa Role',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password123',
            're_password' => 'password123',
        ])->assertSessionHasErrors('role');

        foreach (['owner', 'kasir'] as $role) {
            $this->post('/admin/user', [
                'name' => 'Role Tidak Valid',
                'email' => fake()->unique()->safeEmail(),
                'role' => $role,
                'password' => 'password123',
                're_password' => 'password123',
            ])->assertSessionHasErrors('role');
        }
    }

    public function test_admin_can_update_a_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)->put('/admin/user/'.$user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'admin',
            'password' => '',
            're_password' => '',
        ])->assertRedirect('/admin/user');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'admin',
        ]);
    }

    public function test_user_can_login_and_is_redirected_to_dashboard(): void
    {
        $email = fake()->unique()->safeEmail();

        User::factory()->create([
            'email' => $email,
            'password' => 'password123',
            'role' => 'user',
        ]);

        $this->post('/login/do', [
            'email' => $email,
            'password' => 'password123',
        ])->assertRedirect('/');
    }

    public function test_unknown_role_cannot_access_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        $this->get('/admin/dashboard')->assertForbidden();
    }

    public function test_transaction_reduces_product_stock_using_current_price(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $produk = $this->createProduct(5);

        $this->actingAs($user)
            ->withSession(['transaksi_cart' => [[
                'produk_id' => $produk->id,
                'qty' => 2,
                'harga' => 1,
                'subtotal' => 2,
            ]]])
            ->post('/admin/transaksi', [
                'user_id' => $user->id,
                'tanggal_transaksi' => now()->toDateString(),
                'uang_bayar' => 30000,
            ])
            ->assertRedirect('/admin/transaksi');

        $this->assertDatabaseHas('produks', ['id' => $produk->id, 'stok' => 3]);

        $transaksi = Transaksi::where('user_id', $user->id)->latest('id')->firstOrFail();
        $this->assertTrue($transaksi->stock_deducted);
        $this->assertDatabaseHas('transaksi_items', [
            'transaksi_id' => $transaksi->id,
            'produk_id' => $produk->id,
            'qty' => 2,
            'subtotal' => 20000,
        ]);
    }

    public function test_transaction_is_rejected_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $produk = $this->createProduct(1);
        $transactionCount = Transaksi::count();

        $this->actingAs($user)
            ->withSession(['transaksi_cart' => [[
                'produk_id' => $produk->id,
                'qty' => 2,
                'subtotal' => 40000,
            ]]])
            ->post('/admin/transaksi', [
                'user_id' => $user->id,
                'tanggal_transaksi' => now()->toDateString(),
                'uang_bayar' => 40000,
            ])
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseHas('produks', ['id' => $produk->id, 'stok' => 1]);
        $this->assertSame($transactionCount, Transaksi::count());
        $this->assertDatabaseMissing('transaksi_items', ['produk_id' => $produk->id]);
    }

    public function test_deleting_new_transaction_restores_product_stock(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $produk = $this->createProduct(5);

        $this->actingAs($user)
            ->withSession(['transaksi_cart' => [[
                'produk_id' => $produk->id,
                'qty' => 2,
                'subtotal' => 20000,
            ]]])
            ->post('/admin/transaksi', [
                'user_id' => $user->id,
                'tanggal_transaksi' => now()->toDateString(),
                'uang_bayar' => 20000,
            ]);

        $transaksi = Transaksi::where('user_id', $user->id)->latest('id')->firstOrFail();

        $this->actingAs($user)->delete('/admin/transaksi/'.$transaksi->id)->assertRedirect('/admin/transaksi');

        $this->assertDatabaseHas('produks', ['id' => $produk->id, 'stok' => 5]);
        $this->assertDatabaseMissing('transaksis', ['id' => $transaksi->id]);
    }

    public function test_deleting_legacy_transaction_does_not_change_product_stock(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $produk = $this->createProduct(5);
        $transaksi = Transaksi::create([
            'user_id' => $user->id,
            'tanggal_transaksi' => now()->toDateString(),
            'uang_bayar' => 20000,
            'kembalian' => 0,
            'stock_deducted' => false,
        ]);
        $transaksi->items()->create([
            'produk_id' => $produk->id,
            'qty' => 2,
            'subtotal' => 20000,
        ]);

        $this->actingAs($user)->delete('/admin/transaksi/'.$transaksi->id)->assertRedirect('/admin/transaksi');

        $this->assertDatabaseHas('produks', ['id' => $produk->id, 'stok' => 5]);
    }

    public function test_cart_rejects_quantity_above_available_stock(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $produk = $this->createProduct(1);

        $this->actingAs($user)
            ->postJson(route('transaksi.cart.add'), [
                'produk_id' => $produk->id,
                'qty' => 2,
            ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('produks', ['id' => $produk->id, 'stok' => 1]);
        $this->assertFalse(session()->has('transaksi_cart'));
    }

    private function createProduct(int $stock): Produk
    {
        $kategori = Kategori::create([
            'name' => 'Kategori '.fake()->unique()->word(),
            'slug' => fake()->unique()->slug(),
        ]);

        return Produk::create([
            'name' => 'Produk '.fake()->unique()->word(),
            'kategori_id' => $kategori->id,
            'harga' => 10000,
            'stok' => $stock,
        ]);
    }
}
