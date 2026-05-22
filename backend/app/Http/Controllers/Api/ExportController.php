<?php

namespace App\Http\Controllers\Api;

use App\Exports\ProfilesExport;
use App\Exports\ProductsExport;
use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function products(string $format): Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $rows = Product::orderBy('code')->get();

        return $this->export('productos', $format, $rows, ProductsExport::class, [
            'Código', 'Nombre', 'Marca', 'Precio', 'Fecha creación',
        ], fn ($p) => [
            $p->code,
            $p->name,
            $p->brand,
            $p->price,
            $p->created_at?->format('d/m/Y H:i'),
        ]);
    }

    public function users(string $format): Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $rows = User::orderBy('code')->get();

        return $this->export('usuarios', $format, $rows, UsersExport::class, [
            'Código', 'Usuario', 'Nombre', 'Fecha creación',
        ], fn ($u) => [
            $u->code,
            $u->username,
            $u->name,
            $u->created_at?->format('d/m/Y H:i'),
        ]);
    }

    public function profiles(string $format): Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $rows = Profile::orderBy('code')->get();

        return $this->export('perfiles', $format, $rows, ProfilesExport::class, [
            'Código', 'Nombre', 'Fecha creación',
        ], fn ($p) => [
            $p->code,
            $p->name,
            $p->created_at?->format('d/m/Y H:i'),
        ]);
    }

    private function export(
        string $name,
        string $format,
        $rows,
        string $exportClass,
        array $headers,
        callable $mapper,
    ): Response|\Symfony\Component\HttpFoundation\BinaryFileResponse {
        $data = $rows->map($mapper)->values()->all();

        if ($format === 'excel') {
            return Excel::download(new $exportClass($data), "{$name}.xlsx");
        }

        $pdf = Pdf::loadView('exports.table', [
            'title' => 'Listado de '.ucfirst($name),
            'headers' => $headers,
            'rows' => $data,
        ]);

        return $pdf->download("{$name}.pdf");
    }
}
