<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Delivery;

class OrderToDeliveryTest extends TestCase
{
    public function test_multiple_users_orders_payments_and_deliveries(): void
    {
        // Nombre d’utilisateurs / commandes que l’on veut créer
        $usersCount = 50;

        // S’assurer qu’il y a au moins un produit en base
        $products = Product::all();
        $this->assertNotEmpty($products, 'Aucun produit existant trouvé dans la base de données');

        // --- Boucle principale : 1 utilisateur  => 1 commande de 3 produits (max) ---
        for ($i = 0; $i < $usersCount; $i++) {
            /** @var \App\Models\User $user */
            $user = User::factory()->create();

            // Sélection aléatoire (max 3) de produits existants
            $selectedProducts = $products->random(min(3, $products->count()));

            // Total de la commande = somme des prix des produits choisis
            $orderTotal = $selectedProducts->sum('price');

            /** @var \App\Models\Order $order */
            $order = Order::create([
                'id'                => (string) Str::uuid(),
                'user_id'           => $user->id,
                'status'            => Arr::random(['en_attente', 'confirme', 'expedie', 'livre', 'annule']),
                'total'             => $orderTotal,
                'shipping_address'  => 'Rue 10, Cocody',
                'shipping_city'     => 'Abidjan',
                'shipping_country'  => 'Côte d\'Ivoire',
                'shipping_zip'      => '00225',
            ]);

            // Créer les OrderItem et additionner les prix
            foreach ($selectedProducts as $product) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $product->id,
                    'quantity'    => 1,
                    'size'        => 'M',
                    'unit_price'  => $product->price,
                    'total_price' => $product->price,
                ]);
            }

            // Paiement (méthode et statut aléatoires VALIDES)
            Payment::create([
                'order_id'       => $order->id,
                'amount'         => $orderTotal,
                'method'         => Arr::random(['carte', 'paypal', 'virement', 'especes']),
                'status'         => Arr::random(['en_attente', 'reussi', 'echoue', 'rembourse']),
                'transaction_id' => Str::random(10),
                'paid_at'        => now(),
            ]);

            // Facture
            Invoice::create([
                'order_id'       => $order->id,
                'invoice_number' => strtoupper(Str::random(8)),
                'amount'         => $orderTotal,
                'issued_at'      => now(),
            ]);

            // Livraison
            Delivery::create([
                'order_id'          => $order->id,
                'adresse_livraison' => 'Rue 10, Cocody',
                'transporteur'      => 'DHL',
                'statut'            => 'livre',
                'date_estimee'      => now()->addDays(3),
            ]);
        }

        // ----------------- Assertions globales -----------------
        $this->assertDatabaseCount('users', $usersCount);
        $this->assertDatabaseCount('orders', $usersCount);
        $this->assertDatabaseCount('deliveries', $usersCount);

        // Vérifier qu’il existe au moins une livraison au statut 'livre'
        $this->assertDatabaseHas('deliveries', ['statut' => 'livre']);
    }
}
