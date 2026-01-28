<?php
require_once '../includes/header.php';
require_once '../config/db_connect.php';

$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$search = "";
$search_query = "1=1";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $search_query = "(doctor_name LIKE '%$search%' OR specialization LIKE '%$search%')";
}


$total_sql = "SELECT COUNT(*) FROM doctor WHERE $search_query";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);

$sql = "SELECT * FROM doctor 
        WHERE $search_query 
        ORDER BY doctor_name ASC 
        LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<div class="container mt-4">

    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row">
                <div class="col-md-12">
                    <form action="list.php" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2"
                            placeholder="Search for a doctor or specialization..."
                            value="<?php echo htmlspecialchars($search); ?>">

                        <button type="submit" class="btn btn-outline-primary me-2">Search</button>

                        <?php if (!empty($search)): ?>
                            <a href="list.php" class="btn btn-danger" title="Clear Search">X</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Doctor List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%;">ID</th>
                            <th style="width: 40%;">Doctor Name</th>
                            <th style="width: 50%;">Specialization</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['doctor_id']; ?></td>
                                    <td class="fw-bold">
                                        Dr. <?php echo htmlspecialchars($row['doctor_name']); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark" style="font-size: 0.9em;">
                                            <?php echo htmlspecialchars($row['specialization']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">No doctors found.</td>
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
                        <a class="page-link" href="<?php if ($page > 1)
                            echo "?page=" . ($page - 1) . "&search=$search"; ?>">Previous</a>
                    </li>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($page == $i)
                            echo 'active'; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?php if ($page >= $total_pages)
                        echo 'disabled'; ?>">
                        <a class="page-link" href="<?php if ($page < $total_pages)
                            echo "?page=" . ($page + 1) . "&search=$search"; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>