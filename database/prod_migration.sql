ALTER TABLE powas_settings
ADD COLUMN atp_number VARCHAR(255) NULL,
ADD COLUMN atp_date_issued DATE NULL,
ADD COLUMN atp_valid_until DATE NULL,
ADD COLUMN printer_name VARCHAR(255) NULL,
ADD COLUMN printer_address VARCHAR(255) NULL,
ADD COLUMN printer_tin VARCHAR(255) NULL,
ADD COLUMN printer_accreditation_no VARCHAR(255) NULL,
ADD COLUMN printer_accreditation_date DATE NULL,
ADD COLUMN serial_number_start VARCHAR(255) NULL,
ADD COLUMN serial_number_end VARCHAR(255) NULL,
ADD COLUMN current_serial_number VARCHAR(255) NULL;

ALTER TABLE transactions
ADD COLUMN or_number VARCHAR(255) NULL AFTER description;
