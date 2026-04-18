REATE TABLE dashboard_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL UNIQUE,
    project_filter VARCHAR(50) DEFAULT NULL,
    cr_sort_field VARCHAR(20) DEFAULT 'DATE',
    show_notifications TINYINT(1) DEFAULT 1,
    layout_mode VARCHAR(10) DEFAULT 'grid',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);
