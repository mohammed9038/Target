<?php
// Simple periods management page
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $year = intval($_POST["year"] ?? 0);
    $month = intval($_POST["month"] ?? 0);
    $action = $_POST["action"] ?? "";
    
    if ($year && $month && in_array($action, ["open", "close"])) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=u925629539_TSM", "u925629539_hesoka1", "HEsoka202090$");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $monthNames = [
                1 => "January", 2 => "February", 3 => "March", 4 => "April",
                5 => "May", 6 => "June", 7 => "July", 8 => "August",
                9 => "September", 10 => "October", 11 => "November", 12 => "December"
            ];
            
            $monthName = $monthNames[$month] . " " . $year;
            $isOpen = ($action === "open") ? 1 : 0;
            
            $stmt = $pdo->prepare("INSERT INTO periods (year, month, name, is_open) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE is_open = ?, name = ?");
            $stmt->execute([$year, $month, $monthName, $isOpen, $isOpen, $monthName]);
            
            $message = "Period " . $monthName . " has been " . ($isOpen ? "opened" : "closed");
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Get current periods
try {
    $pdo = new PDO("mysql:host=localhost;dbname=u925629539_TSM", "u925629539_hesoka1", "HEsoka202090$");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT year, month, name, is_open FROM periods ORDER BY year DESC, month ASC");
    $periods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $periods = [];
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Periods Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar3 me-2"></i>Periods Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($message)): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Open a Period</h6>
                                <form method="POST" class="border p-3 rounded">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <select name="year" class="form-select" required>
                                                <option value="">Select Year</option>
                                                <?php for ($y = date("Y"); $y <= date("Y") + 2; $y++): ?>
                                                    <option value="<?= $y ?>"><?= $y ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <select name="month" class="form-select" required>
                                                <option value="">Select Month</option>
                                                <?php 
                                                $months = [1=>"January",2=>"February",3=>"March",4=>"April",5=>"May",6=>"June",7=>"July",8=>"August",9=>"September",10=>"October",11=>"November",12=>"December"];
                                                foreach ($months as $num => $name): 
                                                ?>
                                                    <option value="<?= $num ?>"><?= $name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" name="action" value="open" class="btn btn-success btn-sm mt-2">
                                        <i class="bi bi-unlock me-1"></i>Open Period
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <h6>Close a Period</h6>
                                <form method="POST" class="border p-3 rounded">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <select name="year" class="form-select" required>
                                                <option value="">Select Year</option>
                                                <?php for ($y = date("Y"); $y <= date("Y") + 2; $y++): ?>
                                                    <option value="<?= $y ?>"><?= $y ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <select name="month" class="form-select" required>
                                                <option value="">Select Month</option>
                                                <?php foreach ($months as $num => $name): ?>
                                                    <option value="<?= $num ?>"><?= $name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" name="action" value="close" class="btn btn-danger btn-sm mt-2">
                                        <i class="bi bi-lock me-1"></i>Close Period
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <h6>Current Periods Status</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Year</th>
                                        <th>Month</th>
                                        <th>Period Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($periods)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No periods found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($periods as $period): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($period["year"]) ?></td>
                                                <td><?= htmlspecialchars($period["month"]) ?></td>
                                                <td><?= htmlspecialchars($period["name"]) ?></td>
                                                <td>
                                                    <?php if ($period["is_open"]): ?>
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-unlock me-1"></i>Open
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-lock me-1"></i>Closed
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="targets" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Targets
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>