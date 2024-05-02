<?php
require_once "Perpustakaan.php";
session_start();

if (!isset($_SESSION['perpustakaan'])) {
    $_SESSION['perpustakaan'] = new Library();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $isbn = $_POST['isbn'];
        $judul = $_POST['title'];
        $penulis = $_POST['author'];
        $penerbit = $_POST['publisher'];
        $tahun = $_POST['year'];

        $newBook = new ReferenceBook($judul, $penulis, $tahun, $isbn, $penerbit);
        $_SESSION['perpustakaan']->add($newBook);
    }
    if (isset($_POST['delete'])) {
        if (isset($_POST['isbn'])) {

            $isbn = $_POST['isbn'];
            if (isset($_SESSION['perpustakaan'])) {
                $_SESSION['perpustakaan']->delete($isbn);
            }
        }
    }
    if (isset($_POST['borrow'])) {
        $isbn = $_POST['isbn'];
        $peminjam = $_POST['borrower'];
        $tanggal_kembali = $_POST['date'];

        if ($_SESSION['perpustakaan']->checkUserLimit($peminjam)) {
            $book = $_SESSION['perpustakaan']->searchByISBN($isbn);
            if ($book) {
                $book->borrow($peminjam, $tanggal_kembali);
                $_SESSION['perpustakaan']->saveSession();
            }
        }
    }
    if (isset($_POST['return'])) {
        $isbn = $_POST['isbn'];

        $book = $_SESSION['perpustakaan']->searchByISBN($isbn);

        if ($book) {
            $book->return();
            $_SESSION['perpustakaan']->saveSession();
        } else {
            echo "<script>alert('Tidak ada buku yang dikembalikan');</script>";
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <style>
        .book-card {
            margin-bottom: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            /* Warna latar belakang card dengan transparansi */
            padding: 15px;
            /* Padding dalam card */
            border-radius: 10px;
            /* Sudut card dibulatkan */
        }

        body {
            background-image: url('background.jpg');
            background-size: cover;
            /* Untuk memastikan gambar latar belakang menutupi seluruh area */
            background-repeat: no-repeat;
            /* Untuk mencegah gambar latar belakang diulang */
        }

        /* Gaya untuk header */
        header {
            background-color: #007bff;
            /* Warna latar belakang header */
            color: #fff;
            /* Warna teks header */
            padding: 20px 0;
            /* Padding atas dan bawah */
            text-align: center;
            /* Teks berada di tengah */
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <!-- Form Tambah Buku -->
        <div class="card mb-3">
            <div class="card-header">
                Tambah Buku
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="form-group">
                        <label for="title">Judul</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="author">Penulis</label>
                        <input type="text" class="form-control" id="author" name="author" required>
                    </div>
                    <div class="form-group">
                        <label for="year">Tahun Terbit</label>
                        <input type="text" class="form-control" id="year" name="year" required>
                    </div>
                    <div class="form-group">
                        <label for="isbn">Nomor ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" required>
                    </div>
                    <div class="form-group">
                        <label for="publisher">Penerbit</label>
                        <input type="text" class="form-control" id="publisher" name="publisher" required>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">Tambah Buku</button>
                </form>
            </div>
        </div>

        <!-- Daftar Buku -->
        <div class="card mb-3">
            <div class="card-header">
                Daftar Buku
            </div>
            <div class="card-body">
                <!-- Tombol Pencarian (Disembunyikan) -->
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Cari buku" aria-label="Recipient's username"
                        aria-describedby="basic-addon2">
                </div>
                <!-- Pemilihan Sorting -->
                <div class="mb-3">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <label for="sort">Urutkan berdasarkan</label>
                        <div>
                            <select class="form-select" name="sort" id="sort">
                                <option value="year">Tahun Terbit</option>
                                <option value="author">Nama Penulis</option>
                            </select>
                            <button type="submit" name="apply_sort" class="btn btn-primary mt-3">Terapkan</button>
                        </div>
                    </form>
                </div>

                <!-- Card List Buku -->
                <div class="row">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_sort'])) {
                        $sortCriteria = $_POST['sort'];

                        $sortedBooks = $_SESSION['perpustakaan']->sortBook($sortCriteria);

                        foreach ($sortedBooks as $book) {
                            if (!$book->dipinjam()) {
                                echo "<div class='col-md-4 mb-3'>";
                                echo "<div class='card book-card'>";
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>" . $book->getTitle() . "</h5>";
                                echo "<p class='card-text'>Penulis: " . $book->getAuthor() . "</p>";
                                echo "<p class='card-text'>Tahun Terbit: " . $book->getYear() . "</p>";
                                echo "<p class='card-text'>ISBN: " . $book->getISBN() . "</p>";
                                echo "<p class='card-text'>Penerbit: " . $book->getPublisher() . "</p>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";

                            }
                        }
                    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['keyword'])) {
                        $keyword = $_POST['keyword'];
                        $searchResults = $_SESSION['perpustakaan']->searchBook($keyword);
                        if (sizeof($searchResults) > 0) {
                            foreach ($searchResults as $book) {
                                if (!$book->dipinjam()) {
                                    echo "<div class='col-md-4 mb-3'>";
                                    echo "<div class='card book-card'>";
                                    echo "<div class='card-body'>";
                                    echo "<h5 class='card-title'>" . $book->getTitle() . "</h5>";
                                    echo "<p class='card-text'>Penulis: " . $book->getAuthor() . "</p>";
                                    echo "<p class='card-text'>Tahun Terbit: " . $book->getYear() . "</p>";
                                    echo "<p class='card-text'>ISBN: " . $book->getISBN() . "</p>";
                                    echo "<p class='card-text'>Penerbit: " . $book->getPublisher() . "</p>";
                                    echo "</div>";
                                    echo "</div>";
                                    echo "</div>";

                                }
                            }
                        } else {
                            echo "<p>Tidak ada buku dengan judul dan penulis $keyword</p>";
                        }
                    } else {
                        foreach ($_SESSION['perpustakaan']->getAllBooks() as $book) {
                            if (!$book->isBorrowed()) {
                                echo "<div class='col-md-4 mb-3'>";
                                echo "<div class='card book-card'>";
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>" . $book->getTitle() . "</h5>";
                                echo "<p class='card-text'>Penulis: " . $book->getAuthor() . "</p>";
                                echo "<p class='card-text'>Tahun Terbit: " . $book->getYear() . "</p>";
                                echo "<p class='card-text'>ISBN: " . $book->getISBN() . "</p>";
                                echo "<p class='card-text'>Penerbit: " . $book->getPublisher() . "</p>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";

                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="container-custom d-flex">
            <!-- Form Pinjam Buku -->
            <div class="card mb-3" style="flex: 1; margin-right: 20px;">
                <div class="card-header">
                    Pinjam Buku
                </div>
                <div class="card-body">
                    <form action="#" method="POST">
                        <label for="kembaliISBN">Buku</label>
                        <select class="form-select mb-3" aria-label="Default select example" name="isbn"
                            id="kembaliISBN" required>
                            <?php
                            $counter = 0;
                            foreach ($_SESSION['perpustakaan']->getAllBooks() as $book) {
                                if (!$book->isBorrowed()) {
                                    echo "<option value='" . $book->getISBN() . "'>" . $book->getTitle() . "</option>";
                                } else {
                                    $counter++;
                                }
                            }
                            if ($counter === sizeof($_SESSION['perpustakaan']->getAllBooks())) {
                                echo "<option value='kosong'>Tidak ada buku yang dapat dipinjam</option>";
                            } ?>
                        </select>
                        <label for="namaPeminjam">Nama Peminjam</label>
                        <div class="mb-3">
                            <input type="text" id="namaPeminjam" name="borrower" class="form-control"
                                placeholder="Nama Peminjam">
                        </div>
                        <label for="dateField">Tanggal Dikembalikan</label>
                        <div class="input-group date mb-3" id="datepicker">
                            <input type="date" class="form-control" name="date" id="dateField" required>
                        </div>
                        <button type="submit" name="borrow" class="btn btn-success">Pinjam Buku</button>
                    </form>
                </div>
            </div>

            <!-- Card Hapus Buku -->
            <div class="card mb-3" style="flex: 1;">
                <div class="card-header">
                    Hapus Buku
                </div>
                <div class="card-body">
                    <form action="#" method="POST">
                        <label for="kembaliISBN">Buku</label>
                        <select class="form-select mb-3" aria-label="Default select example" name="isbn"
                            id="kembaliISBN" required>
                            <?php
                            foreach ($_SESSION['perpustakaan']->getAllBooks() as $book) {
                                echo "<option value='" . $book->getISBN() . "'>" . $book->getTitle() . "</option>";
                            }
                            if (sizeof($_SESSION['perpustakaan']->getAllBooks()) <= 0) {
                                echo "<option value='kosong'>Tidak ada buku yang sedang dipinjam</option>";
                            } ?>
                        </select>
                        <button type="submit" name="delete" class="btn btn-success">Hapus Buku</button>
                    </form>
                </div>
            </div>
        </div>
                              <!-- Card Mengembalikan Buku -->
                              <div class="card mb-3">
                                  <div class="card-header">
                                      Kembalikan Buku
                                  </div>
                                  <div class="card-body">
                                      <form action="#" method="POST">
                                          <label for="kembaliISBN">Buku</label>
                                          <select class="form-select mb-3" aria-label="Default select example" name="isbn" id="kembaliISBN"
                                              required>
                                              <?php
                                              $counter = 0;
                                              foreach ($_SESSION['perpustakaan']->getAllBooks() as $book) {
                                                  if ($book->isBorrowed()) {
                                                      echo "<option value='" . $book->getISBN() . "'>" . $book->getTitle() . "</option>";
                                                  } else {
                                                      $counter++;
                                                  }
                                              }
                                              if ($counter === sizeof($_SESSION['perpustakaan']->getAllBooks())) {
                                                  echo "<option value='kosong'>Tidak ada buku yang sedang dipinjam</option>";
                                              } ?>
                                          </select>
                                          <button type="submit" name="return" class="btn btn-warning">Kembalikan Buku</button>
                                      </form>
                                  </div>
                              </div>
    </div>
</body>

</html>