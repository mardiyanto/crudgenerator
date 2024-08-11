<?php
include 'koneksi.php';

// Ambil daftar tabel dari database
$sql = "SHOW TABLES";
$result = $koneksi->query($sql);

$tables = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <body>
  <div class="container-fluid">
    <div class="row"> 
        <div class="card">

          <div class="box-header">
            <h3 class="box-title">CRUD Generator</h3>
          </div>
          <div class="box-body">
            <form action="generate.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                  <label>Pilih Tabel</label>
                  <select class="form-control"  id="table" name="table" required>
            <?php foreach ($tables as $table): ?>
                <option value="<?php echo htmlspecialchars($table); ?>"><?php echo htmlspecialchars($table); ?></option>
            <?php endforeach; ?>
        </select>
                </div>

                <div class="form-group">
                  <label>Folder Output</label>
                  <input type="text" class="form-control" name='folder'  required="required">
                </div>
                <div class="form-group">
                  <input type="submit" class="btn btn-sm btn-primary" value="Simpan">
                </div>         
            </form>
          </div>

        </div>
    </div>
</div>
  </body>
</html>