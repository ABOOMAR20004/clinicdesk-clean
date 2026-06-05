CREATE DATABASE IF NOT EXISTS clinicdesk_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE clinicdesk_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS specializations;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM("admin","doctor","patient") NOT NULL DEFAULT "patient",
  phone VARCHAR(20) DEFAULT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (name, email, password, role) VALUES
("Admin", "admin@clinic.local", "$2y$12$zBmsj9RDlnzQ9o2njmNiguG0e6sdvetpkCSqiwmK/w5uPSmNyBDB.", "admin");

CREATE TABLE specializations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO specializations (name) VALUES
("General Practice"),
("Cardiology"),
("Dermatology"),
("Pediatrics"),
("Orthopedics"),
("Neurology"),
("Ophthalmology"),
("ENT"),
("Psychiatry");

CREATE TABLE doctors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL UNIQUE,
  specialization_id INT UNSIGNED NOT NULL,
  bio TEXT DEFAULT NULL,
  consultation_fee DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  available_days VARCHAR(50) NOT NULL DEFAULT "Sun,Mon,Tue,Wed,Thu",
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (specialization_id) REFERENCES specializations(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE appointments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  patient_id INT UNSIGNED NOT NULL,
  doctor_id INT UNSIGNED NOT NULL,
  appt_date DATE NOT NULL,
  appt_time TIME NOT NULL,
  status ENUM("pending","confirmed","completed","cancelled") NOT NULL DEFAULT "pending",
  reason VARCHAR(255) DEFAULT NULL,
  doctor_notes TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY no_double_booking (doctor_id, appt_date, appt_time),
  FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prescriptions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT UNSIGNED NOT NULL UNIQUE,
  diagnosis TEXT NOT NULL,
  medications TEXT NOT NULL,
  notes TEXT DEFAULT NULL,
  file_path VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (name, email, password, role, phone) VALUES
("Dr. Lina Saleh", "lina@clinic.local", "$2y$12$TFYgIOQ9eY/Cbmf/m5D3T.XGDfDWyEfBBk/eUO3ij9Do3HpPCcjxC", "doctor", "0599000001"),
("Dr. Omar Nasser", "omar@clinic.local", "$2y$12$TFYgIOQ9eY/Cbmf/m5D3T.XGDfDWyEfBBk/eUO3ij9Do3HpPCcjxC", "doctor", "0599000002"),
("Maya Khalil", "maya@clinic.local", "$2y$12$WhhlySD2Hq7DZmAD4WClWOIbsyh1ENhT1m6qxnKoIWUMD2Q8RNtn.", "patient", "0599111111"),
("Yousef Haddad", "yousef@clinic.local", "$2y$12$WhhlySD2Hq7DZmAD4WClWOIbsyh1ENhT1m6qxnKoIWUMD2Q8RNtn.", "patient", "0599222222");

INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days)
SELECT u.id, s.id, "Family medicine doctor focused on preventive care.", 75.00, "Sun,Mon,Tue,Wed,Thu"
FROM users u, specializations s
WHERE u.email = "lina@clinic.local" AND s.name = "General Practice";

INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days)
SELECT u.id, s.id, "Cardiology consultant for follow-ups and heart health screening.", 120.00, "Mon,Wed,Thu,Sat"
FROM users u, specializations s
WHERE u.email = "omar@clinic.local" AND s.name = "Cardiology";

INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, status, reason, doctor_notes)
SELECT p.id, d.id, CURDATE(), "09:00:00", "confirmed", "Routine checkup", "Bring previous lab results."
FROM users p, doctors d, users du
WHERE p.email = "maya@clinic.local" AND d.user_id = du.id AND du.email = "lina@clinic.local";

INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, status, reason)
SELECT p.id, d.id, DATE_ADD(CURDATE(), INTERVAL 2 DAY), "10:30:00", "pending", "Follow-up appointment"
FROM users p, doctors d, users du
WHERE p.email = "yousef@clinic.local" AND d.user_id = du.id AND du.email = "omar@clinic.local";

INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, status, reason, doctor_notes)
SELECT p.id, d.id, DATE_SUB(CURDATE(), INTERVAL 5 DAY), "11:00:00", "completed", "Chest discomfort", "No acute findings."
FROM users p, doctors d, users du
WHERE p.email = "maya@clinic.local" AND d.user_id = du.id AND du.email = "omar@clinic.local";

INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes)
SELECT a.id, "Mild gastritis", "Omeprazole 20mg once daily for 14 days", "Return if symptoms persist."
FROM appointments a
INNER JOIN users p ON p.id = a.patient_id
WHERE p.email = "maya@clinic.local" AND a.status = "completed"
LIMIT 1;
