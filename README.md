# Excel Upload for Laravel
This repository provides an efficient solution for uploading and processing large Excel files in a Laravel application. It utilizes background jobs to handle large data sets without compromising the application's performance.

# Screenshots
Screenshots illustrating the key features and steps of the implementation are provided at the end of this README file. Refer to them for visual guidance and better understanding.

# Installation

1. Clone the Repository
   git clone https://github.com/Shristi1919/excel-import-large-data.git

2. Install Dependencies
   Run the following command to install the necessary PHP and Laravel dependencies:
   
   **composer install**

   You will also need to install the maatwebsite/excel package for Excel file handling:

   **composer require maatwebsite/excel**

3. Set Up the Database
   Run the migrations to set up the database schema (if not already configured):
    **php artisan migrate**

# Configuration

1. Environment Configuration
   Update the .env file with the necessary configurations:

   Set the file upload size limit:
    **UPLOAD_MAX_FILE_SIZE=10240**

   Ensure your PHP configuration allows for large file uploads:
    **upload_max_filesize = 10M**
    **post_max_size = 10M**
    
2. Queue Configuration

   Ensure your queue system is correctly configured to handle background jobs:
    In .env, configure the queue connection (e.g., database, Redis):
    **QUEUE_CONNECTION=database**

    Run the queue migration to set up the necessary tables:
    **php artisan queue:table**
    **php artisan migrate**

# Usage

1. Upload Excel File
   To upload an Excel file, use the /upload-excel API route. Send a POST request with the Excel file attached.

    Example Request:
    **POST**:
    - `/api/upload-excel`

    **Headers**:
    - `Content-Type: multipart/form-data`

    **Body**:
    - `file`: `<your_excel_file.xlsx>`


# File Upload Workflow
1) File Validation: The file is validated in the ExcelController to ensure it is an Excel file (.xlsx, .csv) and does not exceed the maximum size limit.
2) Temporary Storage: The file is stored temporarily in the application's storage directory.
3) Job Dispatch: A background job (ExcelImportJob) is dispatched to process the rows of the Excel file asynchronously.
4) Data Import: The rows are read and imported into the database (e.g., the excel_imports table).
5) Clean Up: After processing, the temporary Excel file is deleted.

# Job Processing
1. Job Setup
   The ExcelImportJob job handles the processing of Excel rows asynchronously. It reads the uploaded file and inserts each row into the database. The job is defined in app/Jobs/ExcelImportJob.php.

2. Job Execution
   To process the job, run the queue worker:
    -php artisan queue:work
    This will begin processing any pending jobs that have been queued.

# Testing

1. Testing the Upload Endpoint
   You can test the file upload functionality using Postman or any API client by sending a POST request to /upload-excel with an Excel file attached.

2. Testing Background Job Processing
   To test that the job is properly processed, check the excel_imports table in your database after uploading the Excel file. If the data is correctly imported, the job worked as expected.

# Troubleshooting
   File Upload Errors: If you encounter issues with file uploads, ensure that your upload_max_filesize and post_max_size settings in PHP are correctly configured.

   Queue Worker Not Running: If jobs aren't being processed, ensure that the queue worker is running by executing:
    **php artisan queue:work**

# [Download the sample file- 10000 Row Excel] 
https://docs.google.com/spreadsheets/d/19bcOTZkb1dxVF-nKO_QH7I_Ox0xTmcqRL4vwhfaizEo/edit?usp=sharing

# Screenshot of Excel Data
![Screenshot](https://github.com/Shristi1919/excel-import-large-data/blob/main/public/screenshot/Screenshot%202024-12-25%20205615.png)

# Screenshot of Excel Data (No record Found)
![Screenshot](https://github.com/Shristi1919/excel-import-large-data/blob/main/public/screenshot/Screenshot%202024-12-25%20193235.png)

# Screenshot of File Upload in Process
![Screenshot](https://github.com/Shristi1919/excel-import-large-data/blob/main/public/screenshot/Screenshot%202024-12-25%20205334.png)

# Screenshot of List of Data Uploaded
![Screenshot](https://github.com/Shristi1919/excel-import-large-data/blob/main/public/screenshot/Screenshot%202024-12-25%20205454.png)

# Screenshot of File too Large Validation
![Screenshot](https://github.com/Shristi1919/excel-import-large-data/blob/main/public/screenshot/Screenshot%202024-12-25%20204909.png)

# Screenshot of Invalid File Structure Validation
![Screenshot](https://github.com/Shristi1919/excel-import-large-data/blob/main/public/screenshot/Screenshot%202024-12-25%20205052.png)

# Screenshot of Invalid File Type Validation
![Screenshot](https://github.com/Shristi1919/excel-import-large-data/blob/main/public/screenshot/Screenshot%202024-12-25%20205143.png)



