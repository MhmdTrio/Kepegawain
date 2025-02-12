<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}
include('../config/koneksi.php');

$pageTitle = 'Manajemen Jabatan';
$activePage = 'jabatan';
include('view/header.php');

// Ambil data jabatan dengan informasi departemen
$query = "SELECT jabatan.*, departemen.nama_departemen 
          FROM jabatan 
          JOIN departemen ON jabatan.id_departemen = departemen.id_departemen";
$result = mysqli_query($koneksi, $query);

// Ambil daftar departemen untuk dropdown
$query_departemen = "SELECT * FROM departemen";
$departemen_result = mysqli_query($koneksi, $query_departemen);
?>
<main class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="mt-4">Manajemen Jabatan</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Daftar Jabatan</li>
                </ol>
            </div>
        </div>

        <style>
    .card-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
    }
    .btn-tambah {
        background-color: #27ae60;
        border-color: #27ae60;
        transition: all 0.3s ease;
    }
    .btn-tambah:hover {
        background-color: #2c3e50;
        border-color: #2c3e50;
    }
    .table thead {
        background-color: #f4f6f9;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(39, 174, 96, 0.1);
    }
</style>
        
        <!-- Tombol Tambah Jabatan -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <button type="button" class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#tambahJabatanModal">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Jabatan
                </button>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Jabatan</th>
                            <th>Departemen</th>
                            <th>Gaji</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
                            <td><?= htmlspecialchars($row['nama_departemen']) ?></td>
                            <td>Rp. <?= number_format($row['gaji'], 0, ',', '.') ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="#" class="btn btn-warning btn-sm" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#editJabatanModal<?= $row['id_jabatan'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="../proses/hapus_jabatan.php?id=<?= $row['id_jabatan'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Jabatan -->
                        <div class="modal fade" id="editJabatanModal<?= $row['id_jabatan'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Jabatan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="../proses/edit_jabatan.php" method="post">
                                        <input type="hidden" name="id_jabatan" value="<?= $row['id_jabatan'] ?>">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Jabatan</label>
                                                <input type="text" name="nama_jabatan" class="form-control" 
                                                       value="<?= htmlspecialchars($row['nama_jabatan']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Departemen</label>
                                                <select name="id_departemen" class="form-select" required>
                                                    <?php 
                                                    mysqli_data_seek($departemen_result, 0);
                                                    while($dept = mysqli_fetch_assoc($departemen_result)): ?>
                                                        <option value="<?= $dept['id_departemen'] ?>" 
                                                            <?= $dept['id_departemen'] == $row['id_departemen'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($dept['nama_departemen']) ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Gaji</label>
                                                <input type="text" name="gaji" class="form-control rupiah" 
                                                value="<?= number_format($row['gaji'], 0, ',', '.') ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah Jabatan -->
        <div class="modal fade" id="tambahJabatanModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jabatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="../proses/tambah_jabatan.php" method="post">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Jabatan</label>
                                <input type="text" name="nama_jabatan" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Departemen</label>
                                <select name="id_departemen" class="form-select" required>
                                    <option value="">Pilih Departemen</option>
                                    <?php 
                                    mysqli_data_seek($departemen_result, 0);
                                    while($dept = mysqli_fetch_assoc($departemen_result)): ?>
                                        <option value="<?= $dept['id_departemen'] ?>">
                                            <?= htmlspecialchars($dept['nama_departemen']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gaji</label>
                                <input type="text" name="gaji" class="form-control rupiah" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include('view/footer.php'); ?>