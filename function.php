<?php

session_start();

//Bikin koneksi
$c = mysqli_connect('localhost','root','','kasir');

//Login
if(isset($_POST['login'])){
    //Initiate variable
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check = mysqli_query($c, "SELECT * FROM user WHERE username='$username' and password='$password'");
    $hitung = mysqli_num_rows($check);

    if($hitung>0){
        //Jika datanya ditemukan
        //berhasil login

        $_SESSION['login'] = 'True';
        header('location:index.php');
    }else{
        //Data tidak ditemukan
        //gagal 
        echo'
        <script>alert("Username atau Password salah");
        window.location.href="login.php"
        </script>
        ';
    }
}



if(isset($_POST['tambahbarang'])){
    $namaproduk = $_POST['namaproduk'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];
    $harga = $_POST['harga'];

    $insert = mysqli_query($c,"insert into produk (namaproduk,deskripsi,harga,stock) values('$namaproduk','$deskripsi','$harga','$stock')");

    if($insert){
        header('location:stock.php');
    }else{
        echo'
        <script>alert("Gagal menambah barang baru");
        window.location.href="stock.php"
        </script>
        ';
    }
    
}



if(isset($_POST['tambahpelanggan'])){
    $namapelanggan = $_POST['namapelanggan'];
    $notelp = $_POST['notelp'];
    $alamat = $_POST['alamat'];

    $insert = mysqli_query($c,"insert into pelanggan (namapelanggan,notelp,alamat) values('$namapelanggan','$notelp','$alamat')");

    if($insert){
        header('location:pelanggan.php');
    }else{
        echo'
        <script>alert("Gagal menambah pelanggan baru");
        window.location.href="pelanggan.php"
        </script>
        ';
    }
    
}


if(isset($_POST['tambahpesanan'])){
    $idpelanggan = $_POST['idpelanggan'];

    $insert = mysqli_query($c,"insert into pesanan (idpelanggan) values('$idpelanggan')");

    if($insert){
        header('location:index.php');
    }else{
        echo'
        <script>alert("Gagal menambah pesanan baru");
        window.location.href="index.php"
        </script>
        ';
    }
}

//produk dipilih di pesanan
if(isset($_POST['addproduk'])){
    $idproduk = $_POST['idproduk'];
    $idp = $_POST['idp']; //idpesanan
    $qty = $_POST['qty']; //jumlah yang mau dikeluarkan

    //hitung stock sekarang ada berapa
    $hitung1 = mysqli_query($c,"SELECT * FROM produk WHERE idproduk='$idproduk'");
    $hitung2 = mysqli_fetch_array($hitung1);
    $stocksekarang = $hitung2['stock']; //stock barang saat ini

    if($stocksekarang>=$qty){

        //kurangi stocknya dengan jumlah yang akan di keluarkan
        $selisih = $stocksekarang-$qty;
        //stocknya cukup
        $insert = mysqli_query($c,"insert into detailpesanan (idpesanan,idproduk,qty) values('$idp','$idproduk','$qty')");
        $update = mysqli_query($c,"UPDATE produk SET stock='$selisih' WHERE idproduk='$idproduk'");
        
        if($insert&&$update){
            header('location:view.php?idp='.$idp);
        }else{
             echo'
             <script>alert("Gagal menambah pesanan baru");
             window.location.href="view.php?idp='.$idp.'"
             </script>
             ';
            }
        }else{
        //stock tidak cukup
         echo'
        <script>alert("Stock barang tidak cukup");
        window.location.href="view.php?idp='.$idp.'"
        </script>
        ';
    }
}


//Menambah barang masuk
if(isset($_POST['barangmasuk'])){
    $idproduk = $_POST['idproduk'];
    $qty = $_POST['qty'];

     //cari tahu stock sekarang berapa
     $caristock = mysqli_query($c,"SELECT * FROM produk WHERE idproduk='$idproduk'");
     $caristock2 = mysqli_fetch_array($caristock);
     $stocksekarang = $caristock2['stock'];

    //hitung
    $newstock = $stocksekarang+$qty;

    $insertb = mysqli_query($c,"insert into masuk (idproduk,qty) values('$idproduk','$qty')");
    $updatetb = mysqli_query($c,"UPDATE produk SET stock='$newstock' WHERE idproduk='$idproduk'");
    
    if($insertb&&$updatetb){
        header('location:masuk.php');
    }else{
        echo'
        <script>alert("Gagal");
        window.location.masuk.php"
        </script>
        '; 
    }
}


