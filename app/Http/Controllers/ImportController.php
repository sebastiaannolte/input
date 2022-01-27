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
        return Inertia::render('Import', [
            'data' => Import::where('is_completed', false)->get()
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
        //Create ImportPolicy
        // if (Request::user()->cannot('delete', $bet)) {
        //     abort(403);
        // }

        $import->delete();
        return redirect()->route('import.index')->with('success', 'Import deleted');;
    }
}
