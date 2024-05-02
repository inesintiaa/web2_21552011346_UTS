<?php

class Buku
{
    private $title;
    private $author;
    private $year;
    private $isBorrowed;
    private $borrower;
    private $date;
    private $fine;

    public function __construct($title, $author, $year)
    {
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->isBorrowed = false;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getBorrower()
    {
        return $this->borrower;
    }
    public function getDate()
    {
        return $this->date;
    }
    public function isBorrowed()
    {
        return $this->isBorrowed;
    }
    public function borrow($borrower, $date)
    {
        if (!$this->isBorrowed) {
            $this->isBorrowed = true;
            $this->borrower = $borrower;
            $this->date = $date;
        }
    }
    public function return()
    {
        if ($this->isBorrowed) {
            $hari_ini = new DateTime();
            $date = new DateTime($this->date);
            if ($date < $hari_ini) {
                $fine = 5000;
                $this->fine = $fine;
                echo "<script>alert('Buku berhasil dikembalikan. Anda terkena fine sebanyak $fine');</script>";
            } else {
                echo "<script>alert('Buku berhasil dikembalikan. Tidak ada fine yang dikenakan');</script>";
            }

            $this->isBorrowed = false;
            $this->borrower = "";
            $this->date = "";
        }
    }

}

class ReferenceBook extends Buku
{
    private $isbn;
    private $publisher;

    public function __construct($title, $author, $year, $isbn, $publisher)
    {
        parent::__construct($title, $author, $year);
        $this->isbn = $isbn;
        $this->publisher = $publisher;
    }

    public function getISBN()
    {
        return $this->isbn;
    }

    public function getPublisher()
    {
        return $this->publisher;
    }
}

class Library
{
    private $books = [];

    public function add(ReferenceBook $book)
    {
        $this->books[] = $book;
    }
    public function searchByISBN($isbn)
    {
        foreach ($this->books as $key => $book) {
            if ($book instanceof ReferenceBook && $book->getISBN() === $isbn) {
                return $this->books[$key];
            }
        }
        return false;
    }
    public function delete($isbn)
    {
        foreach ($this->books as $key => $book) {
            if ($book instanceof ReferenceBook && $book->getISBN() === $isbn) {
                unset($this->books[$key]);
                return true;
            }
        }
        return false;
    }
    public function getAllBooks()
    {
        return $this->books;
    }

    public function searchBook($keyword)
    {
        $result = [];

        foreach ($this->books as $book) {
            if (stripos($book->getTitle(), $keyword) !== false || stripos($book->getAuthor(), $keyword) !== false) {
                $result[] = $book;
            }
        }

        return $result;
    }

    public function sortBook($criteria)
    {
        $sortedBooks = $this->books;

        usort($sortedBooks, function ($a, $b) use ($criteria) {
            if ($criteria === 'author') {
                return strcmp($a->getAuthor(), $b->getAuthor());
            } elseif ($criteria === 'year') {
                return $a->getYear() - $b->getYear();
            }
            return 0;
        });

        return $sortedBooks;
    }
    public function checkUserLimit($borrower)
    {
        $borrowerCounter = 0;

        foreach ($this->books as $book) {
            if ($book->isBorrowed()) {
                $borrowerBook = $book->getBorrower();
                if ($borrowerBook === $borrower) {
                    $borrowerCounter++;
                }
            }
        }

        if ($borrowerCounter >= 3) {
            echo "<script>alert('Anda telah mencapai batas peminjaman buku');</script>";
            return false;
        }
        return true;
    }

    public function saveSession()
    {
        $_SESSION['perpustakaan'] = $this;
    }
}