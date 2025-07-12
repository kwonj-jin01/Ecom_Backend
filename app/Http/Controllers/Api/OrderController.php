<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{

    /**
     * Créer une commande effectue la commande finale
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_country' => 'required|string|max:100',
            'shipping_zip' => 'nullable|string|max:20',
            'total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
            'payment.amount' => 'required|numeric|min:0',
            'payment.method' => 'required|in:carte,paypal,virement,especes',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Génération du numéro de commande unique
            $orderNumber = $this->generateOrderNumber();

            // Création de la commande
            $order = DB::table('orders')->insertGetId([
                'id' => Str::uuid(),
                'order_number' => $orderNumber,
                'user_id' => $request->user_id,
                'status' => 'en_attente',
                'total' => $request->total,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_country' => $request->shipping_country,
                'shipping_zip' => $request->shipping_zip,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Récupération de l'ID de la commande créée
            $orderId = DB::table('orders')->where('order_number', $orderNumber)->value('id');

            // Création des articles de commande
            $orderItems = [];
            foreach ($request->items as $item) {
                $orderItems[] = [
                    'id' => Str::uuid(),
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('order_items')->insert($orderItems);

            // Création du paiement
            $paymentId = Str::uuid();
            DB::table('payments')->insert([
                'id' => $paymentId,
                'order_id' => $orderId,
                'amount' => $request->payment['amount'],
                'method' => $request->payment['method'],
                'status' => 'en_attente',
                'transaction_id' => null, // À remplir lors du traitement du paiement
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Génération de la facture
            $invoiceNumber = $this->generateInvoiceNumber();
            $invoiceId = Str::uuid();
            DB::table('invoices')->insert([
                'id' => $invoiceId,
                'order_id' => $orderId,
                'invoice_number' => $invoiceNumber,
                'amount' => $request->total,
                'issued_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Récupération des données de la commande créée
            $orderData = DB::table('orders')
                ->where('id', $orderId)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'order' => [
                    'id' => $orderData->id,
                    'order_number' => $orderData->order_number,
                    'status' => $orderData->status,
                    'total' => $orderData->total,
                    'created_at' => $orderData->created_at,
                ],
                'payment_id' => $paymentId,
                'invoice_number' => $invoiceNumber,
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère un numéro de commande unique
     */
    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'CMD-' . date('Y') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (DB::table('orders')->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Génère un numéro de facture unique
     */
    private function generateInvoiceNumber(): string
    {
        do {
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (DB::table('invoices')->where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    /**
     * Récupère les commandes d'un utilisateur
     */
    public function getUserOrders(Request $request)
    {
        $userId = $request->user()->id;

        $orders = DB::table('orders')
            ->select([
                'orders.id',
                'orders.order_number',
                'orders.status',
                'orders.total',
                'orders.created_at',
                'payments.status as payment_status',
                'invoices.invoice_number'
            ])
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->leftJoin('invoices', 'orders.id', '=', 'invoices.order_id')
            ->where('orders.user_id', $userId)
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    /**
     * Récupère les détails d'une commande
     */
    public function show($id)
    {
        $order = DB::table('orders')
            ->select([
                'orders.*',
                'payments.method as payment_method',
                'payments.status as payment_status',
                'payments.paid_at',
                'invoices.invoice_number'
            ])
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->leftJoin('invoices', 'orders.id', '=', 'invoices.order_id')
            ->where('orders.id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        }

        // Récupération des articles de la commande
        $orderItems = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select([
                'order_items.*',
                'products.name as product_name',
                'products.image_url'
            ])
            ->where('order_items.order_id', $id)
            ->get();

        return response()->json([
            'success' => true,
            'order' => $order,
            'items' => $orderItems
        ]);
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:en_attente,confirme,en_production,pret,expedie,livre,annule'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $updated = DB::table('orders')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès'
        ]);
    }
}