//hapus produk pesanan
if(isset($_POST['hapusprodukpesanan'])){
    $idp = $_POST['idp']; //iddetailpesanan
    $idpr = $_POST['idpr'];
    $idorder = $_POST['idorder'];

    //Cek qty sekarang
    $cek1 = mysqli_query($c,"SELECT * FROM detailpesanan WHERE iddetailpesanan='$idp'");
    $cek2 = mysqli_fetch_array($cek1);
    $qtysekarang = $cek2['qty'];

    //Cek stock sekarang
    $cek3 = mysqli_query($c,"SELECT * FROM produk WHERE idproduk='$idpr'");
    $cek4 = mysqli_fetch_array($cek3);
    $stocksekarang = $cek4['stock'];

    $hitung = $stocksekarang+$qtysekarang;

    $update = mysqli_query($c,"UPDATE produk SET stock='$hitung' WHERE idproduk='$idpr'"); //update stock
    $hapus = mysqli_query($c,"DELETE FROM detailpesanan WHERE idproduk='$idpr' and iddetailpesanan='$idp'");

    if($update&&$hapus){
        header('location:view.php?idp='.$idorder);
    }else{
        echo'
        <script>alert("Stock menghapus barang");
        window.location.href="view.php?idp='.$idorder.'"
        </script>
        ';
    }
}


//edit barang
if(isset($_POST['editbarang'])){
    $np = $_POST['namaproduk'];
    $desc = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $idp = $_POST['idp']; //idproduk

    $query = mysqli_query($c,"UPDATE produk SET namaproduk='$np', deskripsi='$desc', harga='$harga' WHERE idproduk='$idp' ");

    if($query){
        header('location:stock.php');
    }else{
        echo'
        <script>alert("Gagal");
        window.location.stock.php"
        </script>
        '; 
    }
}


//hapus barang
if(isset($_POST['hapusbarang'])){
    $idp = $_POST['idp'];

    $query = mysqli_query($c,"DELETE FROM produk WHERE idproduk='$idp'");
    if($query){
        header('location:stock.php');
    }else{
        echo'
        <script>alert("Gagal");
        window.location.stock.php"
        </script>
        '; 
    }
}


//edit pelanggan
if(isset($_POST['editpelanggan'])){
    $np = $_POST['namapelanggan'];
    $nt = $_POST['notelp'];
    $a = $_POST['alamat'];
    $id = $_POST['idpl'];

    $query = mysqli_query($c,"UPDATE pelanggan SET namapelanggan='$np', notelp='$nt', alamat='$a' WHERE idpelanggan='$id' ");

    if($query){
        header('location:pelanggan.php');
    }else{
        echo'
        <script>alert("Gagal");
        window.location.pelanggan.php"
        </script>
        '; 
    }
}


//hapus pelanggan
if(isset($_POST['hapuspelanggan'])){
    $idpl = $_POST['idpl'];

    $query = mysqli_query($c,"DELETE FROM pelanggan WHERE idpelanggan='$idpl'");
    if($query){
        header('location:pelanggan.php');
    }else{
        echo'
        <script>alert("Gagal");
        window.location.pelanggan.php"
        </script>
        '; 
    }
}


