<?php
include 'koneksi.php';

// Ambil nama tabel dan folder output dari form
$table_name = $_POST['table'];
$output_folder = !empty($_POST['folder']) ? $_POST['folder'] : 'output';

// Pastikan folder output ada
if (!is_dir($output_folder)) {
    mkdir($output_folder, 0777, true);
}

// Ambil informasi tabel
$sql = "DESCRIBE $table_name";
$result = $koneksi->query($sql);

if ($result->num_rows > 0) {
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row;
    }

    // Ambil nama kolom kunci utama
    $primary_key = '';
    foreach ($columns as $column) {
        if ($column['Key'] == 'PRI') {
            $primary_key = $column['Field'];
            break;
        }
    }

    // Generate CRUD Script
    $crud_script = "<?php\ninclude '../koneksi.php';\n\n";
    
    // Input
    $crud_script .= "if(\$_GET['aksi']=='input'){\n";
    $fields = array_filter($columns, function($col) use ($primary_key) {
        return $col['Field'] != $primary_key;
    });
    $field_names = implode(",", array_map(function($col) {
        return $col['Field'];
    }, $fields));
    $field_values = implode(",", array_map(function($col) {
        return "'\$_POST[{$col['Field']}]'";
    }, $fields));
    $crud_script .= "    mysqli_query(\$koneksi,\"INSERT INTO $table_name ($field_names) VALUES ($field_values)\");\n";
    $crud_script .= "    echo \"<script type='text/javascript'>alert('Data berhasil diinput');</script>\";\n";
    $crud_script .= "    echo \"<script>window.location=('index.php')</script>\";\n";
    $crud_script .= "}\n\n";

    // Edit
    $edit_values = implode(",", array_map(function($col) {
        return "{$col['Field']}='\$_POST[{$col['Field']}]'";
    }, $fields));
    $crud_script .= "elseif(\$_GET['aksi']=='edit'){\n";
    $crud_script .= "    mysqli_query(\$koneksi,\"UPDATE $table_name SET $edit_values WHERE $primary_key='\$_GET[id]'\");\n";
    $crud_script .= "    echo \"<script type='text/javascript'>alert('Data berhasil diedit');</script>\";\n";
    $crud_script .= "    echo \"<script>window.location=('index.php')</script>\";\n";
    $crud_script .= "}\n\n";

    // Hapus
    $crud_script .= "elseif(\$_GET['aksi']=='hapus'){\n";
    $crud_script .= "    mysqli_query(\$koneksi,\"DELETE FROM $table_name WHERE $primary_key='\$_GET[id]'\");\n";
    $crud_script .= "    echo \"<script type='text/javascript'>alert('Data berhasil dihapus');</script>\";\n";
    $crud_script .= "    echo \"<script>window.location=('index.php')</script>\";\n";
    $crud_script .= "}\n";
    $crud_script .= "?>";

    file_put_contents("$output_folder/proses.php", $crud_script);

 // Generate File CRUD
 $crud_script = "<?php \n";
 $crud_script .= "include '../koneksi.php';\n";
 $crud_script .= "?>\n";
 $crud_script .= "<!doctype html>\n";
 $crud_script .= "<html lang=\"en\">\n";
 $crud_script .= "  <head>\n";
 $crud_script .= "    <meta charset=\"utf-8\">\n";
 $crud_script .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
 $crud_script .= "    <title>Bootstrap demo</title>\n";
 $crud_script .= "    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css\" rel=\"stylesheet\" crossorigin=\"anonymous\">\n";
 $crud_script .= "    <script src=\"https://code.jquery.com/jquery-3.2.1.slim.min.js\" crossorigin=\"anonymous\"></script>\n";
 $crud_script .= "    <script src=\"https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js\" crossorigin=\"anonymous\"></script>\n";
 $crud_script .= "    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js\" crossorigin=\"anonymous\"></script>\n";
 $crud_script .= "  </head>\n";
 $crud_script .= "  <body>\n";
 $crud_script .= "  <div class=\"container-fluid\">\n";
 $crud_script .= "    <div class=\"row\">\n";
 $crud_script .= "        <div class=\"box box-info\">\n";
 $crud_script .= "          <div class=\"box-header\">\n";
 $crud_script .= "            <h3 class=\"box-title\">DATA ".strtoupper($table_name)."</h3>\n";
 $crud_script .= "            <button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#exampleModalinput\">Tambah data</button>\n";
 $crud_script .= "          </div>\n";
 $crud_script .= "            <div class=\"modal fade\" id=\"exampleModalinput\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">\n";
 $crud_script .= "              <div class=\"modal-dialog\" role=\"document\">\n";
 $crud_script .= "                <div class=\"modal-content\">\n";
 $crud_script .= "                  <div class=\"modal-header\">\n";
 $crud_script .= "                    <h5 class=\"modal-title\" id=\"exampleModalLabel\">TAMBAH DATA ".strtoupper($table_name)."</h5>\n";
 $crud_script .= "                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">\n";
 $crud_script .= "                      <span aria-hidden=\"true\">&times;</span>\n";
 $crud_script .= "                    </button>\n";
 $crud_script .= "                  </div>\n";
 $crud_script .= "                  <div class=\"modal-body\">\n";
 $crud_script .= "                        <form action=\"proses.php?aksi=input\" method=\"post\" enctype=\"multipart/form-data\">\n";

 foreach ($columns as $column) {
     if ($column['Field'] != $primary_key) {
         $label = strtoupper($column['Field']);
         $crud_script .= "                          <div class=\"form-group\">\n";
         $crud_script .= "                            <label>$label</label>\n";
         $crud_script .= "                            <input type=\"text\" class=\"form-control\" name=\"{$column['Field']}\" required=\"required\" placeholder=\"Masukkan $label ..\">\n";
         $crud_script .= "                          </div>\n";
     }
 }

 $crud_script .= "                          <div class=\"form-group\">\n";
 $crud_script .= "                            <input type=\"submit\" class=\"btn btn-sm btn-primary\" value=\"Simpan\">\n";
 $crud_script .= "                          </div>\n";
 $crud_script .= "                        </form>\n";
 $crud_script .= "                  </div>\n";
 $crud_script .= "                </div>\n";
 $crud_script .= "              </div>\n";
 $crud_script .= "            </div>\n";
 $crud_script .= "          <div class=\"card\">\n";
 $crud_script .= "            <div class=\"table-responsive\">\n";
 $crud_script .= "              <table class=\"table table-bordered table-striped\" id=\"table-datatable\">\n";
 $crud_script .= "                <thead>\n";
 $crud_script .= "                  <tr>\n";
 $crud_script .= "                    <th width=\"1%\">NO</th>\n";

 foreach ($columns as $column) {
     $crud_script .= "                    <th>".strtoupper($column['Field'])."</th>\n";
 }

 $crud_script .= "                    <th>OPSI</th>\n";
 $crud_script .= "                  </tr>\n";
 $crud_script .= "                </thead>\n";
 $crud_script .= "                <tbody>\n";
 $crud_script .= "                  <?php \n";
 $crud_script .= "                  \$no=1;\n";
 $crud_script .= "                  \$data = mysqli_query(\$koneksi,\"SELECT * FROM $table_name\");\n";
 $crud_script .= "                  while(\$row = mysqli_fetch_array(\$data)){\n";
 $crud_script .= "                    ?>\n";
 $crud_script .= "                    <tr>\n";
 $crud_script .= "                      <td><?php echo \$no++; ?></td>\n";

 foreach ($columns as $column) {
     $crud_script .= "                      <td><?php echo \$row['{$column['Field']}']; ?></td>\n";
 }

 $crud_script .= "                      <td>\n";
 $crud_script .= "                      <button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#edit<?php echo \$row['$primary_key'] ?>\">Edit</button>\n";
 $crud_script .= "                        <a class=\"btn btn-danger btn-sm\" href=\"proses.php?aksi=hapus&id=<?php echo \$row['$primary_key'] ?>\">HAPUS</a>\n";
 $crud_script .= "                      </td>\n";
 $crud_script .= "                    </tr>\n";

 $crud_script .= "                    <div class=\"modal fade\" id=\"edit<?php echo \$row['$primary_key'] ?>\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">\n";
 $crud_script .= "                      <div class=\"modal-dialog\" role=\"document\">\n";
 $crud_script .= "                        <div class=\"modal-content\">\n";
 $crud_script .= "                          <div class=\"modal-header\">\n";
 $crud_script .= "                            <h5 class=\"modal-title\" id=\"exampleModalLabel\">EDIT DATA ".strtoupper($table_name)."</h5>\n";
 $crud_script .= "                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">\n";
 $crud_script .= "                              <span aria-hidden=\"true\">&times;</span>\n";
 $crud_script .= "                            </button>\n";
 $crud_script .= "                          </div>\n";
 $crud_script .= "                          <div class=\"modal-body\">\n";
 $crud_script .= "                          <form action=\"proses.php?aksi=edit&id=<?php echo \$row['$primary_key'] ?>\" method=\"post\" enctype=\"multipart/form-data\">\n";

 foreach ($columns as $column) {
     if ($column['Field'] != $primary_key) {
         $crud_script .= "                              <div class=\"form-group\">\n";
         $crud_script .= "                                <label>".strtoupper($column['Field'])."</label>\n";
         $crud_script .= "                                <input type=\"text\" class=\"form-control\" name=\"{$column['Field']}\" value=\"<?php echo \$row['{$column['Field']}'] ?>\" required=\"required\" placeholder=\"Masukkan ".strtoupper($column['Field'])." ..\">\n";
         $crud_script .= "                              </div>\n";
     }
 }

 $crud_script .= "                              <div class=\"form-group\">\n";
 $crud_script .= "                                <input type=\"submit\" class=\"btn btn-sm btn-primary\" value=\"Simpan\">\n";
 $crud_script .= "                              </div>\n";
 $crud_script .= "                            </form>\n";
 $crud_script .= "                          </div>\n";
 $crud_script .= "                        </div>\n";
 $crud_script .= "                      </div>\n";
 $crud_script .= "                    </div>\n";

 $crud_script .= "                    <?php \n";
 $crud_script .= "                  }\n";
 $crud_script .= "                  ?>\n";
 $crud_script .= "                </tbody>\n";
 $crud_script .= "              </table>\n";
 $crud_script .= "            </div>\n";
 $crud_script .= "          </div>\n";
 $crud_script .= "        </div>\n";
 $crud_script .= "    </div>\n";
 $crud_script .= " </div>\n";
 $crud_script .= "  </body>\n";
 $crud_script .= "</html>";

 file_put_contents("$output_folder/index.php", $crud_script);

    echo "CRUD script generated successfully in the '$output_folder' folder!";
} else {
    echo "Table not found!";
}
?>
