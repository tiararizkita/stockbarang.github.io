<?php
session_start();

//Membuat koneksi ke database
$conn = mysqli_connect("localhost","root","","stockbarang");


//Menambah barang baru
if(isset($_POST['addnewbarang'])){
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    $addtotable = mysqli_query($conn,"insert into stock (namabarang, deskripsi, stock) values('$namabarang','$deskripsi','$stock')");
    if($addtotable){
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    }
}


//Menambah barang masuk
if(isset($_POST['barangmasuk'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang+$qty;

    $addtomasuk = mysqli_query($conn,"insert into masuk (idbarang, keterangan, qty) values('$barangnya','$penerima','$qty')");
    $updatestockmasuk = mysqli_query($conn, "update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
    if($addtomasuk && $updatestockmasuk){
        header('location:masuk.php');
    } else {
        echo 'Gagal: ' . mysqli_error($conn);
        // Atau Anda juga bisa mengarahkan pengguna kembali ke halaman masuk.php dengan menambahkan pesan kesalahan di URL
        // header('location:masuk.php?error=' . urlencode(mysqli_error($conn)));
    }
    
}



//Menambah barang keluar
if(isset($_POST['addbarangkeluar'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang-$qty;

    $addtokeluar = mysqli_query($conn,"insert into keluar (idbarang, penerima, qty) values('$barangnya','$penerima', '$qty')");
    $updatestockmasuk = mysqli_query($conn, "update stock set stock=' $tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
    if($addtomasuk && $updatestockmasuk){
        header('location:masuk.php');
    } else {
        echo 'Gagal: ' . mysqli_error($conn);
        // Atau Anda juga bisa mengarahkan pengguna kembali ke halaman masuk.php dengan menambahkan pesan kesalahan di URL
        // header('location:masuk.php?error=' . urlencode(mysqli_error($conn)));
    }
    
}



// Update info barang
if(isset($_POST['updatebarang'])){
    $idbarang = $_POST['idbarang']; // Ubah menjadi $_POST['idbarang']
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    $update = mysqli_query($conn, "UPDATE stock SET namabarang='$namabarang', deskripsi='$deskripsi' WHERE idbarang ='$idbarang'");
    if($update){
        header('location:index.php');
        exit(); // Exit setelah melakukan redirect
    } else {
        echo 'Gagal';
        header('location:index.php');
        exit(); // Exit setelah melakukan redirect
    }
}


// Menghapus barang dari stock
if(isset($_POST['hapusbarang'])){
    $idbarang = $_POST['idbarang']; // Ubah menjadi $_POST['idbarang']

    $hapus = mysqli_query($conn, "DELETE FROM stock WHERE idbarang='$idbarang'");
    if($hapus){
        header('location:index.php');
        exit(); // Exit setelah melakukan redirect
    } else {
        echo 'Gagal';
        header('location:index.php');
        exit(); // Exit setelah melakukan redirect
    }
};



// Mengubah data barang masuk
if(isset($_POST['updatebarangmasuk'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "SELECT * FROM masuk WHERE idmasuk='$idm'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty > $qtyskrg){
        $selisih = $qty - $qtyskrg;
        $kurangin = $stockskrg - $selisih; // Mengurangi stok yang tersedia
        $updatenya = mysqli_query($conn, "UPDATE masuk SET qty='$qty', keterangan='$deskripsi' WHERE idmasuk='$idm'");

        if($updatenya){
            // Update stok barang
            $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock='$kurangin' WHERE idbarang='$idb'");
            if($kurangistocknya){
                header('location:masuk.php');
                exit();
            } else {
                echo 'Gagal mengupdate stok';
                header('location:masuk.php');
                exit();
            }
        } else {
            echo 'Gagal mengupdate barang masuk';
            header('location:masuk.php');
            exit();
        }
    } else {
        $selisih = $qtyskrg - $qty;
        $kurangin = $stockskrg + $selisih; // Menambahkan stok yang tersedia
        $updatenya = mysqli_query($conn, "UPDATE masuk SET qty='$qty', keterangan='$deskripsi' WHERE idmasuk='$idm'");

        if($updatenya){
            // Update stok barang
            $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock='$kurangin' WHERE idbarang='$idb'");
            if($kurangistocknya){
                header('location:masuk.php');
                exit();
            } else {
                echo 'Gagal mengupdate stok';
                header('location:masuk.php');
                exit();
            }
        } else {
            echo 'Gagal mengupdate barang masuk';
            header('location:masuk.php');
            exit();
        }
    }
}



// Menghapus barang masuk
if(isset($_POST['hapusbarangmasuk'])){
    $idb = $_POST['idbarang'];
    $qty = $_POST['kty'];
    $idm = $_POST['idmasuk']; // Mengubah variabel menjadi idmasuk

    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock - $qty; // Menghitung selisih stok dengan pengurangan

    $update = mysqli_query($conn, "UPDATE stock SET stock='$selisih' WHERE idbarang='$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk='$idm'"); // Menggunakan idmasuk untuk penghapusan data

    if($update && $hapusdata){
        header('location:masuk.php');
        exit(); // Exit setelah melakukan redirect
    } else {
        header('location:masuk.php');
        exit(); // Exit setelah melakukan redirect
    }
}



//Mengubah data barang keluar
if(isset($_POST['updatebarangkeluar'])){
    $idb = $_POST['idb'];
    $idk = $_POST['idk']; // Mengubah variabel menjadi idkeluar
    $penerima = $_POST['penerima']; // Mengubah variabel menjadi penerima
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "SELECT * FROM keluar WHERE idkeluar='$idk'"); // Mengubah variabel menjadi idkeluar
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty > $qtyskrg){
        $selisih = $qty - $qtyskrg;
        $kurangin = $stockskrg - $selisih; // Mengurangi stok yang tersedia
        $updatenya = mysqli_query($conn, "UPDATE keluar SET qty='$qty', penerima='$penerima' WHERE idkeluar='$idk'"); // Mengubah variabel menjadi idkeluar

        if($updatenya){
            // Update stok barang
            $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock='$kurangin' WHERE idbarang='$idb'");
            if($kurangistocknya){
                header('location:keluar.php');
                exit();
            } else {
                echo 'Gagal mengupdate stok';
                header('location:keluar.php');
                exit();
            }
        } else {
            echo 'Gagal mengupdate barang keluar';
            header('location:keluar.php');
            exit();
        }
    } else {
        $selisih = $qtyskrg - $qty;
        $kurangin = $stockskrg + $selisih; // Menambahkan stok yang tersedia
        $updatenya = mysqli_query($conn, "UPDATE keluar SET qty='$qty', penerima='$penerima' WHERE idkeluar='$idk'"); // Mengubah variabel menjadi idkeluar

        if($updatenya){
            // Update stok barang
            $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock='$kurangin' WHERE idbarang='$idb'");
            if($kurangistocknya){
                header('location:keluar.php');
                exit();
            } else {
                echo 'Gagal mengupdate stok';
                header('location:keluar.php');
                exit();
            }
        } else {
            echo 'Gagal mengupdate barang keluar';
            header('location:keluar.php');
            exit();
        }
    }
}


// Menghapus barang keluar
if(isset($_POST['hapusbarangkeluar'])){
    $idb = $_POST['idbarang'];
    $qty = $_POST['kty'];
    $idk = $_POST['idkeluar']; // Mengubah variabel menjadi idkeluar

    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock - $qty; // Menghitung selisih stok dengan pengurangan

    $update = mysqli_query($conn, "UPDATE stock SET stock='$selisih' WHERE idbarang='$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar='$idk'"); // Menggunakan idkeluar untuk penghapusan data

    if($update && $hapusdata){
        header('location:keluar.php');
        exit(); // Exit setelah melakukan redirect
    } else {
        header('location:keluar.php');
        exit(); // Exit setelah melakukan redirect
    }
}


?>