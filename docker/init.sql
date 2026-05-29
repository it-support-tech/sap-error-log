CREATE TABLE IF NOT EXISTS employees (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS modules (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name_lo VARCHAR(200) NOT NULL,
    name_en VARCHAR(200) NOT NULL,
    icon VARCHAR(50) DEFAULT 'cube',
    color VARCHAR(20) DEFAULT '#3B82F6',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS error_logs (
    id SERIAL PRIMARY KEY,
    module_id INTEGER NOT NULL REFERENCES modules(id) ON DELETE CASCADE,
    employee_id INTEGER NOT NULL REFERENCES employees(id) ON DELETE CASCADE,
    occurred_at DATE NOT NULL DEFAULT CURRENT_DATE,
    error_message TEXT NOT NULL,
    symptom TEXT NOT NULL,
    cause TEXT,
    solution TEXT,
    video_url VARCHAR(500),
    image_path VARCHAR(500),
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'resolved')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_error_logs_module ON error_logs(module_id);
CREATE INDEX idx_error_logs_employee ON error_logs(employee_id);
CREATE INDEX idx_error_logs_status ON error_logs(status);
CREATE INDEX idx_error_logs_occurred ON error_logs(occurred_at DESC);

CREATE OR REPLACE FUNCTION update_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_error_logs_updated_at
    BEFORE UPDATE ON error_logs
    FOR EACH ROW EXECUTE FUNCTION update_updated_at();

INSERT INTO modules (code, name_lo, name_en, icon, color) VALUES
('administration', 'ການຈັດການລະບົບ', 'Administration', 'cog', '#6366F1'),
('financials', 'ການເງິນແລະບັນຊີ', 'Financials', 'chart-bar', '#10B981'),
('sales', 'ການຂາຍ', 'Sales - A/R', 'shopping-cart', '#F59E0B'),
('purchasing', 'ການຈັດຊື້', 'Purchasing - A/P', 'truck', '#EF4444'),
('inventory', 'ການຈັດການສິນຄ້າຄົງຄັງ', 'Inventory', 'archive', '#8B5CF6'),
('business-partners', 'ຄູ່ຮ່ວມທຸລະກິດ', 'Business Partners', 'users', '#06B6D4'),
('banking', 'ການທະນາຄານ', 'Banking', 'credit-card', '#84CC16'),
('Fixed Asset', 'ຕັ້ງຊັບສິນ', 'Fixed Asset', 'server', '#F97316')
ON CONFLICT (code) DO NOTHING;

INSERT INTO employees (name) VALUES
('ທົດສອບ ລະບົບ'),
('Admin')
ON CONFLICT (name) DO NOTHING;
