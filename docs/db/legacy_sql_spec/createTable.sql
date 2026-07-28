-- RA NO. 10612, RA NO. 7687, and Merit awardees
CREATE TABLE IF NOT EXISTS scholarship_programs (
	id INT AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
	is_available BOOLEAN DEFAULT(1) NOT NULL,

	PRIMARY KEY (id)
);

-- Undergraduate (4 to 5 years) or JLSS (2 to 3 years)
CREATE TABLE IF NOT EXISTS scholarship_program_types (
	id INT AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
	is_available BOOLEAN DEFAULT(1) NOT NULL,

	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS schools (
	id INT AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL,
	campus VARCHAR(100),
	requirement_deadline DATETIME, 
	is_available BOOLEAN DEFAULT(1) NOT NULL,

	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS courses (
	id INT AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
	abbreviation VARCHAR(100) NOT NULL UNIQUE,
	is_available BOOLEAN DEFAULT(1) NOT NULL,
	
	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS regions (
	id INT AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
	abbreviation VARCHAR(100) NOT NULL UNIQUE,
	is_available BOOLEAN DEFAULT(1) NOT NULL,
	
	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS clearance_statuses (
	id INT AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
	is_available BOOLEAN DEFAULT(1) NOT NULL,

	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS file_groups(
	id INT AUTO_INCREMENT,
	name VARCHAR(100) UNIQUE,

	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS file_types(
	id INT AUTO_INCREMENT,
	file_group_id INT,
	name VARCHAR(100) NOT NULL UNIQUE,
	metadata JSON,

	PRIMARY KEY (id),
	FOREIGN KEY (file_group_id) REFERENCES file_group(id)
);

CREATE TABLE IF NOT EXISTS scholars (
   	id INT AUTO_INCREMENT, 
   	first_name VARCHAR(50) NOT NULL,
	middle_name VARCHAR(50),
	last_name VARCHAR(50) NOT NULL,
	generational_suffix VARCHAR(5),
	year_of_award INT NOT NULL,
	scholarship_id INT NOT NULL,
	scholarship_type_id INT NOT NULL,
	spas_no VARCHAR(50),
	sex VARCHAR(6),
	birthdate DATE,
	contact_number VARCHAR(11),
	email_address VARCHAR(70) UNIQUE,
	school_id INT NOT NULL,
	course_id INT,
	program VARCHAR(150),
	barangay VARCHAR(150),
	municipality VARCHAR(150),
	district VARCHAR(150),
	province VARCHAR(150),
	region_id INT NOT NULL,
	clearance_status_id INT DEFAULT(0) NOT NULL,
	clearance_date DATE,

	PRIMARY KEY (id),
	FOREIGN KEY (scholarship_id) REFERENCES scholarships(id),
	FOREIGN KEY (scholarship_type_id) REFERENCES scholarship_types(id),
	FOREIGN KEY (school_id) REFERENCES schools(id),
	FOREIGN KEY (course_id) REFERENCES courses(id),
	FOREIGN KEY (region_id) REFERENCES regions(id),
	FOREIGN KEY (clearance_status_id) REFERENCES clearance_statuses(id)
);

CREATE TABLE IF NOT EXISTS files (
 	id INT AUTO_INCREMENT,
	file_type_id INT NOT NULL,

	-- Note that filepaths follows the specific format: Batch_Name_FileType_Additional Details
	file_name VARCHAR(255) NOT NULL,
	file_path VARCHAR(255) NOT NULL,
	file_size_kb INT,

	uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Deletion causes the file path to be lost but the record to be kept?

	mime_type VARCHAR(50) DEFAULT 'application/pdf',
	metadata JSON,

	PRIMARY KEY (id),
	FOREIGN KEY (file_type_id) REFERENCES file_types(id)
);


CREATE TABLE IF NOT EXISTS audit_logs (
 	id INT AUTO_INCREMENT,
 	user_id INT NOT NULL,
	parent_log_id INT DEFAULT NULL,
 	action_message VARCHAR(200) NOT NULL,    	-- upload, overwrite, strike_off undo_strike_off, login, hard_delete (super admin only), etc.
	file_id INT,  -- Nulls is possible in case the user decides to create a placeholder. (This is heavily program-dependent.)
 	before_payload JSON NULL,
 	after_payload JSON NULL,
 	ip_address VARCHAR(45) NOT NULL,
 	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

 	PRIMARY KEY (id),
 	FOREIGN KEY (user_id) REFERENCES users(id)
 	FOREIGN KEY (parent_log_id) REFERENCES audit_logs(id)
);