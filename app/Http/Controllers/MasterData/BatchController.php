<?php

namespace App\Http\Controllers\MasterData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Batch;

class BatchController extends Controller
{
    
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 10);
        $search  = $request->input('search');
        $companycode = Session::get('companycode');
    
        $query = Batch::where('companycode', $companycode);
    
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('batchno', 'like', "%{$search}%")
                  ->orWhere('plot', 'like', "%{$search}%")
                  ->orWhere('kodevarietas', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('plantingrkhno', 'like', "%{$search}%");
            });
        }

        $batch = $query
            ->orderBy('batchdate', 'desc')
            ->orderBy('batchno', 'desc')
            ->paginate($perPage)
            ->appends([
                'perPage' => $perPage,
                'search'  => $search,
            ]);
    
        return view('master.batch.index', [
            'batch' => $batch,
            'title'     => 'Data Batch',
            'navbar'    => 'Master',
            'nav'       => 'Batch',
            'perPage'   => $perPage,
            'search'    => $search,
        ]);
    }

    public function store(Request $request)
    {
        $companycode = Session::get('companycode');
        
        $request->validate([
            'batchno' => 'required|string|max:20',
            'plot' => 'required|string|max:5',
            'batchdate' => 'required|date',
            'batcharea' => 'required|numeric|min:0|max:9999.99',
            'kodevarietas' => 'nullable|string|max:10',
            'status' => 'required|in:ACTIVE,HARVESTED',
            'plantingrkhno' => 'nullable|string|max:15',
        ]);

        $exists = Batch::where('batchno', $request->batchno)->exists();
            
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'batchno' => 'Duplicate Entry, Batch Number already exists'
                ]);
        }

        Batch::create([
            'batchno' => $request->input('batchno'),
            'companycode' => $companycode,
            'plot' => $request->input('plot'),
            'batchdate' => $request->input('batchdate'),
            'batcharea' => $request->input('batcharea'),
            'kodevarietas' => $request->input('kodevarietas'),
            'status' => $request->input('status'),
            'plantingrkhno' => $request->input('plantingrkhno'),
            'inputby' => Auth::user()->userid,
            'createdat' => now(),
        ]);

        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }

    public function update(Request $request, $batchno)
    {   
        $companycode = Session::get('companycode');
        
        $batch = Batch::where('batchno', $batchno)
            ->where('companycode', $companycode)
            ->firstOrFail();

        $validated = $request->validate([
            'batchno' => 'required|string|max:20',
            'plot' => 'required|string|max:5',
            'batchdate' => 'required|date',
            'batcharea' => 'required|numeric|min:0|max:9999.99',
            'kodevarietas' => 'nullable|string|max:10',
            'status' => 'required|in:ACTIVE,HARVESTED',
            'plantingrkhno' => 'nullable|string|max:15',
        ]);
        
        // Check duplicate if batchno changed
        if ($request->batchno !== $batch->batchno) {
            $exists = Batch::where('batchno', $request->batchno)->exists();
    
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'batchno' => 'Duplicate Entry, Batch Number already exists'
                    ]);
            }
        }
        
        $batch->update([
            'batchno' => $validated['batchno'],
            'plot' => $validated['plot'],
            'batchdate' => $validated['batchdate'],
            'batcharea' => $validated['batcharea'],
            'kodevarietas' => $validated['kodevarietas'],
            'status' => $validated['status'],
            'plantingrkhno' => $validated['plantingrkhno'],
        ]);
    
        return redirect()->back()->with('success', 'Data berhasil di‑update.');
    }

    public function destroy(Request $request, $batchno)
    {
        $companycode = Session::get('companycode');

        Batch::where('batchno', $batchno)
            ->where('companycode', $companycode)
            ->delete();

        return redirect()->back()->with('success','Data berhasil di‑hapus.');
    }
}