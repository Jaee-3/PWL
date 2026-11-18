USE student_portal;

INSERT INTO
    program_studi (kd_prodi, nama_prodi)
VALUES ('42', 'Sistem Informasi'),
    ('41', 'Informatika'),
    ('21', 'Teknik Elektro'),
    ('22', 'Teknik Industri')
ON DUPLICATE KEY UPDATE
    nama_prodi = VALUES(nama_prodi);