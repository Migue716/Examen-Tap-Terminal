<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\AuditLogService;
use App\Services\CodeGeneratorService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private CodeGeneratorService $codeGenerator,
        private AuditLogService $auditLog,
    ) {}

    public function index(): JsonResponse
    {
        $products = Product::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $products->map(fn ($p) => ApiResponse::product($p->toArray())),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        return response()->json(['data' => ApiResponse::product($product->toArray())]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create([
            'code' => $this->codeGenerator->next('products', 'PRD'),
            ...$request->validated(),
        ]);

        $this->auditLog->log('products', (string) $product->_id, 'create', null, $product->toArray());

        return response()->json(['data' => ApiResponse::product($product->toArray())], 201);
    }

    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $previous = $product->toArray();
        $product->update($request->validated());
        $this->auditLog->log('products', (string) $product->_id, 'update', $previous, $product->fresh()->toArray());

        return response()->json(['data' => ApiResponse::product($product->fresh()->toArray())]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $previous = $product->toArray();
        $product->delete();
        $this->auditLog->log('products', $id, 'delete', $previous, null);

        return response()->json(['message' => 'Producto eliminado.']);
    }
}
