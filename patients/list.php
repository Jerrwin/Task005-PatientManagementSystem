<?php
require_once '../includes/header.php';
require_once '../config/db_connect.php';

$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$doctors_result = $conn->query("SELECT doctor_id, doctor_name, specialization FROM doctor");

$search = "";
$search_query = "1=1";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $search_query = "(patient_name LIKE '%$search%' OR diagnosis LIKE '%$search%')";
}

$sort_option = isset($_GET['sort_option']) ? $_GET['sort_option'] : 'newest';

switch ($sort_option) {
    case 'name_asc':
        $sort = "patient_name";
        $order = "ASC";
        break;
    case 'name_desc':
        $sort = "patient_name";
        $order = "DESC";
        break;
    case 'age_asc':
        $sort = "age";
        $order = "ASC";
        break;
    case 'age_desc':
        $sort = "age";
        $order = "DESC";
        break;
    case 'oldest':
        $sort = "created_at";
        $order = "ASC";
        break;
    default:
        $sort = "created_at";
        $order = "DESC";
        break;
}

// --- 4. QUERY EXECUTION ---
$total_sql = "SELECT COUNT(*) FROM patient WHERE $search_query";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);

$sql = "SELECT patient.*, doctor.doctor_name 
        FROM patient 
        LEFT JOIN doctor ON patient.doctor_id = doctor.doctor_id
        WHERE $search_query 
        ORDER BY $sort $order 
        LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<div class="container mt-4">

    <?php
    if (isset($_SESSION['status'])) {
        echo $_SESSION['status'];
        unset($_SESSION['status']);
    }
    ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center g-3">

                <div class="col-12 col-md-3">
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                        data-bs-target="#addPatientModal">
                        + Add New Patient
                    </button>
                </div>

                <div class="col-12 col-md-5">
                    <form action="list.php" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2"
                            placeholder="Search name or diagnosis..." value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="sort_option" value="<?php echo $sort_option; ?>">

                        <button type="submit" class="btn btn-outline-secondary me-2">Search</button>

                        <?php if (!empty($search) || $sort_option != 'newest'): ?>
                            <a href="list.php" class="btn btn-danger" title="Clear Filters">X</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="col-12 col-md-4">
                    <form action="list.php" method="GET">
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <div class="input-group">
                            <label class="input-group-text bg-light">Sort By</label>
                            <select name="sort_option" class="form-select" onchange="this.form.submit()">
                                <option value="newest" <?php if ($sort_option == 'newest')
                                    echo 'selected'; ?>>Newest
                                    First</option>
                                <option value="oldest" <?php if ($sort_option == 'oldest')
                                    echo 'selected'; ?>>Oldest
                                    First</option>
                                <option value="name_asc" <?php if ($sort_option == 'name_asc')
                                    echo 'selected'; ?>>Name
                                    (A-Z)</option>
                                <option value="name_desc" <?php if ($sort_option == 'name_desc')
                                    echo 'selected'; ?>>Name
                                    (Z-A)</option>
                                <option value="age_asc" <?php if ($sort_option == 'age_asc')
                                    echo 'selected'; ?>>Age
                                    (Youngest)</option>
                                <option value="age_desc" <?php if ($sort_option == 'age_desc')
                                    echo 'selected'; ?>>Age
                                    (Oldest)</option>
                            </select>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 text-nowrap">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Doctor</th>
                            <th>Diagnosis</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['age']); ?></td>
                                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                    <td>
                                        <?php if ($row['doctor_name']): ?>
                                            <span class="badge bg-info text-dark">Dr.
                                                <?php echo htmlspecialchars($row['doctor_name']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($row['diagnosis']); ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="edit.php?id=<?php echo $row['patient_id']; ?>" class="btn btn-sm text-warning">
                                            <img src="../assets/images/edit.png" alt="Edit" style="width: 16px; height: 16px;">
                                        </a>
                                        <a href="delete.php?id=<?php echo $row['patient_id']; ?>" class="btn btn-sm text-danger"
                                            onclick="return confirm('Are you sure you want to delete this patient?');">
                                            <img src="../assets/images/delete.png" alt="Delete"
                                                style="width: 16px; height: 16px;">
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">No patients found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end">
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item <?php if ($page <= 1)
                        echo 'disabled'; ?>">
                        <a class="page-link"
                            href="<?php if ($page > 1)
                                echo "?page=" . ($page - 1) . "&search=$search&sort_option=$sort_option"; ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($page == $i)
                            echo 'active'; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&sort_option=<?php echo $sort_option; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($page >= $total_pages)
                        echo 'disabled'; ?>">
                        <a class="page-link"
                            href="<?php if ($page < $total_pages)
                                echo "?page=" . ($page + 1) . "&search=$search&sort_option=$sort_option"; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="addPatientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Patient</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form action="create.php" method="POST">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0"> <label class="form-label">Patient Name</label>
                            <input type="text" name="patient_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-6 col-md-3"> <label class="form-label">Age</label>
                            <input type="number" name="age" class="form-control" required>
                        </div>
                        <div class="col-6 col-md-3"> <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign Doctor</label>
                        <select name="doctor_id" class="form-select">
                            <option value="">-- Select a Doctor (Optional) --</option>
                            <?php
                            if ($doctors_result->num_rows > 0) {
                                $doctors_result->data_seek(0);
                                while ($doc = $doctors_result->fetch_assoc()) {
                                    echo '<option value="' . $doc['doctor_id'] . '">' . "Dr. " .
                                        htmlspecialchars($doc['doctor_name']) . ' (' . htmlspecialchars($doc['specialization']) . ')' .
                                        '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Diagnosis</label>
                        <textarea name="diagnosis" class="form-control" rows="3"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const alertBox = document.querySelector('.alert-success');
        if (alertBox) {
            setTimeout(function () {
                let alert = new bootstrap.Alert(alertBox);
                alert.close();
            }, 3000);
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>