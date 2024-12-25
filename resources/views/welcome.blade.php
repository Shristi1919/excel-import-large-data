<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imported Excel Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        /* Container for the table and pagination */
        .container {
            max-width: 1000px;
            margin-top: 20px;
            padding: 0 20px;
            /* Added padding for better container spacing */
        }

        /* Table styles */
        .table th,
        .table td {
            text-align: center;
            padding: 12px;
        }

        /* Pagination styles */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            padding: 0;
            list-style: none;
            flex-wrap: wrap;
            /* Ensure pagination items wrap when necessary */
            justify-content: flex-start;
            /* Align pagination to the left within the table container */
        }

        /* Each page item */
        .pagination .page-item {
            margin: 0 5px;
        }

        /* Page links */
        .pagination .page-link {
            color: #007bff;
            border-radius: 5px;
            padding: 8px 16px;
            border: 1px solid #ddd;
            white-space: nowrap;
            /* Prevent page numbers from wrapping */
        }

        /* Active page link */
        .pagination .active .page-link {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        /* Disabled page link */
        .pagination .disabled .page-link {
            color: #ccc;
            pointer-events: none;
        }

        /* Filter section styling */
        .filter-section {
            margin-bottom: 20px;
        }

        /* Filter input fields */
        .filter-input {
            width: 200px;
            margin-right: 10px;
            padding: 8px;
        }

        /* Focus state for filter inputs */
        .filter-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Excel Imported Data Table</h1>

        <!-- Filters -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" class="form-control filter-input" id="filterName"
                        placeholder="Search by Name">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control filter-input" id="filterPhone"
                        placeholder="Search by Phone">
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Pagination controls -->
            <div id="pagination" class="pagination pb-2"></div>
            <!-- Table to display data -->
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody"></tbody>
            </table>

        </div>


    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentPage = 1;
        const pageRange = 12; // Number of pages to display at once

        // Fetch data and render the table
        function fetchData(page = 1) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/getlist?page=${page}`,
                method: 'GET',
                success: function(response) {
                    // Render table rows
                    renderTable(response.data.data);

                    // Render pagination
                    renderPagination(response.data.current_page, response.data.last_page);
                }
            });
        }

        // Render table data
        function renderTable(data) {
            const tableBody = $('#dataTableBody');
            tableBody.empty();

            // Check if there is any data
            if (data.length === 0) {
                const noRecordsRow = `
            <tr>
                <td colspan="5" class="text-center">No records found</td>
            </tr>
        `;
                tableBody.append(noRecordsRow);
            } else {
                data.forEach(item => {
                    const row = `
                <tr>
                    <td>${item.id}</td>
                    <td>${item.name}</td>
                    <td>${item.email}</td>
                    <td>${item.phone}</td>
                    <td>${item.address}</td>
                </tr>
            `;
                    tableBody.append(row);
                });
            }
        }


        // Render pagination controls
        function renderPagination(currentPage, lastPage) {
            const pagination = $('#pagination');
            pagination.empty();

            let paginationHTML = '';

            // Calculate the start and end page for the pagination range
            const startPage = Math.floor((currentPage - 1) / pageRange) * pageRange + 1;
            const endPage = Math.min(startPage + pageRange - 1, lastPage);

            // Previous button
            paginationHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="fetchData(${currentPage - 1})">&laquo; Previous</a>
        </li>
    `;

            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                paginationHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="fetchData(${i})">${i}</a>
            </li>
        `;
            }

            // Next button
            paginationHTML += `
        <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="fetchData(${currentPage + 1})">Next &raquo;</a>
        </li>
    `;

            pagination.html(paginationHTML);
        }


        // Filter functionality by Name and Phone
        function applyFilters() {
            const filterName = $('#filterName').val().toLowerCase();
            const filterPhone = $('#filterPhone').val().toLowerCase();

            let foundRecord = false;

            $('#dataTableBody tr').each(function() {
                const name = $(this).find('td:nth-child(2)').text().toLowerCase();
                const phone = $(this).find('td:nth-child(4)').text().toLowerCase();

                if (name.includes(filterName) && phone.includes(filterPhone)) {
                    $(this).show();
                    foundRecord = true;
                } else {
                    $(this).hide();
                }
            });

            // If no record found, show 'No records found' message
            if (!foundRecord) {
                const noRecordsRow = `
            <tr>
                <td colspan="5" class="text-center">No records found</td>
            </tr>
        `;
                $('#dataTableBody').append(noRecordsRow);
            }
        }

        // Event listeners for filters
        $('#filterName, #filterPhone').on('keyup', function() {
            applyFilters();
        });


        // Initial fetch
        fetchData(currentPage);
    </script>
</body>

</html>
