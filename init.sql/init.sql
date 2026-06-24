CREATE TABLE IF NOT EXISTS error_logs (
    id SERIAL PRIMARY KEY,
    module VARCHAR(100) NOT NULL,
    found_date DATE NOT NULL,
    cause TEXT,
    symptoms TEXT,
    error_message TEXT,
    solution TEXT,
    video_link VARCHAR(255),
    image_path VARCHAR(255),
    status VARCHAR(20) DEFAULT 'Pending',
    reported_by VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);