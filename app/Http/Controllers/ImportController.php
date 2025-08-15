<?php

namespace App\Http\Controllers;

use App\Models\Import;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Inertia\Inertia;

class ImportController extends Controller
{
    public function index()
    {

        $import = Import::where('is_completed', false)->get()->map(function ($import) {
            $games = collect($import->data['games'])->map(function ($game) {
                $game['selection'] = preg_replace('/ \(Afgehandeld volgens Opta-gegevens\)/', '', $game['selection']);
                return $game;
            })->toArray();

            // Update the model's data attribute directly
            $data = $import->data;
            $data['games'] = $games;
            $import->data = $data;

            return $import;
        });

        return Inertia::render('Import', [
            'data' => $import,
        ]);
    }

    public function update()
    {
        $id = FacadesRequest::get('id');
        $import = Import::find($id);
        $import->update([
            'is_completed' => true,
        ]);
    }

    public function delete(Import $import)
    {
        $import->delete();

        return Redirect::back()->with('success', 'Import row deleted');
    }
}
