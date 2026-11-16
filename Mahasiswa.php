<?php

class Mahasiswa
{
    // Property menggunakan private
    private $nama;
    private $nim;
    private $jurusan;

    // Constructor
    public function __construct($nama, $nim, $jurusan)
    {
        $this->nama = $nama;
        $this->nim = $nim;
        $this->jurusan = $jurusan;
    }

    // Getter: mengambil data
    public function getNama()
    {
        return $this->nama;
    }

    public function getNim()
    {
        return $this->nim;
    }

    public function getJurusan()
    {
        return $this->jurusan;
    }

    // Setter: mengubah data
    public function setNama($nama)
    {
        $this->nama = $nama;
    }

    public function setJurusan($jurusan)
    {
        $this->jurusan = $jurusan;
    }

    // Menampilkan data mahasiswa
    public function tampilkanMahasiswa()
    {
        echo "Nama: " . $this->nama . "<br>";
        echo "NIM: " . $this->nim . "<br>";
        echo "Jurusan: " . $this->jurusan . "<br>";
    }
}
