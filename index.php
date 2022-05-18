<?php
session_start();
if (isset($_GET['sesi'])) {
	$nik = $_POST['nik'];
	$nama = $_POST['nama'];
	$fn = 'konfig/config.txt';
	$fo = fopen($fn, 'a+');
	if (isset($_POST['daftar'])) {
		$txt = implode('|', array(
			$nik, $nama
		));
		$txt .= "\n";
		fwrite($fo, $txt);
		fclose($fo);
		header('location: index.php');
	} else if(isset($_POST['masuk'])) {
		$fr = fread($fo, filesize($fn)-1);
		fclose($fo);
		$users = explode("\n", $fr);
		foreach($users as $usr) {
			$usr = explode('|', $usr);
			if ($nik == $usr[0] && $nama == $usr[1]) {
				$_SESSION['nik'] = $nik;
				$_SESSION['nama'] = $nama;
				header('location: index.php');
				break;
			}
		}
	}
} else if (isset($_GET['isi']) && isset($_SESSION['nik'])) {
	$tgl = $_POST['tgl'];
	$jam = $_POST['jam'];
	$lokasi = $_POST['lokasi'];
	$suhu = $_POST['suhu'];
	$txt = implode('|', array(
		$tgl, $jam, $lokasi, $suhu
	));
	$txt .= "\n";
	$fn = "konfig/user/{$_SESSION['nik']}.txt";
	$fo = fopen($fn, 'a');
	fwrite($fo, $txt);
	fclose($fo);
	header('location: index.php');
} else if (isset($_GET['logout'])) {
	session_unset();
	session_destroy();
	header('location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Peduli tah?</title>

	<link rel="stylesheet" href="aset/css/global.css">
</head>
<body>
<?php if (!isset($_SESSION['nik'])) { ?>
	<div class="form-page">
		<form action="?sesi" method="post">
			<div class="row">
				<input type="text" name="nik" placeholder="NIK">
			</div>
			<div class="row">
				<input type="text" name="nama" placeholder="Nama Lengkap">
			</div>
			<div class="row">
				<button name="daftar" type="submit">Saya Pengguna Baru</button>
				<button name="masuk" type="submit">Masuk</button>
			</div>
		</form>
	</div>
<?php
} else {
	$fn = "konfig/user/{$_SESSION['nik']}.txt";
?>
	<div class="profile">
		<div class="kepala">
			<img src="aset/gambar/ikon.png" alt="ikon" width="200">
			<h1>PEDULI TAH?</h1>
			<p>Catatan Perjalanan</p>
			<a href="#home">Home</a> | <a href="#catatan">Catatan Perjalanan</a> | <a href="#ngisi">Isi Data</a> | <a href="?logout">keluar</a>
		</div>
		<div id="home">
			<div class="box">
				Selamat Datang <?=$_SESSION['nama']?> di aplikasi yang sangat mempedulikan Anda
			</div>
			<button>Isi Catatan Perjalanan</button>
		</div>
		<div id="catatan">
			<div class="box">
				<label for="sort-as">Urutkan Berdasarkan</label>
				<select id="sort-as">
					<option></option>
					<option value="Tanggal">Tanggal</option>
					<option value="Waktu">Waktu</option>
					<option value="Lokasi">Lokasi</option>
					<option value="Suhu Tubuh">Suhu Tubuh</option>
				</select>
				<button>Urutkan</button>
			</div>
			<div class="box">
<?php
	if (file_exists($fn)) {
		$fo = fopen($fn, 'r');
		$fr = fread($fo, filesize($fn)-1);
		fclose($fo);
		$data = explode("\n", $fr);
?>
				<table>
					<thead>
						<tr>
							<th>Tanggal</th>
							<th>Waktu</th>
							<th>Lokasi</th>
							<th>Suhu Tubuh</th>
						</tr>
					</thead>
					<tbody>
<?php
		foreach($data as $row) {
			$row = explode('|', $row);
?>
						<tr>
							<td data-date="<?=$row[0]?>"><?=date_format(date_create($row[0]), 'd-m-Y')?></td>
							<td><?=$row[1]?></td>
							<td><?=$row[2]?></td>
							<td><?=$row[3]?></td>
						</tr>
<?php } ?>
					</tbody>
				</table>
<?php } else { ?>
				<p>Data Perjalanan Masih Kosong</p>
<?php } ?>
				<button>Isi Catatan Perjalanan</button>
			</div>
		</div>
		<div id="ngisi">
			<div class="form-page">
				<form action="?isi" method="POST">
					<div class="row">
						<label for="tgl">Tanggal</label>
						<input type="date" name="tgl" id="tgl">
					</div>
					<div class="row">
						<label for="jam">Jam</label>
						<input type="time" name="jam" id="jam">
					</div>
					<div class="row">
						<label for="lokasi">Lokasi Yang Dikunjungi</label>
						<input type="text" name="lokasi" id="lokasi">
					</div>
					<div class="row">
						<label for="suhu">Suhu Tubuh</label>
						<input type="number" name="suhu" id="suhu">
					</div>
					<div class="row">
						<div class="col"></div>
						<div class="col">
							<button type="submit">Simpan</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script src="aset/js/sortable.js"></script>
<?php } ?>
</body>
</html>