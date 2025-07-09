<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions; // Changement ici
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Delivery;
use Illuminate\Support\Arr;

class OrderToDeliveryTest extends TestCase
{
    // use DatabaseTransactions; // Au lieu de RefreshDatabase

    public function test_order_using_existing_products_until_delivery(): void
    {
        // Créer un utilisateur fictif
        $user = User::factory()->create();

        // Vérifier qu'au moins un produit existe
        $products = Product::all();
        $this->assertNotEmpty($products, 'Aucun produit existant trouvé dans la base de données');

        // Choisir 3 produits existants (au lieu de 5 pour éviter de trop polluer)
        $selectedProducts = $products->random(min(3, $products->count()));

        foreach ($selectedProducts as $product) {
            $order = Order::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'status' => 'en_attente',
                'total' => $product->price,
                'shipping_address' => 'Rue 10, Cocody',
                'shipping_city' => 'Abidjan',
                'shipping_country' => 'Côte d\'Ivoire',
                'shipping_zip' => '00225',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'size' => 'M',
                'unit_price' => $product->price,
                'total_price' => $product->price,
            ]);

            Payment::create([
                'order_id' => $order->id,
                'amount' => $product->price,
                'method' => Arr::random(['carte', 'paypal', 'virement', 'especes']),
                'status' => Arr::random(['en_attente', 'reussi', 'echoue', 'rembourse']),
                'transaction_id' => Str::random(10),
                'paid_at' => now(),
            ]);

            Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => strtoupper(Str::random(8)),
                'amount' => $product->price,
                'issued_at' => now(),
            ]);

            Delivery::create([
                'order_id' => $order->id,
                'adresse_livraison' => 'Rue 10, Cocody',
                'transporteur' => 'DHL',
                'statut' => 'livre',
                'date_estimee' => now()->addDays(3),
            ]);
        }

        $this->assertDatabaseCount('orders', $selectedProducts->count());
        $this->assertDatabaseCount('deliveries', $selectedProducts->count());
        $this->assertDatabaseHas('deliveries', ['statut' => 'livre']);
    }
}
