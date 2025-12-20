<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LearningMaterial;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    /**
     * Store a newly uploaded learning material.
     */
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'course_code' => 'required|exists:courses,C_Code',
            'week_number' => 'required|integer|min:1|max:14',
            'title'       => 'required|string|max:255',
            'category'    => 'required|string|in:Notes,Lab Sheet,Slides,Reference,Other',
            'file'        => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip|max:10240', // Max 10MB
        ]);

        try {
            // 2. Handle File Upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();

                // Generate a unique filename to prevent overwriting
                $fileName = time() . '_' . str_replace(' ', '_', $originalName);
                
                // Store file in: storage/app/public/materials/{course_code}/week_{number}
                $path = $file->storeAs(
                    'public/materials/' . $request->course_code . '/week_' . $request->week_number,
                    $fileName
                );

                // 3. Save to Database
                LearningMaterial::create([
                    'course_code'        => $request->course_code,
                    'user_id'            => Auth::id(),
                    'week_number'        => $request->week_number,
                    'title'              => $request->title,
                    'category'           => $request->category,
                    'file_path'          => $path,
                    'file_original_name' => $originalName,
                    'file_extension'     => $extension,
                ]);

                return back()->with('success', 'Material uploaded successfully!');
            }

            return back()->with('error', 'File upload failed.');

        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Download the material.
     */
    public function download($id)
    {
        $material = LearningMaterial::findOrFail($id);
        
        // Ensure the file exists in storage
        if (!Storage::exists($material->file_path)) {
            return back()->with('error', 'File not found on server.');
        }

        return Storage::download($material->file_path, $material->file_original_name);
    }

    /**
     * Delete the material.
     */
    public function destroy($id)
    {
        $material = LearningMaterial::findOrFail($id);

        // Delete the physical file first
        Storage::delete($material->file_path);

        // Delete the database record
        $material->delete();

        return back()->with('success', 'Material deleted successfully.');
    }
}