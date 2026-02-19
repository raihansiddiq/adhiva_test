<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
    private $externalUrl = 'https://ogienurdiana.com/career/ecc694ce4e7f6e45a5a7912cde9fe131';

    private function fetchData()
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0'
                ])
                ->get($this->externalUrl);

            if (!$response->successful()) {
                return null;
            }

            return $response->json();

        } catch (\Exception $e) {
            return null;
        }
    }

    public function search(Request $request)
    {
        $data = $this->fetchData();

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data external'
            ], 500);
        }

        $query = collect($data);

        if ($request->has('nama')) {
            $query = $query->where('nama', $request->nama);
        }

        if ($request->has('nim')) {
            $query = $query->where('nim', $request->nim);
        }

        if ($request->has('ymd')) {
            $query = $query->where('ymd', $request->ymd);
        }

        $result = $query->values();

        return response()->json([
            'status' => true,
            'message' => 'Search berhasil',
            'total' => $result->count(),
            'data' => $result
        ]);
    }
}
