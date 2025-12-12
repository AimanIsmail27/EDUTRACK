<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTrack - All Courses</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #c0c0c0;
        }

        /* Header */
        .header {
            background-color: #f5f5f5;
            padding: 20px 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 28px;
            font-weight: bold;
        }

        /* Container */
        .container {
            display: flex;
            min-height: calc(100vh - 80px);
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #f5f5f5;
            padding: 20px;
            margin: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }

        .menu-item:hover {
            background-color: #e0e0e0;
        }

        .menu-item.active {
            background-color: #e0e0e0;
        }

        .menu-item i {
            margin-right: 10px;
            font-size: 20px;
        }

        .submenu {
            margin-left: 30px;
        }

        .submenu-item {
            padding: 12px 15px;
            margin-bottom: 5px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: 500;
        }

        .submenu-item:hover {
            background-color: #e0e0e0;
        }

        .submenu-item.active {
            background-color: #d0d0d0;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            margin: 20px 20px 20px 0;
        }

        .content-box {
            background-color: #f5f5f5;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Filter Section */
        .filter-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .filter-section label {
            font-weight: 500;
        }

        .filter-section select,
        .filter-section input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #e8e8e8;
            min-width: 120px;
        }

        .search-btn {
            padding: 8px 24px;
            background-color: #d0d0d0;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            margin-left: auto;
        }

        .search-btn:hover {
            background-color: #c0c0c0;
        }

        /* Table */
        .course-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #e8e8e8;
        }

        .course-table thead {
            background-color: #d0d0d0;
        }

        .course-table th {
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }

        .course-table td {
            padding: 15px;
            border-bottom: 1px solid #ccc;
        }

        .course-table tbody tr {
            background-color: #f5f5f5;
        }

        .course-table tbody tr:hover {
            background-color: #ececec;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
        }

        .btn-view {
            background-color: #28a745;
            color: white;
        }

        .btn-view:hover {
            background-color: #218838;
        }

        .btn-edit {
            background-color: #007bff;
            color: white;
        }

        .btn-edit:hover {
            background-color: #0069d9;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
        }

        .pagination a {
            color: #666;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .pagination a:hover {
            color: #000;
        }

        /* Icons (using text symbols) */
        .icon-home::before { content: "🏠"; }
        .icon-user::before { content: "👤"; }
        .icon-course::before { content: "📚"; }
        .icon-arrow::before { content: "▼"; }
        .arrow-left::before { content: "←"; }
        .arrow-right::after { content: "→"; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>EduTrack</h1>
    </div>

    <!-- Container -->
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="menu-item">
                <span class="icon-home"></span>
                <span>Dashboard</span>
            </div>

            <div class="menu-item">
                <span class="icon-user"></span>
                <span>Register User</span>
                <span class="icon-arrow" style="margin-left: auto;"></span>
            </div>

            <div class="menu-item active">
                <span class="icon-course"></span>
                <span>Manage Course</span>
                <span class="icon-arrow" style="margin-left: auto;"></span>
            </div>

            <div class="submenu">
                <div class="submenu-item active">View All Courses</div>
                <div class="submenu-item">Add New Courses</div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-box">
                <h2 class="page-title">All Courses</h2>

                <!-- Filter Section -->
                <div class="filter-section">
                    <label>Filter by:</label>
                    <select name="filter_type">
                        <option value="CODE">CODE</option>
                        <option value="NAME">NAME</option>
                        <option value="CREDITS">CREDITS</option>
                        <option value="SEMESTER">SEMESTER</option>
                    </select>

                    <label>Criteria:</label>
                    <input type="text" name="criteria" value="BCN" placeholder="Enter criteria">

                    <button class="search-btn">Search</button>
                </div>

                <!-- Course Table -->
                <table class="course-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Course Name</th>
                            <th>Credits</th>
                            <th>Semester</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>BCN1010</td>
                            <td>Statistics</td>
                            <td>3</td>
                            <td>2</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-view">View</button>
                                    <button class="btn btn-edit">Edit</button>
                                    <button class="btn btn-delete">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>BCN1015</td>
                            <td>Data Mining</td>
                            <td>4</td>
                            <td>2</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-view">View</button>
                                    <button class="btn btn-edit">Edit</button>
                                    <button class="btn btn-delete">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>BCN3030</td>
                            <td>Pure Science</td>
                            <td>4</td>
                            <td>2</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-view">View</button>
                                    <button class="btn btn-edit">Edit</button>
                                    <button class="btn btn-delete">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <a href="#" class="arrow-left">Previous</a>
                    <a href="#" class="arrow-right">Next</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>