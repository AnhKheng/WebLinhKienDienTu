<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thêm danh mục sản phẩm</title>


</head>
<body>
    <h2>Thêm danh mục sản phẩm</h2>
    <table>
        <form action="../../API/admin/Category/Add.php" method="post" enctype="multipart/form-data">
            <tr>
                <td>Mã danh mục:</td>
                <td><input type="text" name="idDM" required></td>
            </tr>
            <tr>
                <td>Tên danh mục:</td>
                <td><input type="text" name="nameDM" required></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" value="Thêm danh mục"></td>
            </tr>
        </form>
    </table>
    
</body>

</html>