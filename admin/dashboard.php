<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}
include('../config/koneksi.php');

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
include('view/header.php');

// Statistik total
$query_total_pegawai = "SELECT COUNT(*) as total FROM pegawai";
$result_total_pegawai = mysqli_query($koneksi, $query_total_pegawai);
$total_pegawai = mysqli_fetch_assoc($result_total_pegawai)['total'];

$query_total_departemen = "SELECT COUNT(*) as total FROM departemen";
$result_total_departemen = mysqli_query($koneksi, $query_total_departemen);
$total_departemen = mysqli_fetch_assoc($result_total_departemen)['total'];

$query_total_jabatan = "SELECT COUNT(*) as total FROM jabatan";
$result_total_jabatan = mysqli_query($koneksi, $query_total_jabatan);
$total_jabatan = mysqli_fetch_assoc($result_total_jabatan)['total'];

$query_total_gaji = "SELECT SUM(gaji) as total FROM pegawai";
$result_total_gaji = mysqli_query($koneksi, $query_total_gaji);
$total_gaji = mysqli_fetch_assoc($result_total_gaji)['total'];

// Grafik pegawai per departemen
$query_pegawai_departemen = "
    SELECT d.nama_departemen, COUNT(p.id_pegawai) as jumlah_pegawai 
    FROM departemen d
    LEFT JOIN pegawai p ON d.id_departemen = p.id_departemen
    GROUP BY d.id_departemen, d.nama_departemen
";
$result_pegawai_departemen = mysqli_query($koneksi, $query_pegawai_departemen);

$departemen_labels = [];
$departemen_data = [];

while ($row = mysqli_fetch_assoc($result_pegawai_departemen)) {
    $departemen_labels[] = $row['nama_departemen'];
    $departemen_data[] = $row['jumlah_pegawai'];
}
?>

<main class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="mt-4">Dashboard</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Statistik</li>
                </ol>
            </div>
        </div>
        <!-- Statistik Utama -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-white p-3 rounded" style="background-color: #27ae60;">
                    <i class="bi bi-people fs-2"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase mb-1">Total Pegawai</h6>
                    <h4 class="mb-0"><?= number_format($total_pegawai) ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-white p-3 rounded" style="background-color: #2c3e50;">
                    <i class="bi bi-building fs-2"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase mb-1">Total Departemen</h6>
                    <h4 class="mb-0"><?= number_format($total_departemen) ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-white p-3 rounded" style="background-color: #34495e;">
                    <i class="bi bi-briefcase fs-2"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase mb-1">Total Jabatan</h6>
                    <h4 class="mb-0"><?= number_format($total_jabatan) ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 text-white p-3 rounded" style="background-color: #27ae60;">
                    <i class="bi bi-cash-coin fs-2"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase mb-1">Total Gaji</h6>
                    <h4 class="mb-0">Rp. <?= number_format($total_gaji, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Grafik Pegawai per Departemen -->
<div class="row">
    <div class="col-xl-9">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h5 class="card-title text-muted">Pegawai per Departemen</h5>
            </div>
            <div class="card-body">
                <canvas id="departemenChart" width="100%" height="40"></canvas>
            </div>
        </div>
    </div>
</div>
</main>

<?php include('view/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Grafik Pegawai per Departemen
    var ctx = document.getElementById('departemenChart').getContext('2d');
    var departemenChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($departemen_labels) ?>,
            datasets: [{
                data: <?= json_encode($departemen_data) ?>,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(153, 102, 255, 0.6)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false 
                }
            },
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>