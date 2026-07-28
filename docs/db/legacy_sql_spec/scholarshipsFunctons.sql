-- Functions to implement:
-- scholarExists(name)
-- createScholarshipByName(name)
-- createScholarshipById(id INT)
-- updateScholarship(id INT, name VARCHAR(100), is_available BOOLEAN)
-- retrieveScholarship()
-- retrieveScholarshipById(id)
-- retrieveScholarshipByName(name)
-- deleteScholarship(id)
-- getScholarshipCount(id)

DELIMITER $$

CREATE FUNCTION scholarExistsByName(name VARCHAR(100))
RETURNS TINYINT(1)
DETERMINISTIC
BEGIN
    IF EXISTS(SELECT 1 FROM scholarships WHERE name = name) THEN
        RETURN 1;
    END IF;
    RETURN 0;
END$$

CREATE FUNCTION ScholarExistsById(id INT)
RETURNS TINYINT(1)
DETERMINISTIC
BEGIN
    IF EXISTS(SELECT 1 FROM scholarships WHERE id = id) THEN
        RETURN 1;
    END IF;
    RETURN 0;
END$$

CREATE FUNCTION CreateScholarship(name VARCHAR(100))
RETURNS TINYINT(1)
DETERMINISTIC
BEGIN$$
    IF scholarExistsByName(name) THEN
        RETURN 0;
    END IF;

    INSERT INTO scholarships(name) VALUES(name);
    RETURN 1;
END$$

CREATE FUNCTION UpdateScholarship(id INT, name VARCHAR(100), is_available BOOLEAN)
RETURNS TINYINT(1)
DETERMINISTIC
BEGIN$$
    IF scholarExists(name) THEN
        RETURN 0;
    END IF;

    INSERT INTO scholarships(name) VALUES(name);
    RETURN 1;
END$$

DELIMETER ;