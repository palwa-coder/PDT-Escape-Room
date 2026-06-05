USE db_escape_room;

SET GLOBAL innodb_lock_wait_timeout = 5;
SET SESSION innodb_lock_wait_timeout = 5;

CREATE VIEW view_all_bookings AS 
SELECT id, user_id, theme, booking_time, package, booking_code, created_at FROM bookings_fragment_1 
UNION ALL 
SELECT id, user_id, theme, booking_time, package, booking_code, created_at FROM bookings_fragment_2;

CREATE VIEW view_booking_details AS 
SELECT b.id, u.username, b.theme, b.booking_time, b.package, b.booking_code, b.created_at 
FROM view_all_bookings b 
INNER JOIN users u ON b.user_id = u.id;

DELIMITER $$
CREATE FUNCTION generate_random_code()
RETURNS VARCHAR(8)
DETERMINISTIC
BEGIN
    DECLARE chars_str VARCHAR(62);
    DECLARE res_str VARCHAR(8);
    DECLARE i INT;
    SET chars_str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    SET res_str = '';
    SET i = 1;
    WHILE i <= 8 DO
        SET res_str = CONCAT(res_str, SUBSTRING(chars_str, FLOOR(1 + RAND() * 62), 1));
        SET i = i + 1;
    END WHILE;
    RETURN res_str;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER after_fragment1_insert
AFTER INSERT ON bookings_fragment_1
FOR EACH ROW
BEGIN
    INSERT INTO booking_audit (booking_code, action_type) VALUES (NEW.booking_code, 'INSERT_FRAGMENT_1');
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER after_fragment2_insert
AFTER INSERT ON bookings_fragment_2
FOR EACH ROW
BEGIN
    INSERT INTO booking_audit (booking_code, action_type) VALUES (NEW.booking_code, 'INSERT_FRAGMENT_2');
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE process_secure_booking(IN p_user_id INT, IN p_theme VARCHAR(50), IN p_time TIME, IN p_package VARCHAR(50), OUT p_generated_code VARCHAR(8))
BEGIN
    DECLARE v_code VARCHAR(8);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    
    SET v_code = generate_random_code();
    SET p_generated_code = v_code;
    
    START TRANSACTION;
    
    IF p_theme IN ('aquatic', 'space', 'cyberpunk', 'steampunk') THEN
        SELECT id FROM bookings_fragment_1 WHERE theme = p_theme AND booking_time = p_time FOR UPDATE;
        INSERT INTO bookings_fragment_1 (user_id, theme, booking_time, package, booking_code) VALUES (p_user_id, p_theme, p_time, p_package, v_code);
    ELSE
        SELECT id FROM bookings_fragment_2 WHERE theme = p_theme AND booking_time = p_time FOR UPDATE;
        INSERT INTO bookings_fragment_2 (user_id, theme, booking_time, package, booking_code) VALUES (p_user_id, p_theme, p_time, p_package, v_code);
    END IF;
    
    COMMIT;
END$$
DELIMITER ;