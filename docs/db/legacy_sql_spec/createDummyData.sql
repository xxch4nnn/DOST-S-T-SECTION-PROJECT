INSERT INTO scholarships(name) VALUES ("DOST-SEI Undergraduate Scholarship"),("DOST-SEI Junior Level Science Scholarship");

INSERT INTO scholarship_types(name) VALUES ("Merit"),("RA 7687"),("RA 10612");

INSERT INTO schools(name,campus) VALUES ("University of Southeastern Philippines","Main Campus"),("University of Southeastern Philippines","Mintal Campus"),("University of Southeastern Philippines","Tagum-Main Campus"),("University of Southeastern Philippines","Tagum-Damosa Campus");

INSERT INTO courses(name, abbreviation) VALUES 
    ("Bachelor of Science in Computer Science, Major in Data Science", "BSCS-DS"),
    ("Bachelor of Science in Information Technology, Major in Inforamtion Security", "BSIT-IS"),
    ("Bachelor of Science in Information Technology, Major in Business Technology Management", "BSIT-BTM"),
    ("Bachelor of Library in Inforamtion Science", "BLIS");

INSERT INTO regions(name, abbreviation) VALUES
    ("National Capital Region", "NCR"),
    ("Cordillera Administrative Region", "CAR"),
    ("Ilocos Region", "RI"),
    ("Cagayan Valley", "RII"),
    ("Central Luzon", "RIII"),
    ("CALABARZON", "RIV-A"),
    ("MIMAROPA", "MIMAROPA"),
    ("Bicol Region", "RV"),
    ("Western Visayas", "RVI"),
    ("Central Visayas", "RVII"),
    ("Eastern Visayas", "RVIII"),
    ("Zamboanga Peninsula", "RIX"),
    ("Northern Mindanao", "RX"),
    ("Davao Region", "RXI"),
    ("SOCCSKSARGEN", "RXII"),
    ("Caraga", "RXIII"),
    ("Bangsamoro Autonomous Region in Muslim Mindanao", "BARMM");

INSERT INTO clearance_statuses(name) VALUES ("Not Complete"), ("Complete");

INSERT INTO file_types(name) VALUES 
    ("Scholarship Agreement"),
    ("Amendatory Agreement"),
    ("Information Sheet"),
    ("Prospectus"),
    ("Certificate of Registration (COR)"),
    ("Certficate of Grades (COG)"),
    ("PTP Documents"),
    ("Transcript of Records"),
    ("Graduate Data File"),
    ("Leave of Absence (LOA)"),
    ("Shifting or Transfer"),
    ("Clearance");

-- INSERT INTO scholars (

-- ) VALUES
-- (

-- )