//mengubah data barang masuk
if(isset($_POST['editdatabarangmasuk'])){
    $qty = $_POST['qty'];
    $idm = $_POST['idm']; //id masuk
    $idp = $_POST['idp']; //id produk

    
    //cari tau qty nya sekarang berapa
    $caritahu = mysqli_query($c,"SELECT * FROM masuk WHERE idmasuk='$idm'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];

    //cari tahu stock sekarang berapa
    $caristock = mysqli_query($c,"SELECT * FROM produk WHERE idproduk='$idp'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang = $caristock2['stock'];

    if($qty >= $qtysekarang){
        //kalau inputan user lebih besar daripada qty yg tercatat 
        //hitung selisih
        $selisih = $qty-$qtysekarang;
        $newstock = $stocksekarang+$selisih;

        $query1 = mysqli_query($c,"UPDATE masuk SET qty='$qty' WHERE idmasuk='$idm' ");
        $query2 = mysqli_query($c,"UPDATE produk SET stock='$newstock' WHERE idproduk='$idp' ");
    
        if($query1&&$query2){
            header('location:masuk.php');
        }else{
            echo'
            <script>alert("Gagal");
            window.location.masuk.php"
            </script>
            '; 
        }
        } else {
        //kalau lebih kecil 
        //hitung selisih
        $selisih = $qtysekarang-$qty;
        $newstock = $stocksekarang-$selisih;

        $query1 = mysqli_query($c,"UPDATE masuk SET qty='$qty' WHERE idmasuk='$idm' ");
        $query2 = mysqli_query($c,"UPDATE produk SET stock='$newstock' WHERE idproduk='$idp' ");
    
        if($query1&&$query2){
            header('location:masuk.php');
        }else{
            echo'
            <script>alert("Gagal");
            window.location.masuk.php"
            </script>
            '; 
        }
    }   
}



//hapus data barang masuk
if(isset($_POST['hapusdatabarangmasuk'])){
    $idm = $_POST['idm'];
    $idp = $_POST['idp'];

    //cari tau qty nya sekarang berapa
    $caritahu = mysqli_query($c,"SELECT * FROM masuk WHERE idmasuk='$idm'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];

    //cari tahu stock sekarang berapa
    $caristock = mysqli_query($c,"SELECT * FROM produk WHERE idproduk='$idp'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang = $caristock2['stock'];

    //hitung selisih
    $newstock = $stocksekarang-$qtysekarang;
     
     $query1 = mysqli_query($c,"DELETE FROM masuk WHERE idmasuk='$idm' ");
     $query2 = mysqli_query($c,"UPDATE produk SET stock='$newstock' WHERE idproduk='$idp' ");
     
     if($query1&&$query2){
        header('location:masuk.php');
    }else{
        echo'
         <script>alert("Gagal");
         window.location.masuk.php"
         </script>
         '; 
    }
}


//hapus order
if(isset($_POST['hapusorder'])){
    $ido = $_POST['ido']; //id order

    $cekdata = mysqli_query($c,"SELECT * FROM detailpesanan dp WHERE idpesanan='$ido'");

    while($ok=mysqli_fetch_array($cekdata)){
        //balikin stock
        $qty = $ok['qty'];
        $idproduk = $ok['idproduk'];
        $iddp = $ok['iddetailpesanan'];

        //cari tahu stock sekarang berapa
        $caristock = mysqli_query($c,"SELECT * FROM produk WHERE idproduk='$idproduk'");
        $caristock2 = mysqli_fetch_array($caristock);
        $stocksekarang = $caristock2['stock'];

        $newstock = $stocksekarang+$qty;

        $queryupdate = mysqli_query($c,"UPDATE produk SET stock='$newstock' WHERE idproduk='$idproduk' ");

        //hapus data
        $querydelete = mysqli_query($c,"DELETE FROM detailpesanan WHERE iddetailpesanan='$iddp'");

    }

    $query = mysqli_query($c,"DELETE FROM pesanan WHERE idorder='$ido'");
    if($queryupdate && $querydelete && $query){
        header('location:index.php');
    }else{
        echo'
        <script>alert("Gagal");
        window.location.index.php"
        </script>
        '; 
    }
}


//mengubah data detail pesanan
if(isset($_POST['editdetailpesanan'])){
    $qty = $_POST['qty'];
    $iddp = $_POST['iddp']; //id masuk
    $idpr = $_POST['idpr']; //id produk
    $idp = $_POST['idp']; //id pesanan

    
    //cari tau qty nya sekarang berapa
    $caritahu = mysqli_query($c,"SELECT * FROM detailpesanan WHERE iddetailpesanan='$iddp'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];

    //cari tahu stock sekarang berapa
    $caristock = mysqli_query($c,"SELECT * FROM produk WHERE idproduk='$idpr'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang = $caristock2['stock'];

    if($qty >= $qtysekarang){
        //kalau inputan user lebih besar daripada qty yg tercatat 
        //hitung selisih
        $selisih = $qty-$qtysekarang;
        $newstock = $stocksekarang-$selisih;

        $query1 = mysqli_query($c,"UPDATE detailpesanan SET qty='$qty' WHERE iddetailpesanan='$iddp' ");
        $query2 = mysqli_query($c,"UPDATE produk SET stock='$newstock' WHERE idproduk='$idpr' ");
    
        if($query1&&$query2){
            header('location:view.php?idp='.$idp);
        }else{
            echo'
            <script>alert("Gagal");
            window.location.href="view.php?idp='.$idp.'"
            </script>
            '; 
        }
        } else {
        //kalau lebih kecil 
        //hitung selisih
        $selisih = $qtysekarang-$qty;
        $newstock = $stocksekarang+$selisih;

        $query1 = mysqli_query($c,"UPDATE detailpesanan SET qty='$qty' WHERE iddetailpesanan='$iddp' ");
        $query2 = mysqli_query($c,"UPDATE produk SET stock='$newstock' WHERE idproduk='$idpr' ");
    
        if($query1&&$query2){
            header('location:view.php?idp='.$idp);
        }else{
            echo'
            <script>alert("Gagal");
            window.location.href="view.php?idp='.$idp.'"
            </script>
            '; 
        }
    }   
}


?>