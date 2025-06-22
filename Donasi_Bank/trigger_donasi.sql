-- Trigger: Setelah data donasi baru ditambahkan, otomatis tambahkan ke riwayat_donasi
DELIMITER $$
CREATE TRIGGER after_insert_donasi
AFTER INSERT ON donasi
FOR EACH ROW
BEGIN
    INSERT INTO riwayat_donasi (id_donasi, status, waktu_update)
    VALUES (NEW.id_donasi, 'pending', NOW());
END$$
DELIMITER ;

-- (Opsional) Jika ingin trigger lain, silakan informasikan.
