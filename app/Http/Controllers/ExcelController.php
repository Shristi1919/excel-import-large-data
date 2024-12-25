<?php

namespace App\Http\Controllers;

use App\Imports\ExcelImportData;
use App\Jobs\ExcelImportJob;
use App\Models\ExcelImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    /**
     * Handle the Excel file upload and dispatch the import job.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function upload(Request $request)
    {
        try {
            // Validate the uploaded file
            $request->validate([
                'file' => 'required|mimes:xlsx,csv|max:10240', // Limit file size to 10MB
            ], [
                'file.required' => 'No file uploaded. Please upload a file.',
                'file.mimes' => 'Invalid file type. Only .xlsx and .csv files are allowed.',
                'file.max' => 'The file is too large. Maximum file size is 10MB.',
            ]);

            $file = $request->file('file');

            // Read the first row from the Excel file to validate headers
            $excelHeaders = $this->getExcelHeaders($file);

            // Check if the headers match the expected ones
            if (!$this->validateHeaders($excelHeaders)) {
                return response()->json(['error' => 'Invalid file structure. The file must have columns: name, email, phone, address.'], 422);
            }

            // Store the uploaded file temporarily
            $filePath = $file->store('uploads');

            // Dispatch the job to import the file asynchronously
            ExcelImportJob::dispatch($filePath);

            // Return a success response
            return response()->json(['message' => 'File is being processed.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as a response
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Catch any other exception and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

/**
 * Get the headers of the uploaded Excel file.
 *
 * @param \Illuminate\Http\UploadedFile $file
 * @return array
 */
    private function getExcelHeaders($file)
    {
        // Read the first row of the Excel file to extract headers
        $excelData = Excel::toArray(new ExcelImportData, $file);

        // Get the first row from the Excel data
        $headers = $excelData[0][0]; // Assuming the first row is the header row

        return $headers;
    }

/**
 * Validate the headers.
 *
 * @param array $headers
 * @return bool
 */
    private function validateHeaders($headers)
    {
        // Expected headers
        $expectedHeaders = ['name', 'email', 'phone', 'address'];

        return $headers === $expectedHeaders;
    }

    public function getImportedExcelData(Request $request)
    {
        $importedData = ExcelImport::paginate(100);

        // Return the paginated data as a JSON response
        return response()->json([
            'data' => $importedData,
        ]);
    }

}
