-- NEXERA Seed Data
-- Load this after running schema.sql to populate sample records.

-- NEXERA Seed Data
-- Load this after running schema.sql to populate sample records.

USE nexera_db;
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM audit_logs;
DELETE FROM tasks;
DELETE FROM documents;
DELETE FROM placements;
DELETE FROM leaves;
DELETE FROM internals;
DELETE FROM attendance;
DELETE FROM fees;
DELETE FROM staff;
DELETE FROM parents;
DELETE FROM students;
DELETE FROM users;

SET FOREIGN_KEY_CHECKS = 1;

-- (keep all your INSERT statements here unchanged)


INSERT INTO users (id, username, password_hash, role, status, created_at)
VALUES
(1, 'admin.nexera', '$2y$10$3ESS0xChcuDJuUEzVnrVi.BaS2HRL6p2rhwLiUXaKsUjW4ZrIWwBe', 'admin', 'active', NOW()),
(2, 'teach.hero', '$2y$10$dgAblnX1k0QfzYqfQ9alC.h9ZvYai/82No3jhrY.I9wTKoRsESSCO', 'staff', 'active', NOW()),
(3, 'ops.manager', '$2y$10$GnIstPnqctxmICnYBCIi9ujCoUgzDaj/RS9E520ah4t8HeNsm8r5.', 'staff', 'active', NOW()),
(4, 'STU001', '$2y$10$9s1No3Yw3haXZ9/2C4xfEuLQ8QofrrtsODRySVPmoQpEc3GSkyWNe', 'student', 'force_reset', NOW()),
(5, 'par.stu001', '$2y$10$JdqJ2QL1FihZfIOpVHYVruQtCB/2.X63oKMFIr7rOHhHaUhzLct8q', 'parent', 'active', NOW());

INSERT INTO staff (user_id, name, position, teaching, qualifications, schedule_json)
VALUES
(2, 'Dr. A. Kavya', 'Associate Professor - CSE', 1, 'Ph.D. in Computer Science', JSON_OBJECT('Monday', 'CSE401 - 10:30', 'Tuesday', 'CSE305 - 09:30')),
(3, 'Ms. Priya Operations', 'Student Affairs Executive', 0, 'MBA - HR', JSON_OBJECT('Desk', 'Admissions counter', 'Support', '09:00-17:00'));

INSERT INTO parents (user_id, parent_of, dob, father_name, mother_name, phone, address)
VALUES
(5, 'Parent of Ananya Kumar', '1978-08-10', 'Mr. Senthil Kumar', 'Mrs. Kavitha Senthil', '+91-90000-11111', '12, Green Meadows, Coimbatore');

INSERT INTO students (user_id, roll_number, first_name, last_name, dob, address, phone, father_name, mother_name, parent_user_id, hosteller, course, branch, year_of_study)
VALUES
(4, 'STU001', 'Ananya', 'Kumar', '2004-06-15', 'Hostel Block B, Room 203', '+91-90000-22222', 'Mr. Senthil Kumar', 'Mrs. Kavitha Senthil', 5, 'hosteller', 'B.Tech', 'Computer Science', 'III');

UPDATE parents SET linked_student_id = (SELECT id FROM students WHERE roll_number = 'STU001') WHERE user_id = 5;

INSERT INTO attendance (student_id, date, status, remarks)
VALUES
((SELECT id FROM students WHERE roll_number = 'STU001'), CURDATE() - INTERVAL 3 DAY, 'present', 'On time'),
((SELECT id FROM students WHERE roll_number = 'STU001'), CURDATE() - INTERVAL 2 DAY, 'present', 'Participated in lab'),
((SELECT id FROM students WHERE roll_number = 'STU001'), CURDATE() - INTERVAL 1 DAY, 'on_duty', 'Placement training'),
((SELECT id FROM students WHERE roll_number = 'STU001'), CURDATE(), 'present', 'Excellent collaboration');

INSERT INTO internals (student_id, subject, marks, max_marks, term, feedback)
VALUES
((SELECT id FROM students WHERE roll_number = 'STU001'), 'Data Structures', 42, 50, 'Midterm', 'Shows strong problem solving'),
((SELECT id FROM students WHERE roll_number = 'STU001'), 'Database Systems', 44, 50, 'Midterm', 'Keep up the momentum');

INSERT INTO leaves (student_id, from_date, to_date, reason, status, approved_by)
VALUES
((SELECT id FROM students WHERE roll_number = 'STU001'), CURDATE() + INTERVAL 2 DAY, CURDATE() + INTERVAL 3 DAY, 'Family function', 'pending', NULL);

INSERT INTO placements (student_id, company, position, status, date_applied, notes)
VALUES
((SELECT id FROM students WHERE roll_number = 'STU001'), 'TechNova Labs', 'Full Stack Intern', 'applied', CURDATE() - INTERVAL 5 DAY, 'Aptitude round cleared');

INSERT INTO documents (uploader_user_id, title, filename, file_path, visibility_role)
VALUES
(2, 'Data Structures Lab Manual', 'ds-lab-manual.pdf', 'uploads/ds-lab-manual.pdf', 'student'),
(2, 'Parent Handbook 2025', 'parent-handbook.pdf', 'uploads/parent-handbook.pdf', 'parent');

INSERT INTO tasks (assigned_by, assigned_to_user_id, title, description, due_date, status)
VALUES
(2, 4, 'Resume Enhancement', 'Update resume with internship highlights', CURDATE() + INTERVAL 7 DAY, 'assigned'),
(2, 4, 'Mock Interview Prep', 'Complete mock interview checklist shared in class.', CURDATE() + INTERVAL 10 DAY, 'assigned');

INSERT INTO fees (student_id, amount_due, amount_paid, due_date, payment_method, status)
VALUES
((SELECT id FROM students WHERE roll_number = 'STU001'), 75000.00, 35000.00, CURDATE() + INTERVAL 15 DAY, 'Online Banking', 'partial');


